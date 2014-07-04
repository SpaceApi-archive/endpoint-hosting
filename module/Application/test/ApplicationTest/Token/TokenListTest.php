<?php

namespace ApplicationTest\Token;

// @todo do we need this line?
use Application\Token\TokenList;
use ApplicationTest\Bootstrap;

use Application\Token\Token;

use ApplicationTest\PHPUnitUtil;
use PHPUnit_Framework_TestCase;

class TokenListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string file path to the test token files in the tests dirctory
     */
    protected $tokenDir = 'data/tmp/tokens';

    /**
     * Tests the reload with the test endpoint which is a hidden directory
     */
    public function testReload() {
        $token_list = new TokenList(array(), $this->tokenDir);
        $this->assertEmpty($token_list->count());
        Token::create('Test', $this->tokenDir);
        $token_list->reload();
        $this->assertNotEmpty($token_list->count());
    }

    public function testReloadWithHiddenTokenFile() {

        $token_list = new TokenList(array(), $this->tokenDir);
        $this->assertEmpty($token_list->count());

        file_put_contents(
            $this->tokenDir . '/.test-endpoint',
            '1234'
        );

        $token_list->reload();
        $this->assertNotEmpty($token_list->count());
    }

    public function testGetSlugFromToken() {
        $token_list = new TokenList(array(), $this->tokenDir);
        $this->assertNull($token_list->getSlugFromToken('someinventedtoken'));

        $name = 'Invented space name';
        $token = Token::create($name, $this->tokenDir);
        $token_list->reload();

        $this->assertEquals(
            $token_list->getSlugFromToken($token->getToken()),
            $token->getSlug()
        );
    }

    public function setUp() {
        mkdir($this->tokenDir);
    }

    public function tearDown() {
        // nothing to do if the directory doesn't exist
        if (!file_exists($this->tokenDir)) {
            return;
        }

        $files = array();

        $non_hidden = glob($this->tokenDir . '/*');

        // if there are no non-hidden token files $nonhidden is not an array
        if (is_array($non_hidden)) {
            $files = $non_hidden;
        }

        $hidden = glob($this->tokenDir . '/.*');

        foreach ($hidden as $h) {
            if ($h !== '.' && $h !== '..') {
                $files[] = $h;
            }
        }

        if (count($files) > 0) {
            foreach ($files as $file) {
                // skip . and .. which are no files actually
                if (basename($file) === '.' || basename($file) === '..') {
                    continue;
                }
                unlink($file);
            }
        }

        rmdir($this->tokenDir);
    }
}
