<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use AnnieHaak\Model\RawMaterials;
use AnnieHaak\Form\RawMaterialsForm;
use Zend\View\Model\JsonModel;

class RawMaterialsController extends AbstractActionController {

    protected $rawMaterialsTable;

    public function indexAction() {
        /*
          http://framework.zend.com/manual/current/en/tutorials/tutorial.pagination.html
         *
          $paginator = $this->getRawMaterialsTable()->fetchFullData(false);
          $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
          $paginator->setItemCountPerPage(15);
          return new ViewModel(array(
          'paginator' => $paginator
          ));
         *
          return new ViewModel(array(
          'rawMaterials' => $this->getRawMaterialsTable()->fetchAll(),
          ));
         *
          $result = new JsonModel(array(
          'some_parameter' => 'some value',
          'success' => true,
          ));
         */

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

    public function getRawMaterialsTable() {
        if (!$this->rawMaterialsTable) {
            $sm = $this->getServiceLocator();
            $this->rawMaterialsTable = $sm->get('AnnieHaak\Model\RawMaterialsTable');
        }
        return $this->rawMaterialsTable;
    }

}
