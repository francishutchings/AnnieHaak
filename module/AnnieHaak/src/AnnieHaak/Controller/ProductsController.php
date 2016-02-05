<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Products;
use AnnieHaak\Form\ProductsForm;
use AnnieHaak\Model\RatesPercentages;
use AnnieHaak\Model\RRPCalculator;
use AnnieHaak\Model\Auditing;
use Zend\View\Model\JsonModel;

class ProductsController extends AbstractActionController {

    protected $productsTable;
    protected $collectionsTable;
    protected $productTypesTable;
    protected $rawMaterialsTable;
    protected $ratesPercentagesObj;

    public function indexAction() {
        return new ViewModel(array(
            'products' => $this->getProductsTable()->fetchAll(),
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
        }

        $paginator = $this->getProductsTable()->fetchFullDataPaginated($sortBy, $search);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($rows);

        if ($paginator->count() > 0) {
            foreach ($paginator->getItemsByPage($currentPage) as $value) {
                $value->EditHTML = '<a class="btn btn-warning btn-sm" href="/business-admin/products/edit/' . $value->ProductID . '"><span class="glyphicon glyphicon-pencil"></span></a>';
                $value->DeleteHTML = '<a class="btn btn-danger btn-sm" href="/business-admin/products/delete/' . $value->ProductID . '"><span class="glyphicon glyphicon-trash"></span></a>';
                $value->CurrentHTML = ($value->Current) ? '<span class="glyphicon glyphicon-ok text-success"></span>' : '<span class="glyphicon glyphicon-remove text-danger"></span>';
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

    public function addAction() {
        $form = new ProductsForm();

        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $productNameElemes = $this->getProductsTable()->getProductNameElements($dbAdapter);

        $this->ratesPercentagesObj = new RatesPercentages($dbAdapter);
        $ratesPercentages = $this->ratesPercentagesObj->fetchAll();
        foreach ($ratesPercentages as $key => $value) {
            $ratesPercentagesData[$key] = $value;
        }

        $form->get('NameCharm')->setValueOptions($productNameElemes['charms']);
        $form->get('NameCrystal')->setValueOptions($productNameElemes['crystals']);
        $form->get('NameColour')->setValueOptions($productNameElemes['colours']);
        $form->get('NameLength')->setValueOptions($productNameElemes['lengths']);

        $selectData = $this->popSelectMenus();
        $form->get('CollectionID')->setValueOptions($selectData['collectionsData']);
        $form->get('ProductTypeID')->setValueOptions($selectData['productTypesData']);

        $request = $this->getRequest();
        if ($request->isPost()) {
            dump($request);

            $products = new Products();
            $form->setInputFilter($products->getInputFilter());
            $form->setData($request->getPost());

            dump($form->isValid());
            dump($form->getMessages());
            #exit();

            if ($form->isValid()) {
                dump($form->getData());
                exit();

                $products->exchangeArray($form->getData());
                $this->getProductsTable()->saveProducts($products);
                $this->flashmessenger()->setNamespace('info')->addMessage('product - ' . $products->productName . ' - added.');
                return $this->redirect()->toRoute('business-admin/products');
            }
        }
        return array(
            'form' => $form,
            'ratesPercentages' => $ratesPercentagesData
        );
    }

//==================================================================================================================
    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/products', array(
                        'action' => 'add'
            ));
        }

        try {
            $products = $this->getProductsTable()->getProducts($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('business-admin/products', array(
                        'action' => 'index'
            ));
        }

        #dump($products);
        #exit();

        $form = new ProductsForm();

        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $productNameElemes = $this->getProductsTable()->getProductNameElements($dbAdapter);

        $this->ratesPercentagesObj = new RatesPercentages($dbAdapter);
        $ratesPercentages = $this->ratesPercentagesObj->fetchAll();
        foreach ($ratesPercentages as $key => $value) {
            $ratesPercentagesData[$key] = $value;
        }

        $form->get('NameCharm')->setValueOptions($productNameElemes['charms']);
        $form->get('NameCrystal')->setValueOptions($productNameElemes['crystals']);
        $form->get('NameColour')->setValueOptions($productNameElemes['colours']);
        $form->get('NameLength')->setValueOptions($productNameElemes['lengths']);

        $selectData = $this->popSelectMenus();
        $form->get('CollectionID')->setValueOptions($selectData['collectionsData']);
        $form->get('ProductTypeID')->setValueOptions($selectData['productTypesData']);

        $form->bind($products);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($products->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                #dump($request);
                #dump($form->getData());
                #exit();

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
                $auditingObj = new Auditing($dbAdapter);

                $productAssocData = array(
                    'user' => $_SESSION['AnnieHaak']['storage']['userInfo'],
                    'rawMaterialsData' => $rawMaterialsData,
                    'packagingData' => $packagingData,
                    'labourItemsData' => $labourItemsData,
                    'auditingObj' => $auditingObj,
                    'dbAdapter' => $dbAdapter
                );

                try {
                    $this->getProductsTable()->saveProducts($products, $productAssocData);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Product - ' . $products->ProductName . ' - updated.');
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/products', array(
                                'action' => 'index'
                    ));
                }
                return $this->redirect()->toRoute('business-admin/products');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'ratesPercentages' => $ratesPercentagesData
        );
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

    private function objectToArray($data) {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->objectToArray($value);
            }
            return json_encode($result);
        }
        return $data;
    }

}
