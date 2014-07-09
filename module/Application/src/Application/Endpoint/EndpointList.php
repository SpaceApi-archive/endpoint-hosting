<?php

namespace Application\Endpoint;


use Application\Common\Collections\DirectoryArrayCollection;

/**
 * List of all the created endpoints.
 * @package Application\Endpoint
 */
class EndpointList extends DirectoryArrayCollection
{
    /**
     * Reloads the list of deployed endpoints by instantiating a SpaceApiObject
     * for every spaceapi JSON file found in the endpoint directory.
     */
    public function reload() {
        parent::reset();
        foreach ($this->_files as $endpoint_path) {
            $slug = basename($endpoint_path);

            // don't consider the git ignore file
            if ($slug !== '.gitignore') {
                $endpoint = new Endpoint($endpoint_path, $slug);
                $this->add($endpoint);
            }
        }
    }
}
