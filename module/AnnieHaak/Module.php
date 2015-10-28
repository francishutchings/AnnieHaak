<?php

namespace AnnieHaak;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use AnnieHaak\Model\Collections;
use AnnieHaak\Model\CollectionsTable;
use AnnieHaak\Model\ProductTypes;
use AnnieHaak\Model\ProductTypesTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module {

    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(
                'route', function($e) {
            $app = $e->getApplication();
            $routeMatch = $e->getRouteMatch();
            $sm = $app->getServiceManager();
            $auth = $sm->get('AuthService');

#var_dump($routeMatch->getMatchedRouteName());
#echo '<hr>';
#var_dump(strpos($routeMatch->getMatchedRouteName(),'login'));
#exit();

            if (!$auth->hasIdentity() && strpos($routeMatch->getMatchedRouteName(), 'login') === FALSE) {
#var_dump(strpos($routeMatch->getMatchedRouteName(),'login') === FALSE);
#exit();
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine(
                        'Location', $e->getRouter()->assemble(
                                array(), array('name' => 'auth')
                        )
                );
                $response->setStatusCode(302);
                return $response;
            }
        }, -100
        );
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
                    return new TableGateway('productcollections', $dbAdapter, null, $resultSetPrototype);
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
            ),
        );
    }

}
