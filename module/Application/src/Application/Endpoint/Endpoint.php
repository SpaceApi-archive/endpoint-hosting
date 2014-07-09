<?php

namespace Application\Endpoint;


use Application\SpaceApi\SpaceApiObject;
use Application\SpaceApi\SpaceApiObjectFactory;

class Endpoint
{
    /**
     * @var string the endpoint's slug, this property allows doctrine's Criteria class being used
     *             to filter specific SpaceApiObject instances after their slug
     */
    protected $slug;

    /**
     * @var ConfigFile representation of an endpoint's config file
     */
    protected $config;

    /**
     * @var SpaceApiObject
     */
    protected $spaceApiObject = null;

    function __construct($endpoint_path, $slug) {
        $this->slug = $slug;
        $this->spaceApiObject = SpaceApiObjectFactory::fromFile(
            $endpoint_path . '/spaceapi.json',
            $slug
        );

        $this->config = new ConfigFile($endpoint_path . '/config.json');
    }

    /**
     * @return ConfigFile
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return SpaceApiObject|null
     */
    public function getSpaceApiObject() {
        return $this->spaceApiObject;
    }

    /**
     * @return string endpoint slug
     */
    public function getSlug() {
        return $this->slug;
    }
} 