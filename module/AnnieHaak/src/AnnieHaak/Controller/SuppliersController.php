<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Suppliers;
use AnnieHaak\Form\SuppliersForm;

class SuppliersController extends AbstractActionController {

    protected $suppliersTable;

    public function indexAction() {

        return new ViewModel(array(
            'suppliers' => $this->getSuppliersTable()->fetchAll(),
        ));
    }

    public function addAction() {
        $form = new SuppliersForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $suppliers = new Suppliers();
            $form->setInputFilter($suppliers->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $suppliers->exchangeArray($form->getData());
                $this->getSuppliersTable()->saveSuppliers($suppliers);
                $this->flashmessenger()->setNamespace('info')->addMessage('Supplier - ' . $suppliers->RMSupplierName . ' - added.');
                return $this->redirect()->toRoute('business-admin/suppliers');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/suppliers', array(
                        'action' => 'add'
            ));
        }

        try {
            $suppliers = $this->getSuppliersTable()->getSuppliers($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('business-admin/suppliers', array(
                        'action' => 'index'
            ));
        }

        $form = new SuppliersForm();
        $form->bind($suppliers);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($suppliers->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getSuppliersTable()->saveSuppliers($suppliers);
                $this->flashmessenger()->setNamespace('info')->addMessage('Supplier - ' . $suppliers->RMSupplierName . ' - updated.');
                return $this->redirect()->toRoute('business-admin/suppliers');
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
            return $this->redirect()->toRoute('business-admin/suppliers');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSuppliersTable()->deleteSuppliers($id);
            }
            $this->flashmessenger()->setNamespace('info')->addMessage('Supplier - ' . $suppliers->RMSupplierName . ' - deleted.');
            return $this->redirect()->toRoute('business-admin/suppliers');
        }

        return array(
            'id' => $id,
            'suppliers' => $this->getSuppliersTable()->getSuppliers($id),
        );
    }

    private function getSuppliersTable() {
        if (!$this->suppliersTable) {
            $sm = $this->getServiceLocator();
            $this->suppliersTable = $sm->get('AnnieHaak\Model\suppliersTable');
        }
        return $this->suppliersTable;
    }

}
