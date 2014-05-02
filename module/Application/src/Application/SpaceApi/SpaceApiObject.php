<?php

namespace Application\SpaceApi;
use Application\Exception\FilesystemException;
use Application\Utils\Utils;
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
 * @property-read int gist Gist ID
 * @property-read string json
 * @property-read object object
 * @property-read string slug normalized hackerspace name
 * @property-read string loaded_from value 'file', 'json' or 'name' which defines from what source the instace got created
 * @property-read ResultInterface|null validation validation result re-initialized on each update request, null if the json is not parsable
 * @property-read ValidatorInterface validator
 * @property-read boolean validJson flag which says that that $json is parsable. This flag is not meant to be a validation result flag.
 */
class SpaceApiObject
{
    const LOADED_FROM_FILE = 'file';
    const LOADED_FROM_JSON = 'json';
    const LOADED_FROM_NAME = 'name';

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
     * will be set to null and {@see Application\SpaceApi\SpaceApiObject::json}
     * remains unchanged to prevent it being written to the filesystem
     * with a subsequent {@see Application\SpaceApi\SpaceApiObject::save()} call.
     *
     * @param string $json
     * @return SpaceApiObject
     * @throws \BadMethodCallException if input is not a string
     * @throws RuntimeException if the JSON could not be decoded
     */
    public function update($json)
    {
        //////////////////////////////////////////////////////////////
        // never set $this->json before the try-catch block

        if (! is_string($json)) {
            throw new \BadMethodCallException('Input not a string');
        }

        $object = null;

        try {
            $this->object = Json::decode($json);
        } catch(RuntimeException $e) {
            $this->validation = null;
            $this->validJson = false;
            throw $e;
        }
        //////////////////////////////////////////////////////////////

        $this->json = $json;
        $this->validJson = true;

        $this->setName($this->object);
        $this->setVersion($this->object);

        // set the gist ID if it's yet uninitialized or write ours back
        // to $object.
        if ($this->gist === 0) {
            $this->setGist($this->object);
        } else {
            $object->ext_gist = $this->gist;
        }

        $this->json = json_encode(
            $this->object,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $this->validate();
        return $this;
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    /**
     * Saves a SpaceApiObject instance to the file. This method does nothing
     * if the object was created from fromJson().
     */
    public function save()
    {
        if ($this->file) {
            file_put_contents($this->file, $this->json);
        }
    }

    /**
     * Validates the spaceapi json. This must never be called directly
     * by a consumer and is intended to be used by SpaceApiObject::update()
     * only. If the update method can't decode the JSON, validate() isn't
     * called.
     */
    private function validate()
    {
        if (! is_null($this->validator)) {
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

    protected function setName($object)
    {
        if (property_exists($object, 'space')) {
            $this->name = $object->space;
        }
    }

    protected function setVersion($object)
    {
        if (property_exists($object, 'version')) {
            $this->version = (int) $object->version;
        }
    }

    protected function setGist($object)
    {
        if (property_exists($object, 'ext_gist')) {
            $this->gist = (int) $object->ext_gist;
        }
    }

    /****************************************************************/
    // factory methods

    /**
     * Creates a SpaceApiObject from a slug or the original space name.
     *
     * @deprecated This method is specific to the 'endpoint hosting'
     *             application which makes this class not reusable as
     *             a library. On the application level a service should
     *             be defined which retrieves the file path and uses fromFile()
     *             instead
     * @param string $name slug or the original space name
     * @return SpaceApiObject
     * @throws \Exception
     */
    public static function fromName($name)
    {
        // normalize the name for all other endpoint than the test endpoint
        if ($name !== '.test-endpoint') {
            $name = Utils::normalize($name);
        }

        $file_path = "public/space/$name/spaceapi.json";

        // we must guarantee that the
        if (!file_exists($file_path)) {
            throw new \Exception("File not found: $file_path");
        }

        $object = static::fromFile($file_path);
        $object->slug = $name;

        // overrides fromFile's definition
        $object->loaded_from = static::LOADED_FROM_NAME;

        return $object;
    }

    /**
     * Creates a SpaceApiObject from a file.
     *
     * @param $file
     * @return SpaceApiObject
     * @throws \Application\Exception\FilesystemException
     */
    public static function fromFile($file)
    {
        if (!file_exists($file)) {
            throw new FilesystemException("File not found: $file");
        }

        /** @var SpaceApiObject $object */
        $instance = static::fromJson(file_get_contents($file));
        $instance->file = $file;

        // overrides fromJson's definition
        $instance->loaded_from = static::LOADED_FROM_FILE;

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
        $instance->loaded_from = static::LOADED_FROM_JSON;
        return $instance;
    }
}
