<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class ReportsController extends AbstractActionController {

    
    public function indexAction() {

        return new ViewModel();
    }

}

