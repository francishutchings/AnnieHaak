<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Products;
use AnnieHaak\Form\ProductsForm;
use AnnieHaak\Model\RatesPercentages;
use AnnieHaak\Model\Auditing;
use Zend\View\Model\JsonModel;
use AnnieHaak\Model\FinancialCalculator;

class ProductsController extends AbstractActionController {

    protected $productsTable;
    protected $collectionsTable;
    protected $productTypesTable;
    protected $rawMaterialsTable;
    protected $labourItemsTable;
    protected $packagingTable;
    protected $ratesPercentagesTable;
    protected $auditingObj;

    public function indexAction() {
        $productActioned = '';
        if (isset($_SESSION['AnnieHaak']['storage']['ProductActioned'])) {
            $productActioned = str_replace(' ', '+', $_SESSION['AnnieHaak']['storage']['ProductActioned']);
            $productActioned = '?_search=true&nd=1454932951823&rows=15&page=1&sidx=ProductName&sord=asc&filters={"groupOp":"AND","rules":[{"field":"ProductName","op":"eq","data":"' . $productActioned . '"}]}&searchField=&searchString=&searchOper=';
            unset($_SESSION['AnnieHaak']['storage']['ProductActioned']);
        }

        return new ViewModel(array(
            'productActioned' => $productActioned
        ));
    }

    public function jsonDataAction() {
        $currentPage = (int) $this->params()->fromQuery('page', 1);
        $sortColumn = $this->params()->fromQuery('sidx', 'ProductName');
        $sortOrder = $this->params()->fromQuery('sord', 'ASC');
        $rows = (int) $this->params()->fromQuery('rows', 15);
        $filters = $this->params()->fromQuery('filters', NULL);

        $sortBy = $sortColumn . ' ' . $sortOrder;

        $search = NULL;

        if (isset($filters) && !empty($filters)) {
            $filters = json_decode($this->params()->fromQuery('filters'));
            $search['groupOp'] = $filters->groupOp;
            foreach ($filters->rules as $value) {
                $temp = array();
                $temp['searchColumn'] = $value->field;
                $temp['searchOper'] = $value->op;
                $temp['searchString'] = $value->data;
                $searchTmp[] = $temp;
            }
            $search['rules'] = $searchTmp;

            foreach ($search['rules'] as $key => $rule) {
                switch ($rule['searchColumn']) {
                    case 'ProductCollectionName':
                        $search['rules'][$key]['searchColumn'] = 'P.CollectionID';
                        break;
                    case 'ProductTypeName':
                        $search['rules'][$key]['searchColumn'] = 'P.ProductTypeID';
                        break;
                }
            }
        }

        $paginator = $this->getProductsTable()->fetchFullDataPaginated($sortBy, $search);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($rows);

        if ($paginator->count() > 0) {
            foreach ($paginator->getItemsByPage($currentPage) as $value) {
                $value->CurrentHTML = ($value->Current) ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-danger"></span>';
                $value->PrintHTML = '<a target="_blank" class="btn btn-info btn-sm" href="/business-admin/products/print/' . $value->ProductID . '"><span class="glyphicon glyphicon-print"></span></a>';
                switch ($_SESSION['AnnieHaak']['storage']['userInfo']['roleLevel']) {
                    case 1:
                        $value->DuplicateHTML = '<a class="btn btn-info btn-sm" href="/business-admin/products/duplicate/' . $value->ProductID . '"><span class="glyphicon glyphicon-duplicate" style="font-size:1.2em;"></span></a>';
                        $value->EditHTML = '<a class="btn btn-warning btn-sm" href="/business-admin/products/edit/' . $value->ProductID . '"><span class="glyphicon glyphicon-pencil"></span></a>';
                        $value->DeleteHTML = '<a class="btn btn-danger btn-sm" href="/business-admin/products/delete/' . $value->ProductID . '"><span class="glyphicon glyphicon-trash"></span></a>';
                        break;
                    case 2:
                        $value->DuplicateHTML = '<a class="btn btn-info btn-sm" href="/business-admin/products/duplicate/' . $value->ProductID . '"><span class="glyphicon glyphicon-duplicate" style="font-size:1.2em;"></span></a>';
                        $value->EditHTML = '<a class="btn btn-warning btn-sm" href="/business-admin/products/edit/' . $value->ProductID . '"><span class="glyphicon glyphicon-pencil"></span></a>';
                        $value->DeleteHTML = '<a class="btn btn-default btn-sm" href="#"><span class="glyphicon glyphicon-ban-circle"></span></a>';
                        break;
                    default:
                        $value->DuplicateHTML = '<a class="btn btn-default btn-sm" href="#"><span class="glyphicon glyphicon-ban-circle"></span></a>';
                        $value->EditHTML = '<a class="btn btn-default btn-sm" href="#"><span class="glyphicon glyphicon-ban-circle"></span></a>';
                        $value->DeleteHTML = '<a class="btn btn-default btn-sm" href="#"><span class="glyphicon glyphicon-ban-circle"></span></a>';
                        break;
                }
                $rawData[] = $value;
            }
        } else {
            $rawData[] = '[]';
        };

        $result = new JsonModel(array(
            'records' => $paginator->getPages()->totalItemCount,
            'page' => $paginator->getPages()->current,
            'total' => $paginator->getPages()->pageCount,
            'rows' => $rawData
        ));
        return $result;
    }

//==================================================================================================================
//==================================================================================================================
    public function addAction() {
        $form = new ProductsForm();

        $ratesPercentages = $this->getRatesPercentagesTable()->getRatesPercentages(1);
        foreach ($ratesPercentages as $key => $value) {
            $ratesPercentagesData[$key] = $value;
        }

        $productNameElemes = $this->getProductsTable()->getProductNameElements();
        $form->get('NameCharm')->setValueOptions($productNameElemes['charms']);
        $form->get('NameCrystal')->setValueOptions($productNameElemes['crystals']);
        $form->get('NameColour')->setValueOptions($productNameElemes['colours']);
        $form->get('NameLength')->setValueOptions($productNameElemes['lengths']);

        $selectData = $this->popSelectMenus();
        $form->get('CollectionID')->setValueOptions($selectData['collectionsData']);
        $form->get('ProductTypeID')->setValueOptions($selectData['productTypesData']);

        $errorMessage = '';
        $duplicationId = 0;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $products = new Products();
            $form->setInputFilter($products->getInputFilter());
            $form->setData($request->getPost());

            $duplicationId = $request->getPost('DuplicationID');

            $dataTestFail = FALSE;
            if (count(json_decode($request->getPost('rawMaterialsGridData'))) == 0) {
                $errorMessage = 'No Raw Materials Picked?';
                $dataTestFail = TRUE;
            }
            if (count(json_decode($request->getPost('packagingGridData'))) == 0) {
                $errorMessage = 'No Packaging Picked?';
                $dataTestFail = TRUE;
            }
            if (count(json_decode($request->getPost('labourItemsGridData'))) == 0) {
                $errorMessage = 'No Labour Items Picked?';
                $dataTestFail = TRUE;
            }

            if ($form->isValid() && !$dataTestFail) {

                foreach (json_decode($request->getPost('rawMaterialsGridData')) as $value) {
                    $rawMaterialsData[] = array(
                        'ProductID' => 0,
                        'RawMaterialID' => $value->RawMaterialID,
                        'RawMaterialQty' => $value->RawMaterialQty
                    );
                }
                foreach (json_decode($request->getPost('packagingGridData')) as $value) {
                    $packagingData[] = array(
                        'ProductID' => 0,
                        'PackagingID' => $value->PackagingID,
                        'PackagingQty' => $value->PackagingQty
                    );
                }
                foreach (json_decode($request->getPost('labourItemsGridData')) as $value) {
                    $labourItemsData[] = array(
                        'ProductID' => 0,
                        'LabourID' => $value->LabourID,
                        'LabourQty' => $value->LabourQty
                    );
                }
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];

                $productAssocData = array(
                    'rawMaterialsData' => $rawMaterialsData,
                    'packagingData' => $packagingData,
                    'labourItemsData' => $labourItemsData
                );

                try {
                    $products->exchangeArray($form->getData());
                    $this->getProductsTable()->saveProducts($products, $auditingObj, $productAssocData);
                    $this->flashmessenger()->setNamespace('info')->addMessage('New product -> ' . $products->ProductName . ' -> Added.');
                    $_SESSION['AnnieHaak']['storage']['ProductActioned'] = $products->ProductName;
                    return $this->redirect()->toRoute('business-admin/products', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    if ($duplicationId) {
                        return $this->redirect()->toRoute('business-admin/products', array('action' => 'duplicate', 'id' => $duplicationId));
                    } else {
                        return $this->redirect()->toRoute('business-admin/products', array('action' => 'add'));
                    }
                }
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'ratesPercentages' => $ratesPercentagesData,
            'errorMessage' => $errorMessage
        ));
    }

//==================================================================================================================
//==================================================================================================================
    public function duplicateAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/products', array('action' => 'add'));
        }

        try {
            $products = $this->getProductsTable()->getProducts($id);
        } catch (\Exception $ex) {
            $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('business-admin/products', array('action' => 'index'));
        }

        $form = new ProductsForm();

        $ratesPercentages = $this->getRatesPercentagesTable()->getRatesPercentages(1);
        foreach ($ratesPercentages as $key => $value) {
            $ratesPercentagesData[$key] = $value;
        }

        $productNameElemes = $this->getProductsTable()->getProductNameElements();
        $form->get('NameCharm')->setValueOptions($productNameElemes['charms']);
        $form->get('NameCrystal')->setValueOptions($productNameElemes['crystals']);
        $form->get('NameColour')->setValueOptions($productNameElemes['colours']);
        $form->get('NameLength')->setValueOptions($productNameElemes['lengths']);

        $selectData = $this->popSelectMenus();
        $form->get('CollectionID')->setValueOptions($selectData['collectionsData']);
        $form->get('ProductTypeID')->setValueOptions($selectData['productTypesData']);

        $form->bind($products);
        $form->get('submit')->setAttribute('value', 'Save Changes');

//Declare New Product
        $form->get('ProductID')->setValue(0);
        $form->get('DuplicationID')->setValue($id);
        $form->get('SKU')->setValue('');

        $form->setAttribute('action', '/business-admin/products/add');

        $view = new ViewModel(array(
            'id' => 0,
            'form' => $form,
            'ratesPercentages' => $ratesPercentagesData
        ));
        $view->setTemplate('annie-haak/products/edit.phtml');
        return $view;
    }

//==================================================================================================================
//==================================================================================================================
    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/products', array('action' => 'add'));
        }

        try {
            $products = $this->getProductsTable()->getProducts($id);
        } catch (\Exception $ex) {
            $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('business-admin/products', array('action' => 'index'));
        }

        $form = new ProductsForm();

        $ratesPercentages = $this->getRatesPercentagesTable()->getRatesPercentages(1);
        foreach ($ratesPercentages as $key => $value) {
            $ratesPercentagesData[$key] = $value;
        }

        $productNameElemes = $this->getProductsTable()->getProductNameElements();
        $form->get('NameCharm')->setValueOptions($productNameElemes['charms']);
        $form->get('NameCrystal')->setValueOptions($productNameElemes['crystals']);
        $form->get('NameColour')->setValueOptions($productNameElemes['colours']);
        $form->get('NameLength')->setValueOptions($productNameElemes['lengths']);

        $selectData = $this->popSelectMenus();
        $form->get('CollectionID')->setValueOptions($selectData['collectionsData']);
        $form->get('ProductTypeID')->setValueOptions($selectData['productTypesData']);

        $form->bind($products);
        $form->get('submit')->setAttribute('value', 'Save Changes');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($products->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                foreach (json_decode($request->getPost('rawMaterialsGridData')) as $value) {
                    $rawMaterialsData[] = array(
                        'ProductID' => $id,
                        'RawMaterialID' => $value->RawMaterialID,
                        'RawMaterialQty' => $value->RawMaterialQty
                    );
                }
                foreach (json_decode($request->getPost('packagingGridData')) as $value) {
                    $packagingData[] = array(
                        'ProductID' => $id,
                        'PackagingID' => $value->PackagingID,
                        'PackagingQty' => $value->PackagingQty
                    );
                }
                foreach (json_decode($request->getPost('labourItemsGridData')) as $value) {
                    $labourItemsData[] = array(
                        'ProductID' => $id,
                        'LabourID' => $value->LabourID,
                        'LabourQty' => $value->LabourQty
                    );
                }
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];

                $productAssocData = array(
                    'rawMaterialsData' => $rawMaterialsData,
                    'packagingData' => $packagingData,
                    'labourItemsData' => $labourItemsData
                );

                try {
                    $this->getProductsTable()->saveProducts($products, $auditingObj, $productAssocData);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Product -> ' . $products->ProductName . ' -> Updated.');
                    return $this->redirect()->toRoute('business-admin/products', array('action' => 'edit', 'id' => $id));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/products', array('action' => 'edit', 'id' => $id));
                }
                return $this->redirect()->toRoute('business-admin/products');
            }
        }

        $form->setAttribute('action', '/business-admin/products/edit/' . $id);
        return new ViewModel(array(
            'id' => $id,
            'form' => $form,
            'ratesPercentages' => $ratesPercentagesData
        ));
    }

//==================================================================================================================
//==================================================================================================================
    public function printAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $cntrlFloatPos = (int) $this->params()->fromQuery('FloatPos', 2);
        if ($cntrlFloatPos < 1 || $cntrlFloatPos > 10) {
            $cntrlFloatPos = 2;
        }
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/products', array('action' => 'index'));
        }
        try {
            $products = $this->getProductsTable()->getProducts($id);
        } catch (\Exception $ex) {
            $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('business-admin/products', array('action' => 'index'));
        }

        $products = (Array) $products;
        $rawMaterials = $this->getRawMaterialsTable()->fetchMaterialsByProduct($id);
        $labourItems = $this->getLabourItemsTable()->getLabourItemsByProduct($id);
        $packaging = $this->getPackagingTable()->getPackagingByProduct($id);

        $ratesPercentages = $this->getRatesPercentagesTable()->getRatesPercentages(1);
        foreach ($ratesPercentages as $key => $value) {
            $ratesPercentagesData[$key] = $value;
        }

        $rawMaterials = $rawMaterials->toArray();
        $subtotal = 0;
        foreach ($rawMaterials as $key => $value) {
            $rawMaterials[$key]["RawMaterialUnitCost"] = number_format((float) $value["RawMaterialUnitCost"], $cntrlFloatPos, '.', '');
            $rawMaterials[$key]["SubtotalRM"] = number_format((float) $value["SubtotalRM"], $cntrlFloatPos, '.', '');
            $subtotal += $rawMaterials[$key]["SubtotalRM"];
        }
        $subtotals['RawMaterials'] = number_format((float) $subtotal, $cntrlFloatPos, '.', '');

        $labourItems = $labourItems->toArray();
        $subtotal = 0;
        foreach ($labourItems as $key => $value) {
            $labourItems[$key]["LabourUnitCost"] = number_format((float) $value["LabourUnitCost"], $cntrlFloatPos, '.', '');
            $labourItems[$key]["SubtotalLabour"] = number_format((float) $value["SubtotalLabour"], $cntrlFloatPos, '.', '');
            $subtotal += $labourItems[$key]["SubtotalLabour"];
        }
        $subtotals['LabourItems'] = number_format((float) $subtotal, $cntrlFloatPos, '.', '');

        $packaging = $packaging->toArray();
        $subtotal = 0;
        $subtotalBag = 0;
        $subtotalBox = 0;
        foreach ($packaging as $key => $value) {
            $packaging[$key]["PackagingUnitCost"] = number_format((float) $value["PackagingUnitCost"], $cntrlFloatPos, '.', '');
            $packaging[$key]["SubtotalPackaging"] = number_format((float) $value["SubtotalPackaging"], $cntrlFloatPos, '.', '');
            $subtotal += $packaging[$key]["SubtotalPackaging"];
            if ($packaging[$key]["PackagingCode"] == 'SBAG' || $packaging[$key]["PackagingCode"] == 'LBAG') {
                $subtotalBag += $packaging[$key]["SubtotalPackaging"];
            }
            if ($packaging[$key]["PackagingCode"] == 'SBOX' || $packaging[$key]["PackagingCode"] == 'LBOX') {
                $subtotalBox += $packaging[$key]["SubtotalPackaging"];
            }
        }
        $subtotals['Packaging']['Total'] = number_format((float) $subtotal, $cntrlFloatPos, '.', '');
        $subtotals['Packaging']['BAG'] = (float) $subtotalBag;
        $subtotals['Packaging']['BOX'] = (float) $subtotalBox;

        $products = (Array) $products;
        $product['RRP'] = $products['RRP'];
        $product['RequiresAssay'] = $products['RequiresAssay'];

        $financialCalcSubTotals = new FinancialCalculator($ratesPercentagesData, $subtotals, $product);
        $financialData = $financialCalcSubTotals->calculateFinancials();
        foreach ($financialData as $key => $value) {
            if ($key != "SubtotalBoxCostTxt") {
                $financialData[$key] = number_format((float) $value, $cntrlFloatPos, '.', '');
            }
        }

        $products['RRP'] = number_format((float) $products['RRP'], $cntrlFloatPos, '.', '');

        $this->layout('layout/print');
        return new ViewModel(array(
            'id' => $id,
            'products' => $products,
            'subtotals' => $subtotals,
            'rawMaterials' => $rawMaterials,
            'labourItems' => $labourItems,
            'packaging' => $packaging,
            'financialCalcSubTotals' => $financialData
        ));
    }

//==================================================================================================================
//==================================================================================================================
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/products', array('action' => 'index'));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getProductsTable()->deleteProducts($id, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Product -> deleted.');
                    return $this->redirect()->toRoute('business-admin/products', array('action' => 'index'));
                } catch (Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/products', array('action' => 'delete', 'id' => $id));
                }
            }
        }

        return new ViewModel(array(
            'id' => $id,
            'products' => $this->getProductsTable()->getProducts($id)
        ));
    }

//==================================================================================================================
//==================================================================================================================
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

    private function getProductsTable() {
        if (!$this->productsTable) {
            $sm = $this->getServiceLocator();
            $this->productsTable = $sm->get('AnnieHaak\Model\ProductsTable');
        }
        return $this->productsTable;
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

    private function getRatesPercentagesTable() {
        if (!$this->ratesPercentagesTable) {
            $sm = $this->getServiceLocator();
            $this->ratesPercentagesTable = $sm->get('AnnieHaak\Model\RatesPercentagesTable');
        }
        return $this->ratesPercentagesTable;
    }

    private function getAuditing() {
        if (!$this->auditingObj) {
            $sm = $this->getServiceLocator();
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $this->auditingObj = new Auditing($dbAdapter);
        }
        return $this->auditingObj;
    }

}
