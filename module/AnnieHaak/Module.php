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
use AnnieHaak\Model\RawMaterials;
use AnnieHaak\Model\RawMaterialsTable;
use AnnieHaak\Model\RawMaterialTypes;
use AnnieHaak\Model\RawMaterialTypesTable;
use AnnieHaak\Model\ProductTypes;
use AnnieHaak\Model\ProductTypesTable;
use AnnieHaak\Model\Suppliers;
use AnnieHaak\Model\SuppliersTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module {

    public function onBootstrap(MvcEvent $e) {

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach('route', function($e) {
            $app = $e->getApplication();
            $routeMatch = $e->getRouteMatch();
            $sm = $app->getServiceManager();
            $auth = $sm->get('AuthService');

            if (!$auth->hasIdentity() && strpos($routeMatch->getMatchedRouteName(), 'login') === FALSE) {
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine(
                        'Location', $e->getRouter()->assemble(
                                array(), array('name' => 'auth')
                        )
                );
                $response->setStatusCode(302);
                return $response;
            }
        }, -100);


        #dump($eventManager);
        #exit();
        #$this->initAcl($e); //Initialise the ACL
        #$eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAcl')); //Acl check
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
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'users', 'username', 'password', 'MD5(?)');

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
                'AnnieHaak\Model\RawMaterialsTable' => function($sm) {
                    $tableGateway = $sm->get('RawMaterialsGateway');
                    $table = new RawMaterialsTable($tableGateway);
                    return $table;
                },
                'RawMaterialsGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RawMaterials());
                    return new TableGateway('rawmateriallookup', $dbAdapter, null, $resultSetPrototype);
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
                    return new TableGateway('rawmaterialtypelookup', $dbAdapter, null, $resultSetPrototype);
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
                    return new TableGateway('rawmaterialsupplierlookup', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

    /**
     * @description Initialise ACL for all modules/controllers/actions
     * @param MvcEvent $e
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
            foreach ($resources as $resource) {
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
                    $userRole = 'member';
                    break;
                case 3:
                    $userRole = 'guest';
                    break;
            }
        } else {
            $userRole = 'guest';
        }

        #$userRole = 'guest';
        #dump($e->getViewModel()->acl);
        #dump($route);
        #dump($userRole);
        #dump($e->getViewModel()->acl->hasResource($route));
        #dump($e->getViewModel()->acl->isAllowed($userRole, $route));
        #exit();
        #dump($e->getViewModel());
        #dump($e->getViewModel()->acl->hasResource($route));
        #dump($e->getViewModel()->acl);
        /*
          if (!$e->getViewModel()->acl->hasResource($route) || !$e->getViewModel()->acl->isAllowed($userRole, $route)) {
          //Naughty trying to get somewhere they shouldn't (Clear there identity force them to login again)
          $response = $e->getResponse();
          //location to page or what ever
          $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/403');
          $response->setStatusCode(403);
          }
         *
         */
    }

}
