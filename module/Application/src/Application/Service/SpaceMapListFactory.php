<?php
/**
 * Created by JetBrains PhpStorm.
 * User: slopjong
 * Date: 2/22/14
 * Time: 4:44 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Application\Service;


use Application\Map\SpaceMapList;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\SerializerBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SpaceMapListFactory implements FactoryInterface
{
    /**
     * Instantiate a serializer.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Serializer $serializer */
        $serializer = $serviceLocator->get('Serializer');
        $map_file_content = file_get_contents('data/map.json');

        /** @var SpaceMapList $map */
        $map = $serializer->deserialize(
            $map_file_content,
            'Application\Map\SpaceMapList',
            'json'
        );

        return $map;
    }
}