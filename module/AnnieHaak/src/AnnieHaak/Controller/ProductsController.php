<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Products;
use AnnieHaak\Form\ProductsForm;
use Zend\View\Model\JsonModel;

class ProductsController extends AbstractActionController {

    protected $productsTable;
    protected $collectionsTable;
    protected $productTypesTable;
    protected $rawMaterialsTable;

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

        $selectData = $this->popSelectMenus();
        $form->get('CollectionID')->setValueOptions($selectData['collectionsData']);
        $form->get('ProductTypeID')->setValueOptions($selectData['productTypesData']);



        $request = $this->getRequest();
        if ($request->isPost()) {
            $products = new Products();
            $form->setInputFilter($products->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $products->exchangeArray($form->getData());
                $this->getProductsTable()->saveProducts($products);
                $this->flashmessenger()->setNamespace('info')->addMessage('product - ' . $products->productName . ' - added.');
                return $this->redirect()->toRoute('business-admin/products');
            }
        }
        return array(
            'form' => $form,
            'rawMaterials' => $rawMaterials
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

}
