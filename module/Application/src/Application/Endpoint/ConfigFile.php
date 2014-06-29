<?php

namespace Application\Endpoint;

/**
 * Class ConfigFile
 * @package Application\Endpoint
 * 
 * @property-read string filePath full path including the file name
 * @property-read \stdClass config the config
 * @property-read string token the endpoint's authentication token
 */
class ConfigFile
{
    protected $filePath = '';
    protected $config = null;

    protected $token = '';

    /**
     * @param $file_path full path including the file name
     */
    public function __construct($file_path) {
        $this->filePath = $file_path;
        if (! file_exists($file_path)) {
            return;
        }
        $this->config = json_decode(file_get_contents($file_path));
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

    /**
     * Check if a private/protected member exists. This magic function
     * must be implemented in order to access the properties from within the
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
     * Sets the token.
     * @param string $token
     */
    public function setToken($token) {
        $this->token = $token;
        if (!is_null($this->config)) {
            $this->config->api_key = $token;
        }
    }

    /**
     * Saves the endpoint configuration to the file.
     */
    public function save() {
        if (!is_null($this->config)) {
            $config_file_content = json_encode($this->config,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($this->filePath, $config_file_content);
        }
    }
}
