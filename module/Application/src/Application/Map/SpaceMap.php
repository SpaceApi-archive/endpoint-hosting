<?php

namespace Application\Map;

use JMS\Serializer\Annotation as JMS;

/**
 * @property string slug
 * @property string token
 *
 * @package Application\Map
 */
class SpaceMap
{
    /**
     * @JMS\Type("string")
     */
    protected $slug;

    /**
     * @JMS\Type("string")
     */
    protected $token;

    public function __construct($slug, $token)
    {
        $this->slug = $slug;
        $this->token = $token;
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property))
            return $this->$property;
    }
}