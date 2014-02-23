<?php

namespace Application\Utils;


use Application\Gist\Result;

class Utils
{
    /**
     * Copies a file or a directory recursively.
     *
     * @param $source
     * @param $destination
     */
    public static function rcopy($source, $destination)
    {
        if (is_dir($source)) {
            mkdir($destination);
            $files = scandir($source);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    static::rcopy("$source/$file", "$destination/$file");
                }
            }
        } elseif (file_exists($source)) {
            copy($source, $destination);
        }
    }

    /**
     * Replaces non alpha-numeric characters with underscores.
     *
     * @param string $input Any string input
     * @return string Normalized string
     * @throws \Exception If the input is not a string
     */
    public static function normalize($input)
    {
        if (! is_string($input))
            throw new \Exception('Input is not a string!');

        // remove illegal characters
        $input = preg_replace("/[^a-zA-Z0-9]/i", "_", $input);

        // reduce sequences of underscores to a single one
        $input = preg_replace("/_{2,}/i", "_", $input);

        return strtolower($input);
    }

    /**
     * Generate a secret key in an over-engineered manner.
     *
     * @return string
     */
    public static function generateSecret()
    {
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), "+", ".");
        $shuffle = str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
        $param = '$' . implode( '$', array("2y", 10, $salt) );
        $blowfish = crypt($shuffle, $param);
        $secret = array_reverse( explode( '$', $blowfish ) )[0];
        return $secret;
    }

    /**
     * Posts a gist using the github API. If an ID is profided the existing
     * gist will be updated.
     *
     * @param string $token OAUTH token
     * @param string $file File name
     * @param string $content Gist content
     * @param int|string $id Gist ID
     * @return Result The gist result
     */
    public static function postGist($token, $file, $content, $id = '')
    {
        $data = array(
            'description' => 'Example showing how to post a gist.',
            'public' => 1,
            'files' => array(
                $file => array('content' => $content),
            ),
        );

        // we must set a user agent and a valid token
        // http://developer.github.com/v3/#user-agent-required
        $headers = array(
            'User-Agent: SpaceApi/endpoint-scripts (issue prefered of email)',
            'Authorization: token ' . $token,
        );

        $url = 'https://api.github.com/gists';

        if (!empty($id)) {
            $url .= "/$id";
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (empty($id)) {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        }

        $response = curl_exec($ch);
        $http_status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new Result($http_status, $response);
    }

    /**
     * Gets a gist using the github API.
     *
     * @param string $token OAUTH token
     * @param int|string $id Gist ID
     * @return Result|null The gist result
     */
    public static function getGist($token, $id)
    {
        if (empty($id))
            return null;

        // we must set a user agent and a valid token
        // http://developer.github.com/v3/#user-agent-required
        $headers = array(
            'User-Agent: SpaceApi/endpoint-scripts (issue prefered of email)',
            'Authorization: token ' . $token,
        );

        $url = 'https://api.github.com/gists/$id';
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($ch);
        $http_status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new Result($http_status, $response);
    }
}