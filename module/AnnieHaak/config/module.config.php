<?php

namespace AnnieHaak;

return array(
    'router' => array(
        'routes' => array(
            '/' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'AnnieHaak\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'auth' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/auth[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller' => 'Auth',
                        'action' => 'logout',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Auth\Controller',
                                'controller' => 'Auth',
                                'action' => 'process',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Auth\Controller',
                                'controller' => 'Auth',
                                'action' => 'logout',
                            ),
                        ),
                    ),
                ),
            ),
            'user-admin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user-admin[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller' => 'Users',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'business-admin' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/business-admin',
                    'defaults' => array(
                        'controller' => 'AnnieHaak\Controller\ViewEdit',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'product-types' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/product-types[/:action][/:id]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\ProductTypes',
                                'action' => 'index'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        ),
                    ),
                ),
            ),
            'reports' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/reports',
                    'defaults' => array(
                        'controller' => 'AnnieHaak\Controller\Reports',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'dynamic-reports' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/dynamic-reports[/:action][/:id]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\DynamicReports',
                                'action' => 'index'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        ),
                    ),
                ),
            ),
            'create-add' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/create-add[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'AnnieHaak\Controller\CreateAdd',
                        'action' => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'AnnieHaak\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'AnnieHaak\Controller\Index' => Controller\IndexController::class,
            'AnnieHaak\Controller\ViewEdit' => Controller\ViewEditController::class,
            'AnnieHaak\Controller\Reports' => Controller\ReportsController::class,
            'AnnieHaak\Controller\CreateAdd' => Controller\CreateAddController::class,
            'AnnieHaak\Controller\ProductTypes' => Controller\ProductTypesController::class,
            'AnnieHaak\Controller\DynamicReports' => Controller\DynamicReportsController::class
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'AnnieHaak/index/index' => __DIR__ . '/../view/AnnieHaak/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'home',
                'order' => -100,
                'class' => 'top-level',
            ),
            array(
                'label' => 'Business Administration',
                'route' => 'business-admin',
                'order' => 200,
                'class' => 'top-level',
                'pages' => array(
                    array(
                        'label' => 'Collections',
                        'route' => 'business-admin/collections',
                        'contoller' => 'ViewEditController',
                        'action' => 'index',
                        'order' => 10,
                    ),
                    array(
                        'label' => 'Labour Time',
                        'route' => 'business-admin/labour-time',
                        'contoller' => 'ViewEditController',
                        'action' => 'index',
                        'order' => 20,
                    ),
                    array(
                        'label' => 'Packaging',
                        'route' => 'business-admin/packaging',
                        'contoller' => 'ViewEditController',
                        'action' => 'index',
                        'order' => 30,
                    ),
                    array(
                        'label' => 'Product',
                        'route' => 'business-admin/product',
                        'contoller' => 'ViewEditController',
                        'action' => 'index',
                        'order' => 40,
                    ),
                    array(
                        'label' => 'Product Types',
                        'route' => 'business-admin/product-types',
                        'contoller' => 'ViewEditController',
                        'action' => 'index',
                        'order' => 50,
                    ),
                    array(
                        'label' => 'Rates Percentages',
                        'route' => 'business-admin/rates-percentages',
                        'contoller' => 'ViewEditController',
                        'action' => 'index',
                        'order' => 60,
                    ),
                    array(
                        'label' => 'Raw Materials',
                        'route' => 'business-admin/raw-materials',
                        'contoller' => 'ViewEditController',
                        'action' => 'index',
                        'order' => 70,
                    ),
                    array(
                        'label' => 'Raw Materials Types',
                        'route' => 'business-admin/raw-materials-types',
                        'contoller' => 'ViewEditController',
                        'action' => 'index',
                        'order' => 80,
                    ),
                    array(
                        'label' => 'Suppliers',
                        'route' => 'business-admin/suppliers',
                        'contoller' => 'ViewEditController',
                        'action' => 'index',
                        'order' => 90,
                    ),
                ),
            ),
            array(
                'label' => 'Business Reports',
                'route' => 'reports',
                'order' => 300,
                'class' => 'top-level',
                'pages' => array(
                    array(
                        'label' => 'All Dynamic Reports',
                        'route' => 'reports/dynamic-reports',
                        'contoller' => 'ReportsController',
                        'action' => 'index',
                        'order' => 10,
                    ),
                    array(
                        'label' => 'Collections & Occasions',
                        'route' => 'reports/collections-occasions',
                        'contoller' => 'ReportsController',
                        'action' => 'index',
                        'order' => 20,
                    ),
                    array(
                        'label' => 'Margins',
                        'route' => 'reports/margins',
                        'contoller' => 'ReportsController',
                        'action' => 'index',
                        'order' => 30,
                    ),
                    array(
                        'label' => 'Products By Occasions',
                        'route' => 'reports/products-by-occasions',
                        'contoller' => 'ReportsController',
                        'action' => 'index',
                        'order' => 40,
                    ),
                    array(
                        'label' => 'RRP & RMs',
                        'route' => 'reports/rrp-rms',
                        'contoller' => 'ReportsController',
                        'action' => 'index',
                        'order' => 50,
                    ),
                    array(
                        'label' => 'Trade Pack RMs + Time',
                        'route' => 'reports/trade-pack-rms-time',
                        'contoller' => 'ReportsController',
                        'action' => 'index',
                        'order' => 60,
                    ),
                ),
            ),
            array(
                'label' => 'User Administration',
                'route' => 'user-admin',
                'order' => 400,
                'class' => 'top-level',
            ),
            array(
                'label' => 'Logout',
                'route' => 'auth/logout',
                'order' => 600,
                'class' => 'top-level',
            ),
        ),
    ),
);
