<?php

namespace Application\Controller;

use Application\Exception\EndpointExistsException;
use Application\Mail\EndpointMailInterface;
use Application\Utils\Utils;
use Zend\Mvc\Controller\AbstractActionController;

class EndpointController extends AbstractActionController
{
    public function createAction()
    {
        $space = $this->params()->fromPost('name');
        $space_normalized = Utils::normalize($space);

        // generate a new api key
        $api_key = Utils::generateSecret();

        if(! empty($space_normalized)) {

            /** @var EndpointMailInterface $email */
            $email = $this->getServiceLocator()->get('EndpointMail');

            try {
                $this->createEndpoint($space_normalized, $api_key);
                $email->send(
                    "New endpoint created for $space",
                    'New space created.'
                );

                $gist_urls = $this->createGist($space_normalized);
                $this->addSpaceMap($space_normalized, $api_key);
                $json = $this->getSpaceapiJson($space_normalized);

                // we need to start a session here
                return array(
                    'error'   => false,
                    'api_key' => $api_key,
                    'space'   => $space_normalized,
                );

            } catch (EndpointExistsException $e) {
                $email->send(
                    "Endpoint creation failed for $space",
                    'The endpoint already exists.'
                );

                return array(
                    'error' => true,
                );
            }
        }
    }

    public function editAction()
    {
        $json = '';
        $gist_urls = array();

        return array(
            'api_key' => $api_key,
            'spaceapi_json' => $json,
            'gist_html_url' => $gist_urls['html_url'],
            'gist_raw_url' => $gist_urls['raw_url'],
        );
    }

    public function validateAction()
    {
    }

    //****************************************************************
    // HELPERS

    /**
     * Creates a new endpoint and adds a new entry to the space map.
     *
     * @param $space The normalized space name
     * @param $api_key A secret key
     * @throws \Application\Exception\EndpointExistsException
     */
    protected function createEndpoint($space, $api_key)
    {
        // create the new endpoint
        $file_path = "public/space/$space";

        if(file_exists($file_path))
            throw new EndpointExistsException();

        Utils::rcopy('data/endpoint-scripts', $file_path);

        // fix the base url for the new endpoint
        $htaccess_file = $file_path . '/.htaccess';
        $htaccess_file_content = file_get_contents($htaccess_file);
        $htaccess_file_content = str_replace(
            "RewriteBase /",
            "RewriteBase /space/$space",
            $htaccess_file_content
        );
        file_put_contents($htaccess_file, $htaccess_file_content);

        // fix the secret key
        $config_file = "$file_path/config.json";
        $config_file_content = file_get_contents($config_file);
        $config = json_decode($config_file_content);
        $config->api_key = $api_key;
        $config_file_content = json_encode($config, JSON_PRETTY_PRINT);
        file_put_contents($config_file, $config_file_content);
    }

    /**
     * Creates a new gist and saves the ID to the json.
     *
     * @param $space_normalized
     */
    protected function createGist($space_normalized)
    {
        $spaceapi_file = "public/space/$space_normalized/spaceapi.json";
        $spaceapi_file_content = file_get_contents($spaceapi_file);

        $config = $this->getServiceLocator()->get('config');

        $gist_file = "$space_normalized.json";

        $response = Utils::postGist(
            $config['gist_token'],
            $gist_file,
            $spaceapi_file_content
        );

        // save the gist id in the spaceapi json
        $spaceapi = json_decode($spaceapi_file_content);
        $spaceapi->gist = $response['id'];
        $spaceapi_file_content_new = json_encode($spaceapi, JSON_PRETTY_PRINT);
        file_put_contents($spaceapi_file, $spaceapi_file_content_new);

        // the raw url has a trailing } which we strip later
        $gist_raw_url = $response['files'][$gist_file]['raw_url'];

        return array(
            'html_url' => $response['html_url'],
            'raw_url' => str_replace('}', '', $gist_raw_url),
        );
    }

    /**
     * Loads the json and removes the gist ID.
     *
     * @param $space_normalized
     * @return string
     */
    protected function getSpaceapiJson($space_normalized)
    {
        $spaceapi_file = "public/space/$space_normalized/spaceapi.json";
        $spaceapi_file_content = file_get_contents($spaceapi_file);
        $spaceapi = json_decode($spaceapi_file_content);
        unset($spaceapi->gist);

        return json_encode($spaceapi, JSON_PRETTY_PRINT);
    }

    protected function addSpaceMap($space_name, $api_key)
    {
        $map = $this->getServiceLocator()->get('SpaceMapList');
        $map->addMap($space_name, $api_key);

        // To save the map we need to do this outside SpaceMapList
        // for some reason. Read the comment in that class.

        // serialize the map
        $serializer = $this->getServiceLocator()->get('Serializer');
        $map = $serializer->serialize($map, 'json');

        // pretty print
        $map = json_encode(json_decode($map), JSON_PRETTY_PRINT);

        // write back to the map file
        file_put_contents('data/map.json', $map);
    }
}
