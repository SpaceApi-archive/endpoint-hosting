<?php

namespace Application\Service;


use Application\Endpoint\EndpointList;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EndpointListFactory implements FactoryInterface
{
    /**
     * Instantiate an endpoint list.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Application\Endpoint\EndpointList
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        return new EndpointList($config['endpointdir']);
    }
}