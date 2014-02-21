<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class RepoController extends AbstractActionController
{
    /** @var AbstractActionController $this */

    public function cloneAction()
    {
        $space_name = $this->params()->fromQuery('name');

        if(! empty($space_name)) {

            // create the new endpoint
            $file_path = "public/space/$space_name";
            mkdir($file_path);
            $this->rcopy('data/endpoint-scripts', $file_path);

            // fix the base url for the new endpoint
            $htaccess = file_get_contents($file_path . '/.htaccess');
            $htaccess = str_replace("RewriteBase /", "RewriteBase /space/$space_name", $htaccess);
            file_put_contents($file_path . '/.htaccess', $htaccess);
        }
        die('end');
    }

    private function rcopy($src, $dst) {
        if (is_dir($src)) {
            mkdir($dst);
            $files = scandir($src);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") $this->rcopy("$src/$file", "$dst/$file");
            }
        } elseif (file_exists($src))
            copy($src, $dst);
    }
}
