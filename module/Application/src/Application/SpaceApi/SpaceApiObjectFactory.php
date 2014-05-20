<?php

namespace Application\SpaceApi;
use Application\Utils\Utils;
use SpaceApi\Validator\Validator;

/**
 * Class SpaceApiObjectFactory
 */
class SpaceApiObjectFactory
{
    const FROM_FILE = 'file';
    const FROM_JSON = 'json';
    const FROM_NAME = 'name';

    /**
     * Creates a SpaceApiObject instance with a default validator already set.
     * @param $data
     * @param $loadFrom
     * @return SpaceApiObject|null
     */
    public static function create($data, $loadFrom) {

        $spaceApiObject = null;

        switch ($loadFrom) {
            case SpaceApiObjectFactory::FROM_JSON:

                $spaceApiObject = static::fromJson($data);
                break;

            case SpaceApiObjectFactory::FROM_FILE:

                $spaceApiObject = static::fromFile($data);
                break;

            case SpaceApiObjectFactory::FROM_NAME:

                $spaceApiObject = static::fromName($data);
                break;
        }

        $spaceApiObject->setValidator(new Validator());
        return $spaceApiObject;
    }

    /**
     * Creates a SpaceApiObject from a slug or the original space name.
     *
     * @param string $name slug or the original space name
     * @return SpaceApiObject
     * @throws \FilesystemException if file with the given name is not found
     */
    public static function fromName($name) {

        $slug = '';

        // normalize the name for all other endpoint than the test endpoint
        if ($name !== '.test-endpoint') {
            $slug = Utils::normalize($name);
        }

        $spaceApiObject = static::fromFile("public/space/$name/spaceapi.json", $slug);
        return $spaceApiObject;
    }

    public static function fromFile($filepath, $slug = '') {
        return SpaceApiObject::fromFile($filepath, $slug);
    }

    public static function fromJson($json) {
        return SpaceApiObject::fromJson($json);
    }
}
