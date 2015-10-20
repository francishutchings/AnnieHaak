<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class CreateAddController extends AbstractActionController {

    
    public function indexAction() {

        return new ViewModel();
    }

}
