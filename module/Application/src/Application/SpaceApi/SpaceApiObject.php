<?php

namespace Application\SpaceApi;
use Application\Utils\Utils;

/**
 * Class SpaceApiObject
 * @package Application\SpaceApiObject
 *
 * @property-read string name
 * @property-read string version
 * @property-read int gist
 * @property-read string json
 * @property-read object object
 * @property-read string slug
 */
class SpaceApiObject
{
    protected $name;
    protected $version;
    protected $gist = 0;
    protected $json;
    protected $object = null;
    protected $slug;

    // this should not be exposed to the code assistant, as it's not
    // accessible outside this class or a subclass.
    protected $file;

    // Don't make this public, this class has factory methods to
    // instantiate itself. As long as fromJson() is the only caller
    // it's guaranteed that a valid json is provided.
    protected function __construct($json)
    {
        $this->update($json);
    }

    public function __get($property)
    {
        if (property_exists($this, $property) && $property !== 'file')
            return $this->$property;
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
     * Updates
     * @param $json
     * @throws \Exception
     */
    public function update($json)
    {
        if (!is_string($json)) {
            throw new \Exception('Input not a string');
        }

        $object = json_decode($json);

        if (is_null($object)) {
            throw new \Exception('Invalid JSON');
        }

        $this->setName($object);
        $this->setVersion($object);

        // set the gist ID if it's yet uninitialized or write ours back
        // to $object.
        if ($this->gist === 0) {
            $this->setGist($object);
        } else {
            $object->ext_gist = $this->gist;
        }

        $this->object = $object;
        $this->json = json_encode(
            $object,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Saves this object to the spaceapi file. This method does nothing
     * if the object was created from fromJson().
     */
    public function save()
    {
        if ($this->file) {
            file_put_contents($this->file, $this->json);
        }
    }

    /**
     * Validates the spaceapi json.
     */
    public function validate()
    {
        // TODO: make a request to the spaceapi validator
        return true;
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
            $this->version = $object->version;
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
     * @param string $name slug or the original space name
     * @return SpaceApiObject
     * @throws \Exception
     */
    public static function fromName($name)
    {
        $name = Utils::normalize($name);
        $file_path = "public/space/$name/spaceapi.json";

        // we must guarantee that the
        if (!file_exists($file_path)) {
            throw new \Exception("File not found: $file_path");
        }

        $object = static::fromFile($file_path);
        $object->slug = $name;
        return $object;
    }

    /**
     * Creates a SpaceApiObject from a file.
     *
     * @param $file
     * @return SpaceApiObject
     * @throws \Exception
     */
    public static function fromFile($file)
    {
        if (!file_exists($file)) {
            throw new \Exception("File not found: $file");
        }

        /** @var SpaceApiObject $object */
        $object = static::fromJson(file_get_contents($file));
        $object->file = $file;
        return $object;
    }

    /**
     * Creates a SpaceApiObject from a JSON.
     *
     * @param $json
     * @return SpaceApiObject
     * @throws \Exception
     */
    public static function fromJson($json)
    {
        if (is_null(json_decode($json))) {
            throw new \Exception('Invalid JSON');
        }

        return new static($json);
    }
}
