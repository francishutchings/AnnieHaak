<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Controller\AuthController;

class IndexController extends AbstractActionController {

    public function indexAction() {

        $auth = AuthController::getAuthService()->hasIdentity();

        if (!$auth) {
            $this->redirect()->toRoute('auth');
        }

        return new ViewModel();
    }
}