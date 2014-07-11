<?php

namespace Application\Twig\Extension;

use Application\Utils\Utils;
use Slopjong\JOL;
use Zend\View\Helper\ServerUrl;

/**
 * SpaceApiExtension extends Twig_Extension and provides the filters
 * and functions as listed below.
 *
 * <strong>Filters</strong>
 *
 * <ul>
 *   <li>json_without_gist</li>
 *   <li>json_without_api</li>
 *   <li>jol_without_gist</li>
 *   <li>jol_without_api</li>
 *   <li>forward_slash</li>
 *   <li>normalize</li>
 *   <li>var_dump</li>
 *   <li>server_port</li>
 * </ul>
 *
 * <strong>Functions</strong>
 *
 * <ul>
 *   <li>serverUrl</li>
 * </ul>
 *
 * @package Application\Twig\Extension
 */
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
            new \Twig_SimpleFilter('json_without_api', array($this, "jsonWithoutApi")),
            new \Twig_SimpleFilter('jol_without_gist', array($this, "jolWithoutGist")),
            new \Twig_SimpleFilter('jol_without_api', array($this, "jolWithoutApi")),
            new \Twig_SimpleFilter('forward_slash', array($this, "forwardSlash")),
            new \Twig_SimpleFilter('normalize', array($this, "normalize")),
            new \Twig_SimpleFilter('var_dump', array($this, "varDump")),
            new \Twig_SimpleFilter('server_port', array($this, "serverPort")),
        );
    }

    public function getFunctions()
    {
        return array(
            // we need this function because the original ServerUrl
            // view helper doesn't return $this when it's invoked, it's
            // returning the full URL already

            // @todo the anonymous function doesn't appear in the outline
            //       define a named function so that code assistance is better supported
            new \Twig_SimpleFunction('serverUrl', function ($scheme = 'http', $port = ''){
                $helper = new ServerUrl();
                $helper->setScheme($scheme);

                if (empty($port)) {
                    // In development mode we have to use the port number
                    // which is configured for the port forwarding in
                    // Vagrantfile. In production mode we let the helper
                    // detect the port.
                    // @todo define global config, make this extension ServiceLocatorAware
                    //       and get the port from the config then
                    if ($scheme === 'https' && getenv('DEVELOPMENT') === 'true') {
                        $helper->setPort(8091);
                    } elseif ($scheme === 'https') {
                        $helper->setPort(443);
                    } elseif ($scheme === 'http' && getenv('DEVELOPMENT') === 'true') {
                        $helper->setPort(8090);
                    } elseif ($scheme === 'http') {
                        $helper->setPort(80);
                    }
                } else {
                    $helper->setPort($port);
                }

                return $helper();
            }),
        );
    }

    /**
     * JSON encodes a variable by removing the field 'api' if it exists.
     * If the input is a string the filter tries to decode it. If the
     * decoding fails, the unmodified input is returned.
     *
     * @param mixed   $value   The value to encode.
     *
     * @return string|boolean The JSON encoded value or false on encoding failure
     */
    function jsonWithoutApi($value)
    {
        if (is_object($value)) {
            unset($value->api);
        } elseif (is_string($value)) {
            $obj = json_decode($value);
            if (is_null($obj)) {
                return $value;
            }

            $value = $obj;
            unset($value->api);
        }

        return json_encode($value,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }

    /**
     * JSON encodes a variable by removing the field 'ext_gist' if it exists.
     * If the input is a string the filter tries to decode it. If the
     * decoding fails, the unmodified input is returned.
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
     * JOL encodes a variable by removing the field 'api' if it exists.
     * JOL is the abbreviation for Javascript Object Literal which looks
     * similar to JSON but both differ in their syntax.
     *
     * @param mixed   $value   The value to encode.
     *
     * @return string The JOL encoded value. The string '{}' is returned on encoding failure.
     */
    function jolWithoutApi($value) {
        $json = $this->jsonWithoutApi($value);

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