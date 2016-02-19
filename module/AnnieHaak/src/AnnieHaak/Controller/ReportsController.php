<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\ReportCollectionsTypes;
use AnnieHaak\Model\ReportMargins;
use AnnieHaak\Model\ReportByOccasions;
use AnnieHaak\Model\ReportRRPAndRMs;
use AnnieHaak\Model\ReportTradePackRmsTime;
use AnnieHaak\Model\ReportsDynamic;
use AnnieHaak\Form\DynamicReportsForm;

class ReportsController extends AbstractActionController {

    protected $dbAdapter;
    protected $ratesPercentagesTable;
    protected $collectionsTable;
    protected $productTypesTable;

    public function indexAction() {
        return new ViewModel();
    }

    public function dynamicReportsAction() {
        $form = new DynamicReportsForm();

        $selectData = $this->popSelectMenus();
        $form->get('CollectionID')->setValueOptions($selectData['collectionsData']);
        $form->get('ProductTypeID')->setValueOptions($selectData['productTypesData']);

        $reportData = array();
        $formData = array();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $this->getAdapter();
            $ratesPercentages = $this->getRatesPercentagesTable()->getRatesPercentages(1);
            $reportData = new ReportsDynamic($this->dbAdapter, $ratesPercentages);
            $this->layout('layout/print');

            return new ViewModel(array(
                'form' => $form,
                'formData' => $formData,
                'reportData' => $reportData->getReport($formData)
            ));
        }

        return new ViewModel(array(
            'form' => $form,
            'formData' => $formData,
            'reportData' => $reportData
        ));
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

    private function popSelectMenus() {
        $collections = $this->getCollectionsTable()->fetchAll();
        $productTypes = $this->getProductTypesTable()->fetchAll();
        foreach ($collections as $key => $value) {
            $collectionsData[$value->ProductCollectionID] = $value->ProductCollectionName;
        }
        foreach ($productTypes as $key => $value) {
            $productTypesData[$value->ProductTypeId] = $value->ProductTypeName;
        }
        return array(
            'collectionsData' => $collectionsData,
            'productTypesData' => $productTypesData
        );
    }

    private function getCollectionsTable() {
        if (!$this->collectionsTable) {
            $sm = $this->getServiceLocator();
            $this->collectionsTable = $sm->get('AnnieHaak\Model\CollectionsTable');
        }
        return $this->collectionsTable;
    }

    private function getProductTypesTable() {
        if (!$this->productTypesTable) {
            $sm = $this->getServiceLocator();
            $this->productTypesTable = $sm->get('AnnieHaak\Model\ProductTypesTable');
        }
        return $this->productTypesTable;
    }

}
