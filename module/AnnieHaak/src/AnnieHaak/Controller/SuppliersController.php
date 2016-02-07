<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Suppliers;
use AnnieHaak\Model\Auditing;
use AnnieHaak\Form\SuppliersForm;

class SuppliersController extends AbstractActionController {

    protected $suppliersTable;
    protected $auditingObj;

    public function indexAction() {

        return new ViewModel(array(
            'suppliers' => $this->getSuppliersTable()->fetchAll(),
        ));
    }

    public function addAction() {
        $form = new SuppliersForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $suppliers = new Suppliers();
            $form->setInputFilter($suppliers->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $suppliers->exchangeArray($form->getData());
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];

                try {
                    $this->getSuppliersTable()->saveSuppliers($suppliers, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Supplier - ' . $suppliers->RMSupplierName . ' - added.');
                    return $this->redirect()->toRoute('business-admin/suppliers', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/suppliers', array('action' => 'add'));
                }
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/suppliers', array('action' => 'add'));
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
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getSuppliersTable()->saveSuppliers($suppliers, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Supplier - ' . $suppliers->RMSupplierName . ' - updated.');
                    return $this->redirect()->toRoute('business-admin/suppliers', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/suppliers', array('action' => 'edit', 'id' => $id));
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
            return $this->redirect()->toRoute('business-admin/suppliers', array('action' => 'index'));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getSuppliersTable()->deleteSuppliers($id, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Supplier - deleted.');
                    return $this->redirect()->toRoute('business-admin/suppliers', array('action' => 'index'));
                } catch (Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/suppliers', array('action' => 'delete', 'id' => $id));
                }
            }
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

    private function getAuditing() {
        if (!$this->auditingObj) {
            $sm = $this->getServiceLocator();
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $this->auditingObj = new Auditing($dbAdapter);
        }
        return $this->auditingObj;
    }

}
