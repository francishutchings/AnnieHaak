<?php

namespace AnnieHaak;

return array(
    'router' => array(
        'routes' => array(
            '' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'AnnieHaak\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Application\Controller'
                            )
                        )
                    )
                )
            ),
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/home',
                    'defaults' => array(
                        'controller' => 'AnnieHaak\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Application\Controller'
                            )
                        )
                    )
                )
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
                            'defaults' => array(),
                        ),
                    ),
                ),
            ),
            'business-admin' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/business-admin',
                    'defaults' => array(
                        'controller' => 'AnnieHaak\Controller\BusinessAdmin',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'collections' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/collections[/:action][/:id]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\Collections',
                                'action' => 'index'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        ),
                    ),
                    'labour-items' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/labour-items[/:action][/:id]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\LabourItems',
                                'action' => 'index'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        ),
                    ),
                    'packaging' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/packaging[/:action][/:id]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\Packaging',
                                'action' => 'index'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        ),
                    ),
                    'products' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/products[/:action][/:id]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\Products',
                                'action' => 'index'
                            ),
                            'constraints' => array(
                                'id' => '[0-9]\d*'
                            )
                        ),
                    ),
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
                    'rates-percentages' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/rates-percentages[/:action]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\RatesPercentages',
                                'action' => 'index'
                            ),
                        ),
                    ),
                    'raw-materials' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/raw-materials[/:action][/:id]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\RawMaterials',
                                'action' => 'index'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        ),
                    ),
                    'raw-material-types' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/raw-material-types[/:action][/:id]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\RawMaterialTypes',
                                'action' => 'index'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        ),
                    ),
                    'suppliers' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/suppliers[/:action][/:id]',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\Suppliers',
                                'action' => 'index'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        ),
                    ),
                ),
            ),
            'business-reports' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/business-reports',
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
                                'controller' => 'AnnieHaak\Controller\Reports',
                                'action' => 'dynamicReports'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        ),
                    ),
                    'collections-types-report' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/collections-types-report',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\Reports',
                                'action' => 'collectionsTypes'
                            ),
                        ),
                    ),
                    'margins-report' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/margins-report',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\Reports',
                                'action' => 'margins'
                            ),
                        ),
                    ),
                    'products-by-occasions-report' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/products-by-occasions-report',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\Reports',
                                'action' => 'byOccasions'
                            ),
                        ),
                    ),
                    'rrp-rms-report' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/rrp-rms-report',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\Reports',
                                'action' => 'rrpAndRms'
                            ),
                        ),
                    ),
                    'trade-pack-rms-time-report' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/trade-pack-rms-time-report',
                            'defaults' => array(
                                'controller' => 'AnnieHaak\Controller\Reports',
                                'action' => 'tradePackRmsTime'
                            ),
                        ),
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
                            'defaults' => array(),
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
        'locale' => 'en_UK',
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
            'AnnieHaak\Controller\BusinessAdmin' => Controller\BusinessAdminController::class,
            'AnnieHaak\Controller\Collections' => Controller\CollectionsController::class,
            'AnnieHaak\Controller\LabourItems' => Controller\LabourItemsController::class,
            'AnnieHaak\Controller\Packaging' => Controller\PackagingController::class,
            'AnnieHaak\Controller\Products' => Controller\ProductsController::class,
            'AnnieHaak\Controller\ProductTypes' => Controller\ProductTypesController::class,
            'AnnieHaak\Controller\RatesPercentages' => Controller\RatesPercentagesController::class,
            'AnnieHaak\Controller\RawMaterials' => Controller\RawMaterialsController::class,
            'AnnieHaak\Controller\RawMaterialTypes' => Controller\RawMaterialTypesController::class,
            'AnnieHaak\Controller\Suppliers' => Controller\SuppliersController::class,
            'AnnieHaak\Controller\Reports' => Controller\ReportsController::class
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'forbidden_template' => 'error/403',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'AnnieHaak/index/index' => __DIR__ . '/../view/AnnieHaak/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/403' => __DIR__ . '/../view/error/403.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'UIAccessControlButtons' => 'AnnieHaak\View\Helpers\UIAccessControlButtons',
            'UIPageTitleFormatter' => 'AnnieHaak\View\Helpers\UIPageTitleFormatter'
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Main Menu',
                'route' => 'home',
                'order' => -100,
                'class' => 'top-level',
            ),
            array(
                'label' => 'Business Admin',
                'route' => 'business-admin',
                'order' => 200,
                'class' => 'top-level',
                'pages' => array(
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Collections',
                        'route' => 'business-admin/collections',
                        'contoller' => 'CollectionsController',
                        'action' => 'index',
                        'order' => 10,
                        'pages' => array(
                            array(
                                'label' => '<span class="glyphicon glyphicon-plus"></span> Add new Collection',
                                'route' => 'business-admin/collections',
                                'contoller' => 'CollectionsController',
                                'action' => 'add',
                                'order' => 10,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-pencil"></span>  Edit Collection',
                                'route' => 'business-admin/collections',
                                'contoller' => 'CollectionsController',
                                'action' => 'edit',
                                'order' => 20,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-trash"></span> Delete this Collection?',
                                'route' => 'business-admin/collections',
                                'contoller' => 'CollectionsController',
                                'action' => 'delete',
                                'order' => 30,
                            ),
                        ),
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Labour Items',
                        'route' => 'business-admin/labour-items',
                        'contoller' => 'LabourItemsController',
                        'action' => 'index',
                        'order' => 20,
                        'pages' => array(
                            array(
                                'label' => '<span class="glyphicon glyphicon-plus"></span> Add new Labour Item',
                                'route' => 'business-admin/labour-items',
                                'contoller' => 'LabourItemsController',
                                'action' => 'add',
                                'order' => 10,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-pencil"></span> Edit Labour Item',
                                'route' => 'business-admin/labour-items',
                                'contoller' => 'LabourItemsController',
                                'action' => 'edit',
                                'order' => 20,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-trash"></span> Delete this Labour Item?',
                                'route' => 'business-admin/labour-items',
                                'contoller' => 'LabourItemsController',
                                'action' => 'delete',
                                'order' => 30,
                            ),
                        ),
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Packaging',
                        'route' => 'business-admin/packaging',
                        'contoller' => 'BusinessAdminController',
                        'action' => 'index',
                        'order' => 30,
                        'pages' => array(
                            array(
                                'label' => '<span class="glyphicon glyphicon-plus"></span> Add new Packaging',
                                'route' => 'business-admin/packaging',
                                'contoller' => 'PackagingController',
                                'action' => 'add',
                                'order' => 10,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-pencil"></span> Edit Packaging',
                                'route' => 'business-admin/packaging',
                                'contoller' => 'PackagingController',
                                'action' => 'edit',
                                'order' => 20,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-trash"></span> Delete this Packaging?',
                                'route' => 'business-admin/packaging',
                                'contoller' => 'PackagingController',
                                'action' => 'delete',
                                'order' => 30,
                            ),
                        ),
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Products',
                        'route' => 'business-admin/products',
                        'contoller' => 'BusinessAdminController',
                        'action' => 'index',
                        'order' => 40,
                        'pages' => array(
                            array(
                                'label' => '<span class="glyphicon glyphicon-plus"></span> Add new Product',
                                'route' => 'business-admin/products',
                                'contoller' => 'ProductsController',
                                'action' => 'add',
                                'order' => 10,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-pencil"></span> Edit Product',
                                'route' => 'business-admin/products',
                                'contoller' => 'ProductsController',
                                'action' => 'edit',
                                'order' => 20,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-trash"></span> Delete Product',
                                'route' => 'business-admin/products',
                                'contoller' => 'ProductsController',
                                'action' => 'delete',
                                'order' => 30,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-print"></span> Print Product',
                                'route' => 'business-admin/products',
                                'contoller' => 'ProductsController',
                                'action' => 'print',
                                'order' => 40,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-duplicate"></span> Duplicate Product',
                                'route' => 'business-admin/products',
                                'contoller' => 'ProductsController',
                                'action' => 'duplicate',
                                'order' => 50,
                            ),
                        ),
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Product Types',
                        'route' => 'business-admin/product-types',
                        'contoller' => 'ProductTypesController',
                        'action' => 'index',
                        'order' => 50,
                        'pages' => array(
                            array(
                                'label' => '<span class="glyphicon glyphicon-plus"></span> Add new Product Type',
                                'route' => 'business-admin/product-types',
                                'contoller' => 'ProductTypesController',
                                'action' => 'add',
                                'order' => 10,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-pencil"></span> Edit Product Type',
                                'route' => 'business-admin/product-types',
                                'contoller' => 'ProductTypesController',
                                'action' => 'edit',
                                'order' => 20,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-trash"></span> Delete this Product Type?',
                                'route' => 'business-admin/product-types',
                                'contoller' => 'ProductTypesController',
                                'action' => 'delete',
                                'order' => 30,
                            ),
                        ),
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Rates and Percentages',
                        'route' => 'business-admin/rates-percentages',
                        'contoller' => 'BusinessAdminController',
                        'action' => 'index',
                        'order' => 60,
                        'pages' => array(
                            array(
                                'label' => '<span class="glyphicon glyphicon-pencil"></span> Edit Rates and Percentages',
                                'route' => 'business-admin/rates-percentages',
                                'contoller' => 'BusinessAdminController',
                                'action' => 'edit',
                                'order' => 10,
                            ),
                        ),
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Raw Materials',
                        'route' => 'business-admin/raw-materials',
                        'contoller' => 'BusinessAdminController',
                        'action' => 'index',
                        'order' => 70,
                        'pages' => array(
                            array(
                                'label' => '<span class="glyphicon glyphicon-plus"></span> Add new Raw Material',
                                'route' => 'business-admin/raw-materials',
                                'contoller' => 'RawMaterialsController',
                                'action' => 'add',
                                'order' => 10,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-pencil"></span> Edit Raw Material',
                                'route' => 'business-admin/raw-materials',
                                'contoller' => 'RawMaterialsController',
                                'action' => 'edit',
                                'order' => 20,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-trash"></span> Delete this Raw Material?',
                                'route' => 'business-admin/raw-materials',
                                'contoller' => 'RawMaterialsController',
                                'action' => 'delete',
                                'order' => 30,
                            ),
                        ),
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Raw Material Types',
                        'route' => 'business-admin/raw-material-types',
                        'contoller' => 'BusinessAdminController',
                        'action' => 'index',
                        'order' => 80,
                        'pages' => array(
                            array(
                                'label' => '<span class="glyphicon glyphicon-plus"></span> Add new Raw Material Type',
                                'route' => 'business-admin/raw-material-types',
                                'contoller' => 'RawMaterialTypesController',
                                'action' => 'add',
                                'order' => 10,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-pencil"></span> Edit Raw Material Type',
                                'route' => 'business-admin/raw-material-types',
                                'contoller' => 'RawMaterialTypesController',
                                'action' => 'edit',
                                'order' => 20,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-trash"></span> Delete this Raw Material Type?',
                                'route' => 'business-admin/raw-material-types',
                                'contoller' => 'RawMaterialTypesController',
                                'action' => 'delete',
                                'order' => 30,
                            ),
                        ),
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Suppliers',
                        'route' => 'business-admin/suppliers',
                        'contoller' => 'BusinessAdminController',
                        'action' => 'index',
                        'order' => 90,
                        'pages' => array(
                            array(
                                'label' => '<span class="glyphicon glyphicon-plus"></span> Add new Supplier',
                                'route' => 'business-admin/suppliers',
                                'contoller' => 'SuppliersController',
                                'action' => 'add',
                                'order' => 10,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-pencil"></span> Edit Supplier',
                                'route' => 'business-admin/suppliers',
                                'contoller' => 'SuppliersController',
                                'action' => 'edit',
                                'order' => 20,
                            ),
                            array(
                                'label' => '<span class="glyphicon glyphicon-trash"></span> Delete this Supplier?',
                                'route' => 'business-admin/suppliers',
                                'contoller' => 'SuppliersController',
                                'action' => 'delete',
                                'order' => 30,
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'label' => 'Business Reports',
                'route' => 'business-reports',
                'order' => 300,
                'class' => 'top-level',
                'pages' => array(
                    array(
                        'label' => '<span class="glyphicon glyphicon-list"></span> Dynamic Reports',
                        'route' => 'business-reports/dynamic-reports',
                        'contoller' => 'ReportsController',
                        'action' => 'dynamicReports',
                        'order' => 10,
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-print"></span> Collections & Types Report',
                        'route' => 'business-reports/collections-types-report',
                        'contoller' => 'ReportsController',
                        'action' => 'collectionsTypes',
                        'order' => 20,
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-print"></span> Margins Report',
                        'route' => 'business-reports/margins-report',
                        'contoller' => 'ReportsController',
                        'action' => 'margins',
                        'order' => 30,
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-print"></span> Products By Occasions',
                        'route' => 'business-reports/products-by-occasions-report',
                        'contoller' => 'ReportsController',
                        'action' => 'byOccasions',
                        'order' => 40,
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-print"></span> RRP & RMs',
                        'route' => 'business-reports/rrp-rms-report',
                        'contoller' => 'ReportsController',
                        'action' => 'rrpAndRms',
                        'order' => 50,
                    ),
                    array(
                        'label' => '<span class="glyphicon glyphicon-print"></span> Trade Pack RMs + Time',
                        'route' => 'business-reports/trade-pack-rms-time-report',
                        'contoller' => 'ReportsController',
                        'action' => 'tradePackRmsTime',
                        'order' => 60,
                    ),
                ),
            ),
            array(
                'label' => 'User Admin',
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
