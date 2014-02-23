<?php

namespace Application\Map;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

// We can't use the ServiceLocatorAwareInterface here for some reason.
// This will throw an exception with the following semantical error message:
//
//    [Semantical Error] The annotation "@triggers" in method Zend\ModuleManager\ModuleManager::loadModules()
//    was never imported. Did you maybe forget to add a "use" statement for this annotation?
//
// To usethe interface we'd need to create our own annotation class which
// has to be registered in the SerializerFactory. Zend classes of interest:
// AnnotationManager, AnnotationBuilder and ModuleManager maybe we can
// get all the annotations from one of these classes.
class SpaceMapList extends AbstractList //implements ServiceLocatorAwareInterface
{
    /**
     * @JMS\Type("ArrayCollection<Application\Map\SpaceMap>")
     */
    protected $spacemap;
    protected $serviceLocator;

    public function __construct(ArrayCollection $elements)
    {
        $wrong_type = array_filter($elements, function($element) {
            if (! $element instanceof SpaceMap)
                return true;
            else
                return false;
        });

        if(count($wrong_type) > 0)
            throw new \Exception("Some elements are no instance of Application\Map\SpaceMap");

        $this->spacemap = $elements;
    }

    public function addMap($space, $key)
    {
        $this->spacemap->add(new SpaceMap($space, $key));
    }

    public function json()
    {
//        /** @var Serializer $serializer */
//        $serializer = $this->getServiceLocator()->get('Serializer');
//        $serializer->serialize($this, 'json');
    }

//    /**
//     * Set service locator. As soon as we set a service locator an
//     * exception is thrown. See the comment above.
//     *
//     * @param ServiceLocatorInterface $serviceLocator
//     */
//    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
//    {
//        $this->serviceLocator = $serviceLocator;
//    }
//
//    /**
//     * Get service locator
//     *
//     * @return ServiceLocatorInterface
//     */
//    public function getServiceLocator()
//    {
//        return $this->serviceLocator;
//    }
}