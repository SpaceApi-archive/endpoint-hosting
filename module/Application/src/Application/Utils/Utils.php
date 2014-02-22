<?php

namespace Application\Utils;


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
        } elseif (file_exists($source))
            copy($source, $destination);
    }

    /**
     * Replaces non alpha-numeric characters with underscores.
     *
     * @param string $input Any string input
     * @return string Normalized string
     * @throws Exception If the input is not a string
     */
    public static function normalize($input)
    {
        if (! is_string($input))
            throw new Exception('Input is not a string!');

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
     * Posts a gist using the github API.
     *
     * @param $token OAUTH token
     * @param $file File name
     * @param $content Gist content
     * @return array
     */
    public static function postGist($token, $file, $content)
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
            'User-Agent: SpaceApi/endpoint-scripts (issue prefered over email)',
            'Authorization: token ' . $token,
        );

        $ch = curl_init('https://api.github.com/gists');

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, TRUE);
    }

    /**
     * Updates a gist using the github API.
     *
     * @param $token OAUTH token
     */
    public static function updateGist($token, $file, $content, $id)
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

        # Sending the data using cURL
        $ch = curl_init('https://api.github.com/gists/' . $id);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, TRUE);
    }
}