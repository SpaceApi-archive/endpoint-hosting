<?php

namespace Application\Controller;

use Application\Exception\EndpointExistsException;
use Application\Mail\EndpointMailInterface;
use Application\Mail\NewEndpointMail;
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
            } catch (EndpointExistsException $e) {
                $email->send(
                    "Endpoint creation failed for $space",
                    'The endpoint already exists.'
                );
            }
        }

        return array(
            'api_key' => $api_key
        );
    }

    /**
     * Creates a new endpoint.
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
}
