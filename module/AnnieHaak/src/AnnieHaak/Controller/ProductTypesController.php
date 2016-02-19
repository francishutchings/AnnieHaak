<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use AnnieHaak\Model\ProductTypes;
use AnnieHaak\Model\Auditing;
use AnnieHaak\Form\ProductTypesForm;

class ProductTypesController extends AbstractActionController {

    protected $productTypesTable;
    protected $auditingObj;

    public function indexAction() {

        return new ViewModel(array(
            'productTypes' => $this->getProductTypesTable()->fetchAll(),
        ));
    }

    public function jsonAllProductTypesAction() {
        $productTypes = $this->getProductTypesTable()->fetchAll();
        foreach ($productTypes->toArray() as $value) {
            $data[] = array(
                'id' => $value['ProductTypeId'],
                'TypeName' => $value['ProductTypeName']
            );
        }
        $result = new JsonModel(array(
            'productTypes' => $data
        ));
        return $result;
    }

    public function addAction() {
        $form = new ProductTypesForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $productTypes = new ProductTypes();
            $form->setInputFilter($productTypes->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $productTypes->exchangeArray($form->getData());
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getProductTypesTable()->saveProductTypes($productTypes, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Product Type -> ' . $productTypes->ProductTypeName . ' -> Added.');
                    return $this->redirect()->toRoute('business-admin/product-types');
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/product-types', array('action' => 'add'));
                }
            }
        }
        return array(
            'form' => $form
        );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/product-types', array('action' => 'add'));
        }
        try {
            $productTypes = $this->getProductTypesTable()->getProductTypes($id);
        } catch (\Exception $ex) {
            $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('business-admin/product-types', array('action' => 'index'));
        }
        $form = new ProductTypesForm();
        $form->bind($productTypes);
        $form->get('submit')->setAttribute('value', 'Update');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($productTypes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getProductTypesTable()->saveProductTypes($productTypes, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Product Type -> ' . $productTypes->ProductTypeName . ' -> Updated.');
                    return $this->redirect()->toRoute('business-admin/product-types');
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/product-types', array('action' => 'edit', 'id' => $id));
                }
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
            return $this->redirect()->toRoute('business-admin/product-types', array('action' => 'index'));
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getProductTypesTable()->deleteProductTypes($id, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Product Type -> Deleted.');
                    return $this->redirect()->toRoute('business-admin/product-types', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/product-types', array('action' => 'delete', 'id' => $id));
                }
            }
        }
        return array(
            'id' => $id,
            'productTypes' => $this->getProductTypesTable()->getProductTypes($id)
        );
    }

    private function getProductTypesTable() {
        if (!$this->productTypesTable) {
            $sm = $this->getServiceLocator();
            $this->productTypesTable = $sm->get('AnnieHaak\Model\ProductTypesTable');
        }
        return $this->productTypesTable;
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
