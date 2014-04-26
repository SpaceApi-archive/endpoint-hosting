<?php

namespace Application\Twig\Extension;

use Application\Utils\Utils;
use Slopjong\JOL;

class SpaceApiExtension extends \Twig_Extension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'spaceapiextension';
    }

    /**
     * Returns a list of twig filters.
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('json_without_gist', array($this, "jsonWithoutGist")),
            new \Twig_SimpleFilter('jol_without_gist', array($this, "jolWithoutGist")),
            new \Twig_SimpleFilter('forward_slash', array($this, "forwardSlash")),
            new \Twig_SimpleFilter('normalize', array($this, "normalize")),
            new \Twig_SimpleFilter('var_dump', array($this, "varDump")),
            new \Twig_SimpleFilter('server_port', array($this, "serverPort")),
        );
    }

    /**
     * JSON encodes a variable by removing the field 'ext_gist' if it exists.
     *
     * @param mixed   $value   The value to encode.
     * @param integer $options Not used on PHP 5.2.x
     *
     * @return string|boolean The JSON encoded value or false on encoding failure
     */
    function jsonWithoutGist($value, $options = 0)
    {
        if (is_object($value)) {
            unset($value->ext_gist);
        } elseif (is_string($value)) {
            $obj = json_decode($value);
            if (is_null($obj)) {
                return $value;
            }

            $value = $obj;
            unset($value->ext_gist);
        }

        return json_encode($value,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }

    /**
     * JOL encodes a variable by removing the field 'ext_gist' if it exists.
     * JOL is the abbreviation for Javascript Object Literal which looks
     * similar to JSON but both differ in their syntax.
     *
     * @param mixed   $value   The value to encode.
     *
     * @return string The JOL encoded value. The string '{}' is returned on encoding failure.
     */
    function jolWithoutGist($value) {
        $json = $this->jsonWithoutGist($value);

        //
        if ($json === false) {
            return '{}';
        }

        $jsoneditor_default_input = json_decode($json);
        $jol = new JOL();
        return $jol->encode($jsoneditor_default_input);
    }

    /**
     * Replaces '\/' sequences with '/'.
     *
     * @param mixed   $value
     * @param integer $options Not used on PHP 5.2.x
     *
     * @return mixed The JSON encoded value
     */
    function forwardSlash($value, $options = 0)
    {
        if (is_string($value)) {
            return str_replace('\/', '/', $value);
        }

        return $value;
    }

    /**
     * Replaces characters that are not alpha-numeric or underscores.
     *
     * @param mixed   $value
     * @param integer $options Not used on PHP 5.2.x
     *
     * @return mixed The normalized string.
     */
    function normalize($value, $options = 0)
    {
        if (is_string($value)) {
            return Utils::normalize($value);
        }
        return $value;
    }

    /**
     * Vardumps.
     *
     * @param mixed   $value
     * @param integer $options Not used on PHP 5.2.x
     *
     * @return void
     */
    function varDump($value, $options = 0)
    {
        var_dump($value);
    }

    /**
     * Appends the server port number to a passed string. If a non-string
     * is passed, the value is returned unmodified.
     *
     * @param string   $value
     *
     * @return string
     */
    function serverPort($value)
    {
        if (is_string($value)) {
            $port = intval($_SERVER['SERVER_PORT']);
            if ($port !== 80) {
                $value .= ":$port";
            }
        }
        return $value;
    }
}