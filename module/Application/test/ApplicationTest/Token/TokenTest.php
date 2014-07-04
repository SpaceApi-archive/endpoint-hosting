<?php

namespace ApplicationTest\Token;

// @todo do we need this line?
use Application\Utils\Utils;
use ApplicationTest\Bootstrap;

use Application\Token\Token;

use ApplicationTest\PHPUnitUtil;
use PHPUnit_Framework_TestCase;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string file path to the test token files in the tests dirctory
     */
    protected $tokenDir = 'data/tmp/tokens';

    protected $testNames = array(
        '',
        'Test',
        '1234',
        ',;+-"\'/',
    );

    /**
     * Tests Token::create() but also Token::getSlug() and Token::getToken()
     */
    public function testCreate() {

        foreach ($this->testNames as $name) {

            if (empty($name)) {
                $this->setExpectedException('InvalidArgumentException');
                Token::create($name, $this->tokenDir);
                continue;
            }

            $normalized_name = Utils::normalize($name);

            $token = Token::create($name, $this->tokenDir);

            $this->assertNotEmpty($token->getSlug());
            $this->assertNotEmpty($token->getToken());
            $this->assertEquals($normalized_name, $token->getSlug());

            $file_path = $this->tokenDir . "/$normalized_name";
            $this->assertFileExists($file_path);
        }
    }

    public function setUp() {
        mkdir($this->tokenDir);
    }

    public function tearDown() {

        // nothing to do if the directory doesn't exist
        if (!file_exists($this->tokenDir)) {
            return;
        }

        $files = glob($this->tokenDir . '/*');

        if (is_array($files)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }

        rmdir($this->tokenDir);
    }
}
