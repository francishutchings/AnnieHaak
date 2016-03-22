<?php

namespace AnnieHaak;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use AnnieHaak\Model\Collections;
use AnnieHaak\Model\CollectionsTable;
use AnnieHaak\Model\LabourItems;
use AnnieHaak\Model\LabourItemsTable;
use AnnieHaak\Model\Packaging;
use AnnieHaak\Model\PackagingTable;
use AnnieHaak\Model\ProductTypes;
use AnnieHaak\Model\ProductTypesTable;
use AnnieHaak\Model\Products;
use AnnieHaak\Model\ProductsTable;
use AnnieHaak\Model\RatesPercentages;
use AnnieHaak\Model\RatesPercentagesTable;
use AnnieHaak\Model\RawMaterials;
use AnnieHaak\Model\RawMaterialsTable;
use AnnieHaak\Model\RawMaterialTypes;
use AnnieHaak\Model\RawMaterialTypesTable;
use AnnieHaak\Model\Suppliers;
use AnnieHaak\Model\SuppliersTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module {

    public function onBootstrap(MvcEvent $e) {

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function($e) {
            if ($_SERVER['SERVER_ADDR'] != '127.0.0.1') {
                $catchStates = array('error-router-no-match', 'error-controller-not-found');
                if (array_search($e->getParam('error'), $catchStates)) {
                    $response = $e->getResponse();
                    $response->getHeaders()->addHeaderLine(
                            'Location', $e->getRouter()->assemble(array(), array('name' => 'home'))
                    );
                    $response->setStatusCode(302);
                    return $response;
                }
            }
        });

        $eventManager->attach('route', function($e) {
            $app = $e->getApplication();
            $routeMatch = $e->getRouteMatch();
            $sm = $app->getServiceManager();
            $auth = $sm->get('AuthService');
            $app->getEventManager()->attach('render', array($this, 'setLayoutTitle'));

            if (!$auth->hasIdentity() && strpos($routeMatch->getMatchedRouteName(), 'login') === FALSE) {
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $e->getRouter()->assemble(array(), array('name' => 'auth')));
                $response->setStatusCode(302);
                return $response;
            }
        }, -100);

        $this->initAcl($e);
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAcl'));
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig() {
        /*
         * http://stackoverflow.com/questions/15947224/when-to-use-tablegateway-and-adapter
         */
        return array(
            'factories' => array(
                'AuthService' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'Users', 'username', 'password', 'MD5(?)');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Auth\Model\MyAuthStorage'));
                    return $authService;
                },
                'AnnieHaak\Model\CollectionsTable' => function($sm) {
                    $tableGateway = $sm->get('CollectionsGateway');
                    $table = new CollectionsTable($tableGateway);
                    return $table;
                },
                'CollectionsGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Collections());
                    return new TableGateway('ProductCollections', $dbAdapter, null, $resultSetPrototype);
                },
                'AnnieHaak\Model\LabourItemsTable' => function($sm) {
                    $tableGateway = $sm->get('LabourItemsGateway');
                    $table = new LabourItemsTable($tableGateway);
                    return $table;
                },
                'LabourItemsGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LabourItems());
                    return new TableGateway('LabourLookup', $dbAdapter, null, $resultSetPrototype);
                },
                'AnnieHaak\Model\PackagingTable' => function($sm) {
                    $tableGateway = $sm->get('PackagingGateway');
                    $table = new PackagingTable($tableGateway);
                    return $table;
                },
                'PackagingGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Packaging());
                    return new TableGateway('PackagingLookup', $dbAdapter, null, $resultSetPrototype);
                },
                'AnnieHaak\Model\ProductsTable' => function($sm) {
                    $tableGateway = $sm->get('ProductsGateway');
                    $table = new ProductsTable($tableGateway);
                    return $table;
                },
                'ProductsGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Products());
                    return new TableGateway('Products', $dbAdapter, null, $resultSetPrototype);
                },
                'AnnieHaak\Model\ProductTypesTable' => function($sm) {
                    $tableGateway = $sm->get('ProductTypesGateway');
                    $table = new ProductTypesTable($tableGateway);
                    return $table;
                },
                'ProductTypesGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProductTypes());
                    return new TableGateway('ProductTypes', $dbAdapter, null, $resultSetPrototype);
                },
                'AnnieHaak\Model\RatesPercentagesTable' => function($sm) {
                    $tableGateway = $sm->get('RatesPercentagesGateway');
                    $table = new RatesPercentagesTable($tableGateway);
                    return $table;
                },
                'RatesPercentagesGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RatesPercentages());
                    return new TableGateway('RatesPercentages', $dbAdapter, null, $resultSetPrototype);
                },
                'AnnieHaak\Model\RawMaterialsTable' => function($sm) {
                    $tableGateway = $sm->get('RawMaterialsGateway');
                    $table = new RawMaterialsTable($tableGateway);
                    return $table;
                },
                'RawMaterialsGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RawMaterials());
                    return new TableGateway('RawMaterialLookup', $dbAdapter, null, $resultSetPrototype);
                },
                'AnnieHaak\Model\RawMaterialTypesTable' => function($sm) {
                    $tableGateway = $sm->get('RawMaterialTypesGateway');
                    $table = new RawMaterialTypesTable($tableGateway);
                    return $table;
                },
                'RawMaterialTypesGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RawMaterialTypes());
                    return new TableGateway('RawMaterialTypeLookup', $dbAdapter, null, $resultSetPrototype);
                },
                'AnnieHaak\Model\SuppliersTable' => function($sm) {
                    $tableGateway = $sm->get('SuppliersGateway');
                    $table = new SuppliersTable($tableGateway);
                    return $table;
                },
                'SuppliersGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Suppliers());
                    return new TableGateway('RawMaterialSupplierLookup', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

    public function setLayoutTitle($e) {
        $matches = $e->getRouteMatch();

        $action = $matches->getParam('action');
        $routeName = $matches->getMatchedRouteName();
        $id = $matches->getParam('id');
        $controller = $matches->getParam('controller');
        $module = __NAMESPACE__;
        $siteName = 'Edith Administration';

        $routeNameArr = explode('/', $routeName);
        $routeName = ucwords(str_replace('-', ' ', end($routeNameArr)));

        // Getting the view helper manager from the application service manager
        $viewHelperManager = $e->getApplication()->getServiceManager()->get('viewHelperManager');

        // Getting the headTitle helper from the view helper manager
        $headTitleHelper = $viewHelperManager->get('headTitle');

        // Setting a separator string for segments
        $headTitleHelper->setSeparator(' - ');

        // Setting the action, controller, module and site name as title segments
        $headTitleHelper->append($siteName);
        $headTitleHelper->append($routeName);
        $headTitleHelper->append(ucwords($action));
        $headTitleHelper->append($id);
    }

    /**
     * @description Initialise ACL for all modules/controllers/actions
     * @param MvcEvent $e
     * @link http://ivangospodinow.com/zend-framework-2-acl-setup-in-5-minutes-tutorial/ Original example
     */
    public function initAcl(MvcEvent $e) {

        $acl = new \Zend\Permissions\Acl\Acl();
        $roles = include __DIR__ . '/config/module.acl.roles.php';
        $allResources = array();
        foreach ($roles as $role => $resources) {

            $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
            $acl->addRole($role);

            $allResources = array_merge($resources, $allResources);
//Resources
            foreach ($resources as $resource) {
                if (!$acl->hasResource($resource)) {
                    $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
                }
            }
//Restrictions
            foreach ($allResources as $resource) {
                $acl->allow($role, $resource);
            }
        }
//setting to view
        $e->getViewModel()->acl = $acl;
    }

    /**
     * @description Check User has the correct permissions to view this page
     * @param MvcEvent $e
     */
    public function checkAcl(MvcEvent $e) {
        $matches = $e->getRouteMatch();
        $action = $matches->getParam('action');
        $controller = explode("\\", $matches->getParam('controller'));

        $route = $controller[2] . '/' . $action;

        $sessData = $e->getApplication()->getServiceManager()->get('AuthService')->getStorage()->read();

        if (isset($sessData["userInfo"]["roleLevel"])) {
            switch ($sessData["userInfo"]["roleLevel"]) {
                case 1:
                    $userRole = 'admin';
                    break;
                case 2:
                    $userRole = 'staff';
                    break;
                case 3:
                    $userRole = 'guest';
                    break;
            }
        } else {
            $userRole = 'guest';
        }

        #dump($route);
        #dump($matches);
        #dump($userRole);
        #dump($e->getViewModel()->acl->hasResource($route));
        #dump($e->getViewModel()->acl->isAllowed($userRole, $route));
        #exit();

        if (!$e->getViewModel()->acl->hasResource($route) || !$e->getViewModel()->acl->isAllowed($userRole, $route)) {
            $response = $e->getResponse();
            $response->setStatusCode(403);
            $response->setContent('<html><body><h1>403 - Access Denied</h1></body></html>');
            return $response;
        }
    }

}
