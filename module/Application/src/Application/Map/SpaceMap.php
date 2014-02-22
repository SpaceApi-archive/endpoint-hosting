<?php

namespace Application\Map;

use JMS\Serializer\Annotation as JMS;

/**
 * @property string space_normalized
 * @property string api_key
 *
 * @package Application\Map
 */
class SpaceMap
{
    /**
     * @JMS\Type("string")
     */
    protected $space_normalized;

    /**
     * @JMS\Type("string")
     */
    protected $api_key;

    public function __construct($space_normalized, $api_key)
    {
        $this->space_normalized = $space_normalized;
        $this->api_key = $api_key;
    }

    public function __get($property)
    {
        if (property_exists($this, $property))
            return $this->$property;
    }
}