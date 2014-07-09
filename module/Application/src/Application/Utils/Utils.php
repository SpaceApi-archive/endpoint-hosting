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
        if (! is_string($input)) {
            throw new \Exception(get_class($input) . ' give but a string expected!');
        }

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

    /**
     * Adds an api field with the latest stable version number to the
     * passed JSON if it's missing. If it's present it's overridden.
     * It returns the unmodified input if the json could not be decoded.
     *
     * @param string $json
     * @return string
     */
    public static function setApiToLatest($json) {

        // @todo: don't hard-code the stable api version
        //        define the latest api version in the global config
        // @todo: instead of this utility function this should be in SpaceApiObject

        // Set the api version to 0.13, the last stable version.
        // We decode to an associative array to prepend the api
        // field. This is to avoid an unnecessary change to be
        // tracked by gist.
        $unmarshalled_json_array = json_decode($json, true);
        if (! is_null($unmarshalled_json_array)) {
            $unmarshalled_json_array = array_merge(
                array('api' => '0.13'),
                $unmarshalled_json_array
            );

            // this is an extra step which we need, if the api field
            // is present in $json. Then it could happen that the api
            // field from $json is overriding ours again.
            $unmarshalled_json_array['api'] = '0.13';

            $json = json_encode($unmarshalled_json_array);
        }

        return $json;
    }

    /**
     * Wrapper of PHP's native json_encode by using pre-defined flags
     * for pretty print, unescaped slashes and unicode.
     * @param $mixed
     * @return string prettyfied JSON
     */
    public static function json_encode($mixed) {
        return json_encode($mixed, JSON_PRETTY_PRINT |
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $dir directory without a trailing slash
     * @return array list of files containing in $dir
     */
    public static function getFilesFromDir($dir) {
        $file_paths = array();

        $nonhidden = glob($dir . '/*');

        // if there are no non-hidden token files $nonhidden is not an array
        if (is_array($nonhidden)) {
            $file_paths = $nonhidden;
        }

        $hidden = glob($dir . '/.*');

        if (is_array($hidden)) {
            foreach ($hidden as $h) {
                if (basename($h) !== '.' && basename($h) !== '..') {
                    $file_paths[] = $h;
                }
            }
        }

        return $file_paths;
    }
}