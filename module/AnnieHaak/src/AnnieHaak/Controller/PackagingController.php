<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Packaging;
use AnnieHaak\Model\Auditing;
use AnnieHaak\Form\PackagingForm;
use Zend\View\Model\JsonModel;

class PackagingController extends AbstractActionController {

    protected $packagingTable;
    protected $auditingObj;

    public function indexAction() {
        return new ViewModel(array(
            'packaging' => $this->getPackagingTable()->fetchAll(),
        ));
    }

    public function jsonAllPackagingAction() {
        $packaging = $this->getPackagingTable()->fetchAll();
        foreach ($packaging->toArray() as $value) {
            $data[] = array('id' => $value['PackagingID'], 'value' => $value['PackagingName']);
        }
        $result = new JsonModel(array(
            'packaging' => $data
        ));
        return $result;
    }

    public function jsonPackagingByTypeAction() {
        $packagingId = (int) $this->params()->fromQuery('PackagingID', 0);
        $packaging = $this->getPackagingTable()->fetchPackagingByType($packagingId);
        #dump($packaging);
        #exit();
        $result = new JsonModel(array(
            'packaging' => $packaging->toArray()
        ));
        return $result;
    }

    public function jsonPackagingByProductAction() {
        $productId = (int) $this->params()->fromQuery('productId', 0);
        $packaging = $this->getPackagingTable()->getPackagingByProduct($productId);
        $packaging = $packaging->toArray();
        foreach ($packaging as $key => $value) {
            $packaging[$key]["PackagingUnitCost"] = number_format((float) $value["PackagingUnitCost"], 4, '.', '');
            $packaging[$key]["SubtotalPackaging"] = number_format((float) $value["SubtotalPackaging"], 4, '.', '');
        }
        $result = new JsonModel(array(
            'records' => count($packaging),
            'page' => 1,
            'total' => count($packaging),
            'rows' => $packaging
        ));
        return $result;
    }

    public function addAction() {
        $form = new PackagingForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $packaging = new Packaging();
            $form->setInputFilter($packaging->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $packaging->exchangeArray($form->getData());
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getPackagingTable()->savePackaging($packaging, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Packaging -> ' . $packaging->PackagingName . ' -> Added.');
                    return $this->redirect()->toRoute('business-admin/packaging', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/packaging', array('action' => 'add'));
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
            return $this->redirect()->toRoute('business-admin/packaging', array('action' => 'add'));
        }
        try {
            $packaging = $this->getPackagingTable()->getPackaging($id);
        } catch (\Exception $ex) {
            $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('business-admin/packaging', array('action' => 'index'));
        }
        $form = new PackagingForm();
        $form->bind($packaging);
        $form->get('submit')->setAttribute('value', 'Update');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($packaging->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getPackagingTable()->savePackaging($packaging, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Packaging -> ' . $packaging->PackagingName . ' -> Updated.');
                    return $this->redirect()->toRoute('business-admin/packaging');
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/packaging', array('action' => 'edit', 'id' => $id));
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
            return $this->redirect()->toRoute('business-admin/packaging');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getPackagingTable()->deletePackaging($id, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Packaging -> Deleted.');
                    return $this->redirect()->toRoute('business-admin/packaging', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/packaging', array('action' => 'delete', 'id' => $id));
                }
            }
        }
        return array(
            'id' => $id,
            'packaging' => $this->getPackagingTable()->getPackaging($id)
        );
    }

    public function getPackagingTable() {
        if (!$this->packagingTable) {
            $sm = $this->getServiceLocator();
            $this->packagingTable = $sm->get('AnnieHaak\Model\PackagingTable');
        }
        return $this->packagingTable;
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
