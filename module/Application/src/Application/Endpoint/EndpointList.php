<?php

namespace Application\Endpoint;


use Application\SpaceApi\SpaceApiObjectFactory;
use Application\Utils\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * List of all the created endpoints.
 * @package Application\SpaceApi
 */
class EndpointList extends ArrayCollection
{
    protected $endpointDir;

    /**
     * Creates a new EndpointList instance in two ways:
     *
     * <ul>
     *   <li>(default) empty array of elements passed, endpoint directory provided</li>
     *   <li>array of elements provided while a given endpoint directory not considered</li>
     * </ul>
     *
     * @param array $elements list of endpoints which this instance should be initialized from
     * @param string $endpointDir directory that contains the deployed endpoint scripts
     */
    function __construct($elements = array(), $endpointDir = '') {
        if (! empty($elements)) {
            parent::__construct($elements);
        } else {
            $this->endpointDir = $endpointDir;
            $this->reload();
        }
    }

    /**
     * Reloads the list of deployed endpoints by instantiating a SpaceApiObject
     * for every spaceapi JSON file found in the endpoint directory.
     */
    public function reload() {
        $endpoint_paths = Utils::getFilesFromDir($this->endpointDir);

        $this->clear();

        foreach ($endpoint_paths as $endpoint_path) {
            $spaceApiObject = SpaceApiObjectFactory::create(
                $endpoint_path . '/spaceapi.json',
                SpaceApiObjectFactory::FROM_FILE
            );
            $this->add($spaceApiObject);
        }
    }
} 