<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\RawMaterials;
use AnnieHaak\Model\Auditing;
use AnnieHaak\Form\RawMaterialsForm;
use Zend\View\Model\JsonModel;

class RawMaterialsController extends AbstractActionController {

    protected $rawMaterialsTable;
    protected $rawMaterialTypesTable;
    protected $suppliersTable;
    protected $productsTable;
    protected $auditingObj;

    public function indexAction() {
        return new ViewModel();
    }

    public function jsonAllMaterialsAction() {
        $rawMaterials = $this->getRawMaterialsTable()->fetchAll();
        foreach ($rawMaterials->toArray() as $value) {
            $data[] = array('id' => $value['RawMaterialID'], 'value' => $value['RawMaterialName']);
        }
        $result = new JsonModel(array(
            'rawMaterials' => $data
        ));
        return $result;
    }

    public function jsonMaterialsByProductAction() {
        $productid = (int) $this->params()->fromQuery('productid', 0);
        $rawMaterials = $this->getRawMaterialsTable()->fetchMaterialsByProduct($productid);
        $rawMaterials = $rawMaterials->toArray();
        foreach ($rawMaterials as $key => $value) {
            $rawMaterials[$key]["SubtotalRM"] = number_format((float) $value["SubtotalRM"], 4, '.', '');
            $rawMaterials[$key]["RawMaterialUnitCost"] = number_format((float) $value["RawMaterialUnitCost"], 4, '.', '');
        }
        $result = new JsonModel(array(
            'records' => count($rawMaterials),
            'page' => 1,
            'total' => count($rawMaterials),
            'rows' => $rawMaterials
        ));
        return $result;
    }

    public function jsonMaterialsByTypeAction() {
        $RawMaterialTypeId = (int) $this->params()->fromQuery('RawMaterialTypeId', 0);
        $rawMaterials = $this->getRawMaterialsTable()->fetchMaterialsByType($RawMaterialTypeId);
        #dump($rawMaterials);
        #exit();
        $result = new JsonModel(array(
            'rawMaterials' => $rawMaterials->toArray()
        ));
        return $result;
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
        $form = new RawMaterialsForm();
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
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getRawMaterialsTable()->saveRawMaterials($rawMaterials, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Raw Material -> ' . $rawMaterials->RawMaterialName . ' -> Added.');
                    return $this->redirect()->toRoute('business-admin/raw-materials', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/raw-materials', array('action' => 'add'));
                }
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/raw-materials', array('action' => 'add'));
        }
        try {
            $rawMaterials = $this->getRawMaterialsTable()->getRawMaterials($id);
        } catch (\Exception $ex) {
            $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('business-admin/raw-materials', array('action' => 'index'));
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
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getRawMaterialsTable()->saveRawMaterials($rawMaterials, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Raw Material -> ' . $rawMaterials->RawMaterialName . ' -> Updated.');
                    return $this->redirect()->toRoute('business-admin/raw-materials');
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/raw-materials', array('action' => 'edit', 'id' => $id));
                }
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
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getRawMaterialsTable()->deleteRawMaterials($id, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Raw Material -> Deleted.');
                    return $this->redirect()->toRoute('business-admin/raw-materials', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/raw-materials', array('action' => 'delete', 'id' => $id));
                }
            }
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

    private function getAuditing() {
        if (!$this->auditingObj) {
            $sm = $this->getServiceLocator();
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $this->auditingObj = new Auditing($dbAdapter);
        }
        return $this->auditingObj;
    }

}
