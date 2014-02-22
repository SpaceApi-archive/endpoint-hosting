<?php

namespace Application\Service;

use Application\Mail\EndpointMail;
use Application\Mail\EndpointMailInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EndpointMailFactory implements FactoryInterface
{
    /**
     * Create
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return EndpointMailInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $email = new EndpointMail();
        $recipients = $serviceLocator->get('config')['emails'];
        $email->setRecipients($recipients);

        return $email;
    }
}