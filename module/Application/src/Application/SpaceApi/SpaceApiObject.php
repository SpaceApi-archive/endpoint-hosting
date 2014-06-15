<?php

namespace Application\SpaceApi;
use Application\Exception\FilesystemException;
use JsonSchema\Exception\JsonDecodingException;
use SpaceApi\Validator\ResultInterface;
use SpaceApi\Validator\ValidatorInterface;
use Zend\Json\Exception\RuntimeException;
use Zend\Json\Json;

/**
 * Class SpaceApiObject
 * @package Application\SpaceApiObject
 *
 * @property-read string name Hackerspace name
 * @property-read int version Specification version number
 * @property-read int gist Gist ID, a value of 0 means that it's not set.
 * @property-read string json
 * @property-read object object
 * @property-read string slug normalized hackerspace name
 * @property-read string loaded_from value 'file', 'json' or 'name' which defines from what source the instace got created
 * @property-read ResultInterface|null validation validation result re-initialized on each update request, null if the json is not parsable
 * @property-read ValidatorInterface|null validator
 * @property-read boolean validJson flag which says that that $json is parsable. This flag is not meant to be a validation result flag nor if the JSON was an empty string. To check if the JSON was empty simply do a empty() check on the json property or a is_null() check on the object property.
 */
// @todo move this class to a library repo which will be shared with other modules/projects
class SpaceApiObject
{
    const FROM_FILE = 'file';
    const FROM_JSON = 'json';

    protected $name = '';
    protected $version = 0;
    protected $gist = 0;
    protected $json = '';
    protected $object = null;
    protected $slug = '';
    protected $validation = null;
    protected $validJson = true;
    protected $loaded_from = '';
    protected $validator = null;

    // these should not be exposed to the code assistant, as it's not
    // accessible outside this class or a subclass.
    protected $file;

    // properties that should not be accessed by the magic getter
    protected $private_properties = array(
        'private_properties',
        'file'
    );

    /**
     * Creates a new SpaceApiObject instance. The constructor is not
     * intended for public access. Instead there are three factory
     * methods, {@see Application\SpaceApi\SpaceApiObject::fromName()},
     * {@see Application\SpaceApi\SpaceApiObject::fromFile()} and
     * {@see Application\SpaceApi\SpaceApiObject::fromJson()}.
     *
     * If an invalid JSON is passed as an argument it will be stored in
     * {@see Application\SpaceApi\SpaceApiObject::json} and
     * {@see Application\SpaceApi\SpaceApiObject::json} set to false.
     *
     * @param string $json
     */
    protected function __construct($json)
    {
        $this->json = $json;

        try {
            // update() sets validJson to false if it fails but we need
            // to catch all the exceptions to not propagate them to
            // higher application levels where 'validJson' and 'validation'
            // are used to check whether there's something wrong with
            // the endpoint data
            $this->update($json);
        } catch (\BadMethodCallException $badMethodCallException) {
        } catch (RuntimeException $decodingException) {
        }
    }

    /**
     * Magic getter to access the protected properties. Private properties
     * are skipped.
     *
     * @param $property
     * @return mixed
     * @throws \Exception
     */
    public function __get($property)
    {
        if (! in_array($property, $this->private_properties) && property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new \Exception('Bad method call or attribute access!');
        }
    }

    /**
     * Check if a private/protected member exists. This magic function
     * must be implemented in to access the properties from within the
     * template.
     *
     * @param mixed $member Property
     * @return bool
     */
    public function __isset($member)
    {
        return property_exists($this, $member);
    }

    /**
     * Updates {@see Application\SpaceApi\SpaceApiObject::json} and the
     * its object representation {@see Application\SpaceApi\SpaceApiObject::object}.
     * If the JSON could not be decoded {@see Application\SpaceApi\SpaceApiObject::object}
     * will be set to null but {@see Application\SpaceApi\SpaceApiObject::json}
     * will be updated. Though the {@see Application\SpaceApi\SpaceApiObject::validJson}
     * flag will be set to false which prevents the json property being
     * written to the filesystem with a subsequent {@see Application\SpaceApi\SpaceApiObject::save()} call.
     *
     * @param string $json
     * @return SpaceApiObject
     * @throws \BadMethodCallException if input is not a string
     * @throws RuntimeException if the JSON could not be decoded
     */
    public function update($json)
    {
        $this->json = $json;

        //////////////////////////////////////////////////////////////
        // always set $this->json before the try-catch block

        if (! is_string($json)) {
            throw new \BadMethodCallException('Input not a string');
        }

        $this->object = null;

        try {
            // @frameowork zend2
            $this->object = Json::decode($json);
        } catch(RuntimeException $e) {
            $this->validation = null;
            $this->validJson = false;
            throw $e;
        }
        //////////////////////////////////////////////////////////////

        $this->validJson = true;

        // empty strings are valid JSON but since $object is null in
        // this case we simply return here
        if (is_null($this->object)) {
            return $this;
        }

        $this->setName($this->object);
        $this->setVersion($this->object);

        // set the gist ID if it's yet uninitialized or write ours back
        // to $object.
        if ($this->gist === 0) {
            $this->setGist($this->object);
        } else {
            $this->object->ext_gist = $this->gist;
        }

        $this->json = json_encode(
            $this->object,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $this->validate();
        return $this;
    }

    /**
     * Sets a validator and validates the JSON.
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator) {
        $this->validator = $validator;
        $this->validate();
    }

    /**
     * Saves a SpaceApiObject instance to the file. This method does nothing
     * if the object was created from fromJson() or if the json property
     * contains no valid JSON.
     */
    public function save()
    {
        if ($this->file && $this->validJson) {
            file_put_contents($this->file, $this->json);
        }
    }

    /**
     * Validates the spaceapi json. This must never be called directly
     * by a consumer and is intended to be used by SpaceApiObject::update()
     * or SpaceApiObject::setValidator() only. This method doesn nothing
     * if the json property doesn't contain a valid JSON. If the validator
     * is set and the 'json' property an empty string, the validator will be
     * triggered.
     */
    private function validate()
    {
        if (! is_null($this->validator) && $this->validJson) {
            $this->validation = $this->validator->validateStableVersion($this->json);
        }
    }

    /**
     * Returns the validation results if a validator was set and a validation
     * performed. Otherwise null is returned.
     *
     * @return null|ResultInterface
     */
    public function getValidation() {
        return $this->validation;
    }

    /****************************************************************/
    // setters, not public in favor of update() to go through validation chain

    /**
     * Sets the name property taken from the argument's space property.
     *
     * @param \stdClass $object deserialized json of the SpaceApiObject wrapper
     */
    protected function setName(\stdClass $object)
    {
        if (property_exists($object, 'space')) {
            $this->name = $object->space;
        }
    }

    /**
     * Sets the version property taken from the argument's version property.
     *
     * @param \stdClass $object deserialized json of the SpaceApiObject wrapper
     */
    protected function setVersion(\stdClass $object)
    {
        if (property_exists($object, 'version')) {
            $this->version = (int) $object->version;
        }
    }

    /**
     * Sets the gist property if the gist ID is alphanumeric. Gist IDs
     * are hex numbers but the less strict test is future-safe if Github
     * is changing their ID format.
     *
     * The Gist ID is taken from the the 'ext_gist' property of the passed
     * object.
     *
     * @param \stdClass $object deserialized json of the SpaceApiObject wrapper
     */
    protected function setGist(\stdClass $object)
    {
        if (property_exists($object, 'ext_gist') &&
            is_string($object->ext_gist) &&
            preg_match("/^[a-zA-Z0-9]+$/", $object->ext_gist)) {
            $this->gist = $object->ext_gist;
        }
    }

    /****************************************************************/
    // factory methods

    /**
     * Creates a SpaceApiObject from a file. You can pass a slug if you want
     * to pass a file identifier to other objects in your application
     * to be able to identify/load the file which this SpaceApiObject
     * instance is created from.
     *
     * @param string $file
     * @param string $slug unique file identifier
     * @return SpaceApiObject
     * @throws \Application\Exception\FilesystemException
     */
    public static function fromFile($file, $slug = '')
    {
        if (!file_exists($file)) {
            $error_message = array(
                "File not found: $file",
                "Current directory: ". getcwd()
            );
            throw new FilesystemException(join("\n", $error_message));
        }

        /** @var SpaceApiObject $instance */
        $instance = static::fromJson(file_get_contents($file));
        $instance->file = $file;
        $instance->slug = $slug;

        // overrides fromJson's definition
        $instance->loaded_from = static::FROM_FILE;

        return $instance;
    }

    /**
     * Creates a SpaceApiObject from a JSON.
     *
     * @param $json
     * @return SpaceApiObject
     */
    public static function fromJson($json)
    {
        $instance = new static($json);
        $instance->loaded_from = static::FROM_JSON;
        return $instance;
    }
}
