<?php

namespace AnnieHaak;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
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
                    if (!$auth->hasIdentity() && $routeMatch->getMatchedRouteName() != 'login') {
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
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'users', 'user_name', 'pass_word', 'MD5(?)');

                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Auth\Model\MyAuthStorage'));

                    return $authService;
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
