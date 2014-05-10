<?php

namespace Application\SpaceApi;
use SpaceApi\Validator\Validator;

/**
 * Class SpaceApiObjectFactory
 */
class SpaceApiObjectFactory
{
    /**
     * Creates a SpaceApiObject instance with a default validator already set.
     * @param $data
     * @param $loadFrom
     * @return SpaceApiObject|null
     */
    public static function create($data, $loadFrom) {

        // @todo get the filepath from the name and use fromFile
        // @todo don't use SpaceApiObject's FROM_* constants as FROM_NAME will be dropped in the future
        //       define own constants in this class

        $spaceApiObject = null;

        switch ($loadFrom) {
            case SpaceApiObject::FROM_JSON:

                $spaceApiObject = SpaceApiObject::fromJson($data);
                break;

            case SpaceApiObject::FROM_FILE:

                $spaceApiObject = SpaceApiObject::fromFile($data);
                break;

            case SpaceApiObject::FROM_NAME:

                $spaceApiObject = SpaceApiObject::fromName($data);
                break;
        }

        $spaceApiObject->setValidator(new Validator());
        return $spaceApiObject;
    }
}
