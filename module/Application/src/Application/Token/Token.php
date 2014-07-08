<?php

namespace Application\Token;


use Application\Utils\Utils;

class Token
{
    /**
     * @var string file path where this token is located
     */
    protected $filePath;

    /**
     * @var string normalized hackerspace name
     */
    protected $slug;

    /**
     * @var string auto-generated token
     */
    protected $token;

    /**
     * @param string $name hackerspace name or the corresponding slug
     * @param string $tokenDir
     * @return \Application\Token\Token
     * @throws \InvalidArgumentException if an empty name is provided
     */
    public static function create($name, $tokenDir) {
        $normalized_name = Utils::normalize($name);

        if (empty($normalized_name)) {
            throw new \InvalidArgumentException('Empty name provided');
        }
        $file_path = $tokenDir . "/$normalized_name";
        file_put_contents(
            $file_path,
            Utils::generateSecret()
        );

        return new static($file_path);
    }

    /**
     * Creates a new token for a hackerspace.
     */
    public function reset() {
        $this->token = Utils::generateSecret();
        file_put_contents(
            $this->filePath,
            $this->token
        );
    }

    /**
     * Creates a new Token instance.
     * @param $filePath token file for a specific hackerspace
     */
    function __construct($filePath) {
        $this->filePath = $filePath;
        $this->slug = basename($filePath);
        $this->token = file_get_contents($filePath);
    }

    /**
     * @return string a hackerspace's token
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @return string the normalized hackerspace name
     */
    public function getSlug() {
        return $this->slug;
    }
} 