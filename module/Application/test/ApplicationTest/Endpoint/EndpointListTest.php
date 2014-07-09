<?php

namespace ApplicationTest\Endpoint;

// @todo do we need this line?
use Application\Controller\EndpointController;
use Application\Endpoint\Endpoint;
use Application\Endpoint\EndpointList;
use Application\SpaceApi\SpaceApiObject;
use Application\Utils\Utils;
use ApplicationTest\Bootstrap;
use ApplicationTest\PHPUnitUtil;
use Doctrine\Common\Collections\Criteria;
use PHPUnit_Framework_TestCase;

class EndpointListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string file path to the test token files in the tests dirctory
     */
    protected $endpointDir = 'data/tmp/endpoints';

    /**
     * Tests the reload with the test endpoint which is a hidden directory
     */
    public function testReload() {
        $endpoint_list = new EndpointList($this->endpointDir);
        $this->assertEmpty($endpoint_list->count());

        $this->createEndpoint('asdf', 'http://localhost:8090');
        $this->createEndpoint('Test Endpoint', 'http://localhost:8090', '.test-endpoint');

        $endpoint_list->reload();

        $this->assertEquals(2, $endpoint_list->count());

        /** @var Endpoint $endpoint */
        $endpoint = $endpoint_list->current();

        $this->assertEquals('asdf', $endpoint->getSpaceApiObject()->name);

        $endpoint = $endpoint_list->next();
        $this->assertEquals('Test Endpoint', $endpoint->getSpaceApiObject()->name);
    }

    /**
     * @param string $name Not normalized hackerspace name
     * @param string $url Fake URL, no crawlable
     * @param string $slug overrides the internally normalized hackerspace name which is useful for hidden endpoints starting with a dot
     */
    protected function createEndpoint($name, $url, $slug = '') {

        // get the JSON template from the latest version of the endpoint scripts
        // and set the space name and URL
        $old_cwd = getcwd();
        chdir('../../../');
        $json = file_get_contents(EndpointController::ENDPOINT_SCRIPTS_DIR . "/spaceapi.json");
        $obj = json_decode($json);
        $obj->space = $name;
        $obj->url = $url;
        $json = json_encode($obj);
        chdir($old_cwd);

        if (empty($slug)) {
            $slug = Utils::normalize($name);
        }

        $endpoint_path = $this->endpointDir . "/$slug";
        $endpoint_spaceapi_file = $endpoint_path . "/spaceapi.json";

        // fake deployment of the endpoint
        mkdir($endpoint_path);
        file_put_contents($endpoint_spaceapi_file, $json);
    }

    public function setUp() {
        mkdir($this->endpointDir);
    }

    public function tearDown() {

        // nothing to do if the directory doesn't exist
        if (!file_exists($this->endpointDir)) {
            return;
        }

        $endpoint_paths = Utils::getFilesFromDir($this->endpointDir);

        foreach ($endpoint_paths as $path) {
            unlink($path . "/spaceapi.json");
            rmdir($path);
        }

        rmdir($this->endpointDir);
    }
}
