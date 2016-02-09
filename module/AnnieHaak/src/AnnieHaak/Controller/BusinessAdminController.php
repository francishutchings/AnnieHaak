<?php

namespace AnnieHaak\Controller;

use AnnieHaak\Model\FinancialCalculator;
use AnnieHaak\Model\RatesPercentages;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class BusinessAdminController extends AbstractActionController {

    protected $productsTable;
    protected $rawMaterialsTable;
    protected $labourItemsTable;
    protected $packagingTable;

    public function indexAction() {
        return new ViewModel();
    }

    public function runFinancialCalcAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $finDataJSON = '';
        if ($id) {

            try {
                $products = $this->getProductsTable()->getProducts($id);
            } catch (\Exception $ex) {
                return $this->redirect()->toRoute('business-admin/products', array('action' => 'index'));
            }

            $sm = $this->getServiceLocator();
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $ratesPercentagesObj = new RatesPercentages($dbAdapter);
            $ratesPercentages = $ratesPercentagesObj->fetchAll();
            foreach ($ratesPercentages as $key => $value) {
                $ratesPercentagesData[$key] = $value;
            }

            $rawMaterials = $this->getRawMaterialsTable()->fetchMaterialsByProduct($id);
            $labourItems = $this->getLabourItemsTable()->getLabourItemsByProduct($id);
            $packaging = $this->getPackagingTable()->getPackagingByProduct($id);

            $rawMaterials = $rawMaterials->toArray();
            $subtotal = 0;
            foreach ($rawMaterials as $key => $value) {
                $subtotal += $rawMaterials[$key]["SubtotalRM"];
            }
            $subtotals['RawMaterials'] = (float) $subtotal;

            $labourItems = $labourItems->toArray();
            $subtotal = 0;
            foreach ($labourItems as $key => $value) {
                $subtotal += $labourItems[$key]["SubtotalLabour"];
            }
            $subtotals['LabourItems'] = (float) $subtotal;

            $packaging = $packaging->toArray();
            $subtotalBag = 0;
            $subtotalBox = 0;
            foreach ($packaging as $key => $value) {
                if ($packaging[$key]["PackagingCode"] == 'SBAG' || $packaging[$key]["PackagingCode"] == 'LBAG') {
                    $subtotalBag += $packaging[$key]["SubtotalPackaging"];
                }
                if ($packaging[$key]["PackagingCode"] == 'SBOX' || $packaging[$key]["PackagingCode"] == 'LBOX') {
                    $subtotalBox += $packaging[$key]["SubtotalPackaging"];
                }
            }
            $subtotals['Packaging']['BAG'] = (float) $subtotalBag;
            $subtotals['Packaging']['BOX'] = (float) $subtotalBox;

            $products = (Array) $products;
            $product['RRP'] = $products['RRP'];
            $product['RequiresAssay'] = $products['RequiresAssay'];

            $financialCalculator = new FinancialCalculator($ratesPercentagesData, $subtotals, $product);

            $finDataJSON = $financialCalculator->calculateFinancials();
        }

        $result = new ViewModel(array(
            'finDataJSON' => $finDataJSON
        ));
        return $result;
    }

    private function getProductsTable() {
        if (!$this->productsTable) {
            $sm = $this->getServiceLocator();
            $this->productsTable = $sm->get('AnnieHaak\Model\ProductsTable');
        }
        return $this->productsTable;
    }

    private function getRawMaterialsTable() {
        if (!$this->rawMaterialsTable) {
            $sm = $this->getServiceLocator();
            $this->rawMaterialsTable = $sm->get('AnnieHaak\Model\RawMaterialsTable');
        }
        return $this->rawMaterialsTable;
    }

    public function getLabourItemsTable() {
        if (!$this->labourItemsTable) {
            $sm = $this->getServiceLocator();
            $this->labourItemsTable = $sm->get('AnnieHaak\Model\LabourItemsTable');
        }
        return $this->labourItemsTable;
    }

    public function getPackagingTable() {
        if (!$this->packagingTable) {
            $sm = $this->getServiceLocator();
            $this->packagingTable = $sm->get('AnnieHaak\Model\PackagingTable');
        }
        return $this->packagingTable;
    }

}
