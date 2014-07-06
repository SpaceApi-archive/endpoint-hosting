<?php

namespace ApplicationTest\Token;

// @todo do we need this line?
use Application\Utils\Utils;
use ApplicationTest\Bootstrap;

use ApplicationTest\PHPUnitUtil;
use PHPUnit_Framework_TestCase;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    protected $utilsDir = 'data/tmp/utils';

    protected $file_data = array(
        array('test1', 'test'),
        array('test2', '1234,;'),
        array('.test3', 'test'),
    );

    public function testGetFilesFromDir() {

        $utils_paths_is = Utils::getFilesFromDir($this->utilsDir);
        $utils_paths_should = array();

        foreach ($this->file_data as $data) {
            $utils_paths_should[] = $this->utilsDir . '/' . $data[0];
        }

        $this->assertEquals($utils_paths_should, $utils_paths_is);
    }

    public function setUp() {
        mkdir($this->utilsDir);

        foreach ($this->file_data as $file) {
            file_put_contents($this->utilsDir . '/' . $file[0], $file[1]);
        }
    }

    public function tearDown() {

        // nothing to do if the directory doesn't exist
        if (!file_exists($this->utilsDir)) {
            return;
        }

        foreach ($this->file_data as $file) {
            unlink($this->utilsDir . '/' . $file[0]);
        }

        rmdir($this->utilsDir);
    }
}
