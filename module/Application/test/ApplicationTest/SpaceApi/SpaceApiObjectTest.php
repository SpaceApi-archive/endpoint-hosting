<?php

namespace ApplicationTest\SpaceApi;

// @todo do we need this line?
use ApplicationTest\Bootstrap;

use Application\SpaceApi\SpaceApiObject;

use PHPUnit_Framework_TestCase;
use SpaceApi\Validator\Validator;

class SpaceApiObjectTest extends \PHPUnit_Framework_TestCase
{
    // @depends and @dataProvider can't be combined, don't rely too much
    // on @dataProvider if you want to chain tests. If you use @depends
    // that function might need to be declared before the dependency
    // is actually used

    protected $validator = null;

    public function testSave() {

    }

    public function testUpdate() {

    }

    public function testFromName() {
        // @todo implement
    }

    public function testFromFile() {
        // @todo implement
    }

    public function testFromJson() {

        $object_list = array();

        foreach ($this->providerJsonData() as $data) {
            $json = $data[0];
            $spaceApiObject = SpaceApiObject::fromJson($json);
            $this->assertNotNull($spaceApiObject);

            if ($json === '') {
                $this->assertNull($spaceApiObject->object);
                $this->assertEmpty($spaceApiObject->json);
            } else {
                $this->assertNotNull($spaceApiObject->object);
                $this->assertNotEmpty($spaceApiObject->json);
            }

            $object_list[] = $spaceApiObject;
            unset($json);
        }

        return $object_list;
    }

    /**
     * @depends testFromJson
     */
    public function testSetValidator() {

        $spaceApiObjectList = func_get_args()[0];

        foreach ($spaceApiObjectList as $spaceApiObject) {
            $this->assertNull($spaceApiObject->validator);
            $spaceApiObject->setValidator($this->validator);
            $this->assertNotNull($spaceApiObject->validator);
        }

        return $spaceApiObjectList;
    }

    /**
     * @depends testSetValidator
     */
    public function testGetValidation() {
        $spaceApiObjectList = func_get_args()[0];

        /** @var SpaceApiObject $spaceApiObject */
        foreach ($spaceApiObjectList as $spaceApiObject) {
            // The validation property can only be null if no validator is set.
            $this->assertNotNull($spaceApiObject->validation);;
        }

        return $spaceApiObjectList;
    }

    public function providerValidator() {
        $validator = new Validator();

        return array(
            array($validator)
        );
    }

    public function providerJsonDataGood() {
        $goodJson = <<<JSON
{
    "api": "0.13",
    "space": "Slopspace",
    "logo": "http://your-space.org/img/logo.png",
    "url": "http://your-space.org",
    "location": {
        "address": "Ulmer Strasse 255, 70327 Stuttgart, Germany",
        "lon": 9.236,
        "lat": 48.777
    },
    "contact": {
        "twitter": "@spaceapi"
    },
    "issue_report_channels": [
        "twitter"
    ],
    "state": {
        "icon": {
            "open": "http://example.com/open.gif",
            "closed": "http://example.com/closed.gif"
        },
        "open": null
    }
}
JSON;
        return array(
            array($goodJson),
        );
    }

    public function providerJsonDataBad() {
        return array(
            array('{}'),
            array(''),
        );
    }

    public function providerJsonData() {
        return array_merge_recursive(
            static::providerJsonDataGood(),
            static::providerJsonDataBad()
        );
    }

    public function setUp() {
        $this->validator = new Validator();
    }
}
