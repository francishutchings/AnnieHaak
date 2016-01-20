<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\RawMaterials;
use AnnieHaak\Form\RawMaterialsForm;
use Zend\View\Model\JsonModel;

class RawMaterialsController extends AbstractActionController {

    protected $rawMaterialsTable;
    protected $rawMaterialTypesTable;
    protected $suppliersTable;
    protected $productsTable;

    public function indexAction() {
        return new ViewModel();
    }

    public function jsonDataAction() {
        $currentPage = (int) $this->params()->fromQuery('page', 1);
        $sortColumn = $this->params()->fromQuery('sidx', 'RawMaterialCode');
        $sortOrder = $this->params()->fromQuery('sord', 'asc');
        $rows = (int) $this->params()->fromQuery('rows', 15);

        $sortBy = $sortColumn . ' ' . $sortOrder;

        $search = NULL;
        if ((Boolean) $this->params()->fromQuery('_search', FALSE)) {
            $search['searchColumn'] = $this->params()->fromQuery('searchField');
            $search['searchOper'] = $this->params()->fromQuery('searchOper');
            $search['searchString'] = $this->params()->fromQuery('searchString');
        }

        $paginator = $this->getRawMaterialsTable()->fetchFullDataPaginated($sortBy, $search);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($rows);

        if ($paginator->count() > 0) {
            foreach ($paginator->getItemsByPage($currentPage) as $value) {
                $value->EditHTML = '<a class="btn btn-warning btn-sm" href="/business-admin/raw-materials/edit/' . $value->RawMaterialID . '"><span class="glyphicon glyphicon-pencil"></span></a>';
                $value->DeleteHTML = '<a class="btn btn-danger btn-sm" href="/business-admin/raw-materials/delete/' . $value->RawMaterialID . '"><span class="glyphicon glyphicon-trash"></span></a>';
                $rawData[] = $value;
            }
        } else {
            $rawData[] = NULL;
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
        $form = new RawMaterialsForm();
        $form->get('submit')->setValue('Add');

        $selectData = $this->popSelectMenus();
        $form->get('RMTypeID')->setValueOptions($selectData['rawMaterialTypesData']);
        $form->get('RMSupplierID')->setValueOptions($selectData['suppliersData']);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $rawMaterials = new RawMaterials();
            $form->setInputFilter($rawMaterials->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $rawMaterials->exchangeArray($form->getData());
                $this->getRawMaterialsTable()->saveRawMaterials($rawMaterials);
                $this->flashmessenger()->setNamespace('info')->addMessage('Raw Material - ' . $rawMaterials->RawMaterialName . ' - added.');
                return $this->redirect()->toRoute('business-admin/raw-materials');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/raw-materials', array(
                        'action' => 'add'
            ));
        }

        try {
            $rawMaterials = $this->getRawMaterialsTable()->getRawMaterials($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('business-admin/raw-materials', array(
                        'action' => 'index'
            ));
        }

        $form = new RawMaterialsForm();

        $selectMenusData = $this->popSelectMenus();

        $form->get('RMTypeID')->setValueOptions($selectMenusData['rawMaterialTypesData']);
        $form->get('RMSupplierID')->setValueOptions($selectMenusData['suppliersData']);

        $form->bind($rawMaterials);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($rawMaterials->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getRawMaterialsTable()->saveRawMaterials($rawMaterials);
                $this->flashmessenger()->setNamespace('info')->addMessage('Raw Material - ' . $rawMaterials->RawMaterialName . ' - updated.');
                return $this->redirect()->toRoute('business-admin/raw-materials');
            }
        }

        $relatedProducts = $this->getProductsTable()->fetchProductsByMaterial($id);

        return array(
            'id' => $id,
            'form' => $form,
            'relatedProducts' => $relatedProducts
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/raw-materials');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getRawMaterialsTable()->deleteRawMaterials($id);
            }
            $this->flashmessenger()->setNamespace('info')->addMessage('Raw Material - ' . $rawMaterials->RawMaterialName . ' - deleted.');
            return $this->redirect()->toRoute('business-admin/raw-materials');
        }

        return array(
            'id' => $id,
            'rawMaterials' => $this->getRawMaterialsTable()->getRawMaterials($id)
        );
    }

    private function popSelectMenus() {
        $rawMaterialTypes = $this->getRawMaterialTypesTable()->fetchAll();
        $suppliers = $this->getSuppliersTable()->fetchAll();
        foreach ($rawMaterialTypes as $key => $value) {
            $rawMaterialTypesData[$value->RMTypeID] = $value->RMTypeName;
        }
        foreach ($suppliers as $key => $value) {
            $suppliersData[$value->RMSupplierID] = $value->RMSupplierName;
        }

        return array('rawMaterialTypesData' => $rawMaterialTypesData, 'suppliersData' => $suppliersData);
    }

    private function getRawMaterialsTable() {
        if (!$this->rawMaterialsTable) {
            $sm = $this->getServiceLocator();
            $this->rawMaterialsTable = $sm->get('AnnieHaak\Model\RawMaterialsTable');
        }
        return $this->rawMaterialsTable;
    }

    private function getRawMaterialTypesTable() {
        if (!$this->rawMaterialTypesTable) {
            $sm = $this->getServiceLocator();
            $this->rawMaterialTypesTable = $sm->get('AnnieHaak\Model\RawMaterialTypesTable');
        }
        return $this->rawMaterialTypesTable;
    }

    private function getSuppliersTable() {
        if (!$this->suppliersTable) {
            $sm = $this->getServiceLocator();
            $this->suppliersTable = $sm->get('AnnieHaak\Model\suppliersTable');
        }
        return $this->suppliersTable;
    }

    private function getProductsTable() {
        if (!$this->productsTable) {
            $sm = $this->getServiceLocator();
            $this->productsTable = $sm->get('AnnieHaak\Model\ProductsTable');
        }
        return $this->productsTable;
    }

}
