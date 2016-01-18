<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use AnnieHaak\Model\RawMaterials;
use AnnieHaak\Form\RawMaterialsForm;
use Zend\View\Model\JsonModel;
use AnnieHaak\Model\RawMaterialTypesTable;
use AnnieHaak\Model\SuppliersTable;

class RawMaterialsController extends AbstractActionController {

    protected $rawMaterialsTable;
    protected $rawMaterialTypesTable;
    protected $suppliersTable;

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


        foreach ($paginator->getItemsByPage($currentPage) as $value) {
            $value->EditHTML = '<a class="btn btn-warning btn-sm" href="/business-admin/raw-materials/edit/' . $value->RawMaterialID . '"><span class="glyphicon glyphicon-pencil"></span></a>';
            $value->DeleteHTML = '<a class="btn btn-danger btn-sm" href="/business-admin/raw-materials/edit/' . $value->RawMaterialID . '"><span class="glyphicon glyphicon-trash"></span></a>';
            $rawData[] = $value;
        }

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

        $rawMaterialTypes = $this->getRawMaterialTypesTable()->fetchAll();
        $suppliers = $this->getSuppliersTable()->fetchAll();

        foreach ($rawMaterialTypes as $key => $value) {
            $rawMaterialTypesData[$value->RMTypeID] = $value->RMTypeName;
        }

        foreach ($suppliers as $key => $value) {
            $suppliersData[$value->RMSupplierID] = $value->RMSupplierName;
        }

        $form->get('RMTypeID')->setValueOptions($rawMaterialTypesData);
        $form->get('RMSupplierID')->setValueOptions($suppliersData);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $rawMaterial = new RawMaterial();
            $form->setInputFilter($rawMaterial->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $rawMaterial->exchangeArray($form->getData());
                $this->getRawMaterialTypesTable()->saveRawMaterial($rawMaterial);

                return $this->redirect()->toRoute('business-admin/raw-materials');
            }
        }
        #dump($form->get('RMSupplierID'));
        #exit();
        return array('form' => $form);
    }

    public function getRawMaterialsTable() {
        if (!$this->rawMaterialsTable) {
            $sm = $this->getServiceLocator();
            $this->rawMaterialsTable = $sm->get('AnnieHaak\Model\RawMaterialsTable');
        }
        return $this->rawMaterialsTable;
    }

    public function getRawMaterialTypesTable() {
        if (!$this->rawMaterialTypesTable) {
            $sm = $this->getServiceLocator();
            $this->rawMaterialTypesTable = $sm->get('AnnieHaak\Model\RawMaterialTypesTable');
        }
        return $this->rawMaterialTypesTable;
    }

    public function getSuppliersTable() {
        if (!$this->suppliersTable) {
            $sm = $this->getServiceLocator();
            $this->suppliersTable = $sm->get('AnnieHaak\Model\suppliersTable');
        }
        return $this->suppliersTable;
    }

}
