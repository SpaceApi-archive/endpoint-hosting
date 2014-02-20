<?php

namespace Application\Controller\Console;

use Zend\Mvc\Controller\AbstractActionController;

class RepoController extends AbstractActionController
{
    public function indexAction()
    {
        $space_name = $this->params()->fromRoute('space_name');
        echo $space_name;
    }
}
