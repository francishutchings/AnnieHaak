<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class DynamicReportsController extends AbstractActionController {

    
    public function indexAction() {

        return new ViewModel();
    }

}