<?php

namespace Application\Service;


use Application\Token\TokenList;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TokenListFactory implements FactoryInterface
{
    /**
     * Instantiate a token list.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Application\Token\TokenList
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        return new TokenList(array(), $config['tokendir']);
    }
}