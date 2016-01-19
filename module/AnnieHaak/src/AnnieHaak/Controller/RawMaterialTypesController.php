<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\RawMaterialTypes;
use AnnieHaak\Form\RawMaterialTypesForm;
use Zend\View\Model\JsonModel;

class RawMaterialTypesController extends AbstractActionController {

    protected $rawMaterialTypesTable;

    public function indexAction() {

        return new ViewModel();
    }

    public function jsonDataAction() {

        /*
         * /business-admin/raw-materials/jsonData
         * ?_search=false
         * &nd=1452974065193
         * &rows=15
         * &page=1
         * &sidx=RMSupplierName
         * &sord=asc
         *
         * ##SEARCH
         * _search=true
         * &nd=1453035449910
         * &rows=15
         * &page=1
         * &sidx=RawMaterialCode
         * &sord=asc
         * &searchOper=eq
         * &searchString=B3X2.5FRCRM
         * &searchField=RawMaterialCode
         * &filters=
         */

        #dump($this->params());
        #exit();

        $currentPage = (int) $this->params()->fromQuery('page', 1);
        $sortColumn = $this->params()->fromQuery('sidx', 'RMTypeName');
        $sortOrder = $this->params()->fromQuery('sord', 'asc');
        $rows = (int) $this->params()->fromQuery('rows', 15);

        $sortBy = $sortColumn . ' ' . $sortOrder;

        $search = NULL;
        if ((Boolean) $this->params()->fromQuery('_search', FALSE)) {
            $search['searchColumn'] = $this->params()->fromQuery('searchField');
            $search['searchOper'] = $this->params()->fromQuery('searchOper');
            $search['searchString'] = $this->params()->fromQuery('searchString');
        }

        $paginator = $this->getRawMaterialTypesTable()->fetchFullDataPaginated($sortBy, $search);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($rows);


        foreach ($paginator->getItemsByPage($currentPage) as $value) {
            $value->EditHTML = '<a class="btn btn-warning btn-sm" href="/business-admin/raw-material-types/edit/' . $value->RMTypeID . '"><span class="glyphicon glyphicon-pencil"></span></a>';
            $value->DeleteHTML = '<a class="btn btn-danger btn-sm" href="/business-admin/raw-material-types/edit/' . $value->RMTypeID . '"><span class="glyphicon glyphicon-trash"></span></a>';
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
        $form = new RawMaterialTypesForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $rawMaterialTypes = new RawMaterialTypes();
            $form->setInputFilter($rawMaterialTypes->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $rawMaterialTypes->exchangeArray($form->getData());
                $this->getRawMaterialTypesTable()->saveRawMaterialTypes($rawMaterialTypes);
                $this->flashmessenger()->setNamespace('info')->addMessage('Raw Material Type - ' . $rawMaterialTypes->RMTypeName . ' - added.');
                return $this->redirect()->toRoute('business-admin/raw-material-types');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/raw-material-types', array(
                        'action' => 'add'
            ));
        }

        try {
            $rawMaterialTypes = $this->getRawMaterialTypesTable()->getRawMaterialTypes($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('business-admin/raw-material-types', array(
                        'action' => 'index'
            ));
        }

        $form = new RawMaterialTypesForm();
        $form->bind($rawMaterialTypes);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter($rawMaterialTypes->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getRawMaterialTypesTable()->saveRawMaterialTypes($rawMaterialTypes);
                $this->flashmessenger()->setNamespace('info')->addMessage('Raw Material Type - ' . $rawMaterialTypes->RMTypeName . ' - updated.');
                return $this->redirect()->toRoute('business-admin/raw-material-types');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/raw-material-types');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getRawMaterialTypesTable()->deleteRawMaterialTypes($id);
            }
            $this->flashmessenger()->setNamespace('info')->addMessage('Raw Material Type - ' . $rawMaterialTypes->RMTypeName . ' - deleted.');
            return $this->redirect()->toRoute('business-admin/raw-material-types');
        }

        return array(
            'id' => $id,
            'rawMaterialTypes' => $this->getRawMaterialTypesTable()->getRawMaterialTypes($id)
        );
    }

    public function getRawMaterialTypesTable() {
        if (!$this->rawMaterialTypesTable) {
            $sm = $this->getServiceLocator();
            $this->rawMaterialTypesTable = $sm->get('AnnieHaak\Model\RawMaterialTypesTable');
        }
        return $this->rawMaterialTypesTable;
    }

}
