<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\CollectionsTypesReport;
use AnnieHaak\Model\MarginsReport;

class ReportsController extends AbstractActionController {

    protected $dbAdapter;
    protected $ratesPercentagesTable;

    public function indexAction() {
        return new ViewModel();
    }

    public function collectionsTypesAction() {
        $this->getAdapter();
        return new ViewModel(array(
            'collectionsTypesReport' => new CollectionsTypesReport($this->dbAdapter)
        ));
    }

    public function marginsAction() {
        $this->getAdapter();
        $ratesPercentages = $this->getRatesPercentagesTable()->getRatesPercentages(1);
        $reportData = new MarginsReport($this->dbAdapter, $ratesPercentages);
        $this->layout('layout/print');
        return new ViewModel(array(
            'marginsReport' => $reportData->getReport()
        ));
    }

    private function getRatesPercentagesTable() {
        if (!$this->ratesPercentagesTable) {
            $sm = $this->getServiceLocator();
            $this->ratesPercentagesTable = $sm->get('AnnieHaak\Model\RatesPercentagesTable');
        }
        return $this->ratesPercentagesTable;
    }

    public function getAdapter() {
        if (!$this->dbAdapter) {
            $sm = $this->getServiceLocator();
            $this->dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        }
        return $this->dbAdapter;
    }

}
