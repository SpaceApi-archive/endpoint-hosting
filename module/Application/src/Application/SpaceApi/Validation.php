<?php

namespace Application\SpaceApi;


class Validation
{
    protected $ok = false;
    protected $result;

    /**
     * @param $json
     */
    public function __construct($json)
    {
        // TODO: validate $json against a schema and output a warning
        if (false) {
            // output the warning here
            $this->result = (object) array(
                "valid"    => array(),
                "invalid"  => array(),
                "errors"   => array(),
                "warnings" => array(),
            );
        }

        $this->result = json_decode($json);
        $this->ok = property_exists($this->result, 'valid')
            && !empty($this->result->valid);
    }

    public function getOk()
    {
        return $this->ok;
    }
}
