<?php
/**
 * Created by JetBrains PhpStorm.
 * User: slopjong
 * Date: 2/22/14
 * Time: 4:44 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Application\Service;


use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use JMS\Serializer\SerializerBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SerializerFactory implements FactoryInterface
{
    /**
     * Instantiate a serializer.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // in v0.13 of the serializer the annotations had to be registered
        // we're currently using v0.15 so it could be already fixed.
        $namespaces = include 'vendor/composer/autoload_namespaces.php';
        AnnotationRegistry::registerAutoloadNamespace(
            "JMS\Serializer",
            $namespaces['JMS\Serializer']
        );

        return SerializerBuilder::create()->build();
    }
}