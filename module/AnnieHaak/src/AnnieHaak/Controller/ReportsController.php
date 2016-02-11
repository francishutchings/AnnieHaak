<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\ReportCollectionsTypes;
use AnnieHaak\Model\ReportMargins;
use AnnieHaak\Model\ReportByOccasions;
use AnnieHaak\Model\ReportRRPAndRMs;
use AnnieHaak\Model\ReportTradePackRmsTime;

class ReportsController extends AbstractActionController {

    protected $dbAdapter;
    protected $ratesPercentagesTable;

    public function indexAction() {
        return new ViewModel();
    }

    public function collectionsTypesAction() {
        $this->getAdapter();
        $collectionsTypes = new ReportCollectionsTypes($this->dbAdapter);
        $this->layout('layout/print');
        return new ViewModel(array(
            'collectionsTypesReport' => $collectionsTypes->getReport()
        ));
    }

    public function marginsAction() {
        $this->getAdapter();
        $ratesPercentages = $this->getRatesPercentagesTable()->getRatesPercentages(1);
        $reportData = new ReportMargins($this->dbAdapter, $ratesPercentages);
        $this->layout('layout/print');
        return new ViewModel(array(
            'marginsReport' => $reportData->getReport()
        ));
    }

    public function byOccasionsAction() {
        $this->getAdapter();
        $reportData = new ReportByOccasions($this->dbAdapter);
        $this->layout('layout/print');
        return new ViewModel(array(
            'occasionsReport' => $reportData->getReport()
        ));
    }

    public function rrpAndRmsAction() {
        $this->getAdapter();
        $reportData = new ReportRRPAndRMs($this->dbAdapter);
        $this->layout('layout/print');
        return new ViewModel(array(
            'RRPAndRMsReport' => $reportData->getReport()
        ));
    }

    public function tradePackRmsTimeAction() {
        $this->getAdapter();
        $reportData = new ReportTradePackRmsTime($this->dbAdapter);
        $this->layout('layout/print');
        return new ViewModel(array(
            'tradePackRmsTimeReport' => $reportData->getReport()
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
