<?php

namespace Application\Controller;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * The AssetController delivers dynamically created and non-compressed
 * files. This controller should be used as less as possible for asset
 * files, only if something really needs to be done dynamically such as
 * setting the base path.
 *
 * Possibliy {@link https://github.com/albulescu/zf2-module-assets} should
 * be used to cache the assets.
 *
 * @package Application\Controller
 */
class AssetController extends AbstractActionController
{
    public function noAssetFoundAction() {
        $response = new Response();
        $response->setStatusCode(404);
        return $response;
    }

    /**
     * Generates the following asset files:
     *
     * <ul>
     *   <li>:host/:basepath/asset/js/globals.js</li>
     * </ul>
     *
     * The <code>:host</code> and <code>:basepath</code> identifiers are
     * placeholders for the actual domain and URL alias defined in the
     * VHOST config.
     */
    public function jsAction()
    {
        $file = $this->params('file');
        $file = str_replace('.js', '', $file);

        /**
         * $response is in fact a ResponseInterface but we let the code
         * assistant beleive that it's a Response to show us more
         * methods, which in fact should not be accessible at all but
         * yeah, it's PHP -_-
         * @var Response $response
         */
        $response = $this->getResponse();

        switch ($file) {

            case 'globals':

                $global_js = "var base_path='". $this->getRequest()->getBaseUrl() ."';";
                $response->setContent($global_js);
                break;

            default:

                // returns a not found response object
                return $this->noAssetFoundAction();
        }

        return $response;
    }
}
