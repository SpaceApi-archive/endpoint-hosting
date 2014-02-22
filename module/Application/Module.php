<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Mail\EndpointMail;
use Zend\Di\ServiceLocator;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        /** @var $service_manager ServiceManager */
        $service_manager = $e->getApplication()->getServiceManager();
        $service_manager->get('translator');

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $config = $service_manager->get('config');

        // register an errorlog
        $error_log = new Logger;
        $writer = new Stream('data/logs/error.log');
        $error_log->addWriter($writer);
        $service_manager->setService('error_log', $error_log);

        // should the errorlog write all notices, warnings and errors
        // to an error log file?
        if($config['enabled_logger']['error_log'])
            Logger::registerErrorHandler($error_log);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
