<?php

namespace Application\Service;


use Application\Token\Token;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TokenFactory implements FactoryInterface
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
        return new Token(array(), $config['tokendir']);
    }
}