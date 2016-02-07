<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\LabourItems;
use AnnieHaak\Form\LabourItemsForm;
use Zend\View\Model\JsonModel;

class LabourItemsController extends AbstractActionController {

    protected $labourItemsTable;

    public function indexAction() {
        return new ViewModel(array(
            'labourItems' => $this->getLabourItemsTable()->fetchAll(),
        ));
    }

    public function jsonAllLaboutItemsAction() {
        $labourItems = $this->getLabourItemsTable()->fetchAll();
        foreach ($labourItems->toArray() as $value) {
            $data[] = array('id' => $value['LabourID'], 'value' => $value['LabourName']);
        }
        $result = new JsonModel(array(
            'labourItems' => $data
        ));
        return $result;
    }

    public function jsonLabourItemsByProductAction() {
        $productId = (int) $this->params()->fromQuery('productId', 0);
        $labourItems = $this->getLabourItemsTable()->getLabourItemsByProduct($productId);
        $labourItems = $labourItems->toArray();
        foreach ($labourItems as $key => $value) {
            $labourItems[$key]["LabourUnitCost"] = number_format((float) $value["LabourUnitCost"], 4, '.', '');
            $labourItems[$key]["SubtotalLabour"] = number_format((float) $value["SubtotalLabour"], 4, '.', '');
        }
        $result = new JsonModel(array(
            'records' => count($labourItems),
            'page' => 1,
            'total' => count($labourItems),
            'rows' => $labourItems
        ));
        return $result;
    }

    public function jsonLabourItemByTypeAction() {
        $LabourId = (int) $this->params()->fromQuery('LabourId', 0);
        $labourItems = $this->getLabourItemsTable()->fetchLabourItemByType($LabourId);
        #dump($labourItems);
        #exit();
        $result = new JsonModel(array(
            'labourItems' => $labourItems->toArray()
        ));
        return $result;
    }

    public function addAction() {
        $form = new LabourItemsForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $labourItems = new LabourItems();
            $form->setInputFilter($labourItems->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $labourItems->exchangeArray($form->getData());
                $this->getLabourItemsTable()->saveLabourItems($labourItems);
                $this->flashmessenger()->setNamespace('info')->addMessage('Labour Item - ' . $labourItems->LabourName . ' - added.');
                return $this->redirect()->toRoute('business-admin/labour-items');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/labour-items', array(
                        'action' => 'add'
            ));
        }

        try {
            $labourItems = $this->getLabourItemsTable()->getLabourItems($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('business-admin/labour-items', array(
                        'action' => 'index'
            ));
        }

        $form = new LabourItemsForm();
        $form->bind($labourItems);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($labourItems->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getLabourItemsTable()->saveLabourItems($labourItems);
                $this->flashmessenger()->setNamespace('info')->addMessage('Labour Item - ' . $labourItems->LabourName . ' - updated.');
                return $this->redirect()->toRoute('business-admin/labour-items');
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
            return $this->redirect()->toRoute('business-admin/labour-items');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getLabourItemsTable()->deleteLabourItems($id);
            }
            $this->flashmessenger()->setNamespace('info')->addMessage('Labour Item deleted.');
            return $this->redirect()->toRoute('business-admin/labour-items');
        }

        return array(
            'id' => $id,
            'labourItems' => $this->getLabourItemsTable()->getLabourItems($id)
        );
    }

    public function getLabourItemsTable() {
        if (!$this->labourItemsTable) {
            $sm = $this->getServiceLocator();
            $this->labourItemsTable = $sm->get('AnnieHaak\Model\LabourItemsTable');
        }
        return $this->labourItemsTable;
    }

}
