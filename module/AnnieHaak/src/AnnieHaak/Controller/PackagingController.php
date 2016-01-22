<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Packaging;
use AnnieHaak\Form\PackagingForm;

class PackagingController extends AbstractActionController {

    protected $packagingTable;

    public function indexAction() {
        return new ViewModel(array(
            'packaging' => $this->getPackagingTable()->fetchAll(),
        ));
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
                $this->getPackagingTable()->savePackaging($packaging);
                $this->flashmessenger()->setNamespace('info')->addMessage('Packaging - ' . $packaging->PackagingName . ' - added.');
                return $this->redirect()->toRoute('business-admin/packaging');
            }
        }

        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/packaging', array(
                        'action' => 'add'
            ));
        }

        try {
            $packaging = $this->getPackagingTable()->getPackaging($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('business-admin/packaging', array(
                        'action' => 'index'
            ));
        }

        $form = new PackagingForm();
        $form->bind($packaging);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($packaging->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getPackagingTable()->savePackaging($packaging);
                $this->flashmessenger()->setNamespace('info')->addMessage('Packaging - ' . $packaging->PackagingName . ' - updated.');
                return $this->redirect()->toRoute('business-admin/packaging');
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
                $this->getPackagingTable()->deletePackaging($id);
            }
            $this->flashmessenger()->setNamespace('info')->addMessage('Packaging deleted.');
            return $this->redirect()->toRoute('business-admin/packaging');
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

}
