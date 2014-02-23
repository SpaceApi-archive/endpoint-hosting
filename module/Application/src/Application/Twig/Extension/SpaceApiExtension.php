<?php

namespace Application\Twig\Extension;

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
            new \Twig_SimpleFilter('jsonWithoutGist', array($this, "jsonWithoutGist")),
            new \Twig_SimpleFilter('forwardSlash', array($this, "forwardSlash")),
        );
    }

    /**
     * JSON encodes a variable by removing the field 'ext_gist' if it exists.
     *
     * @param mixed   $value   The value to encode.
     * @param integer $options Not used on PHP 5.2.x
     *
     * @return mixed The JSON encoded value
     */
    function jsonWithoutGist($value, $options = 0)
    {
        if (is_object($value)) {
            unset($value->ext_gist);
        } elseif (is_string($value)) {
            $obj = json_decode($value);
            if (!is_null($obj)) {
                $value = $obj;
                unset($value->ext_gist);
            }
        }

        return json_encode($value, JSON_PRETTY_PRINT);
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
}