<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\ProductTypes;
use AnnieHaak\Form\ProductTypesForm;

class ProductTypesController extends AbstractActionController {

    protected $productTypesTable;

    public function indexAction() {

        return new ViewModel(array(
            'productTypes' => $this->getProductTypesTable()->fetchAll(),
        ));
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
                $this->getProductTypesTable()->saveProductTypes($productTypes);
                $this->flashmessenger()->setNamespace('info')->addMessage('Product Type - ' . $productTypes->ProductTypeName . ' - added.');
                return $this->redirect()->toRoute('business-admin/product-types');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/product-types', array(
                        'action' => 'add'
            ));
        }

        try {
            $productTypes = $this->getProductTypesTable()->getProductTypes($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('business-admin/product-types', array(
                        'action' => 'index'
            ));
        }

        $form = new ProductTypesForm();
        $form->bind($productTypes);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($productTypes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getProductTypesTable()->saveProductTypes($productTypes);
                $this->flashmessenger()->setNamespace('info')->addMessage('Product Type - ' . $productTypes->ProductTypeName . ' - updated.');
                return $this->redirect()->toRoute('business-admin/product-types');
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
            return $this->redirect()->toRoute('business-admin/product-types');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getProductTypesTable()->deleteProductTypes($id);
            }
            $this->flashmessenger()->setNamespace('info')->addMessage('Product Type deleted.');
            return $this->redirect()->toRoute('business-admin/product-types');
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

}
