<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\CollectionsTypesReport;
use AnnieHaak\Model\MarginsReport;

class ReportsController extends AbstractActionController {

    protected $dbAdapter;

    public function indexAction() {
        return new ViewModel();
    }

    public function getAdapter() {
        if (!$this->dbAdapter) {
            $sm = $this->getServiceLocator();
            $this->dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        }
        return $this->dbAdapter;
    }

    public function collectionsTypesAction() {
        $this->getAdapter();
        return new ViewModel(array(
            'collectionsTypesReport' => new CollectionsTypesReport($this->dbAdapter)
        ));
    }

    public function marginsAction() {
        $this->getAdapter();
        return new ViewModel(array(
            'marginsReport' => new MarginsReport($this->dbAdapter)
        ));
    }

}
