<?php

$module_config = array(
    'service_manager' => array(
        // alternatively we could implement getServiceConfig() in Module.php
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'EndpointMail' => 'Application\Service\EndpointMailFactory',
            'Serializer' => 'Application\Service\SerializerFactory',
            'SpaceMapList' => 'Application\Service\SpaceMapListFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
//    'console' => array(
//        'router' => array(
//            'routes' => array(
//                'clone-repo' => array(
//                    'type' => 'Zend\Mvc\Router\Console\Simple',
//                    'options' => array(
//                        'route'    => 'repo clone <space_name>',
//                        'defaults' => array(
//                            'controller' => 'Application\Controller\Console\Registry',
//                            'action'     => 'register',
//                        ),
//                    ),
//                ),
//            ),
//        ),
//    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'       => '/[home]',
                    'constraints' => array(
                        'action' => '[0-9a-fA-F-]+',
                        'controller' => '[0-9a-fA-F-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Endpoint',
                        'action'     => 'index',
                    )
                ),
            ),
            'endpoint' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/endpoint',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Endpoint',
                        'action'     => 'index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'action' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/:action',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            // Console controllers (not accessible through the web server)
//            'Application\Controller\Console\Repo'       => 'Application\Controller\Console\RepoController',

            // Controllers invokable through the web server
            'Application\Controller\Endpoint'       => 'Application\Controller\EndpointController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/default.phtml',
            'layout/landing'           => __DIR__ . '/../view/layout/landing.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array('ZfcTwigViewStrategy'),
    ),
    'zfctwig' => array(
        /**
         * Service manager alias of the loader to use with ZfcTwig. By default, it uses
         * the included ZfcTwigLoaderChain which includes a copy of ZF2's TemplateMap and
         * TemplatePathStack.
         */
        'environment_loader' => 'ZfcTwigLoaderChain',

        /**
         * Options that are passed directly to the Twig_Environment.
         */
        'environment_options' => array(),

        /**
         * Service manager alias of any additional loaders to register with the chain. The default
         * has the TemplateMap and TemplatePathStack registered. This setting only has an effect
         * if the `environment_loader` key above is set to ZfcTwigLoaderChain.
         */
        'loader_chain' => array(
            'ZfcTwigLoaderTemplateMap',
            'ZfcTwigLoaderTemplatePathStack'
        ),

        /**
         * Service manager alias or fully qualified domain name of extensions. ZfcTwigExtension
         * is required for this module to function!
         */
        'extensions' => array(
            'zfctwig' => 'ZfcTwigExtension',
            'spaceapitwig' => 'Application\Twig\Extension\SpaceApiExtension',
        ),

        /**
         * The suffix of Twig files. Technically, Twig can load *any* type of file
         * but the templates in ZF are suffix agnostic so we must specify the extension
         * that's expected here.
         */
        'suffix' => 'twig',

        /**
         * When enabled, the ZF2 view helpers will get pulled using a fallback renderer. This will
         * slightly degrade performance but must be used if you plan on using any of ZF2's view helpers.
         */
        'enable_fallback_functions' => true,

        /**
         * If set to true disables ZF's notion of parent/child layouts in favor of
         * Twig's inheritance model.
         */
        'disable_zf_model' => true
    ),
);

return $module_config;