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

        $this->result = json_decode($json, true);

        // @todo: don't hard-code the api version number
        $this->ok = @in_array('0.13', $this->result['valid']);
    }

    public function getOk()
    {
        return $this->ok;
    }

    public function getResult()
    {
        return $this->result;
    }
}
