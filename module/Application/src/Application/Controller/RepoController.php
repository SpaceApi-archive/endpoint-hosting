<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class RepoController extends AbstractActionController
{
    /** @var AbstractActionController $this */

    public function cloneAction()
    {
        $space_name = $this->params()->fromQuery('name');
        $space_name = 'spacefasdfasdf';
        //$zip = new \ZipArchive();
        //if($zip->open('data/endpoint-scripts.zip'))
        if(copy('data/endpoint-scripts.zip', "public/space/$space_name"))
        {
            echo 'copy';
            //$zip->extractTo('public/space/' . $space_name);
        }
        die('end');
    }
}
