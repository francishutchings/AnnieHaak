<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Collections;
use AnnieHaak\Form\CollectionsForm;

class CollectionsController extends AbstractActionController {

    protected $collectionsTable;

    public function indexAction() {

        return new ViewModel(array(
            'collections' => $this->getCollectionsTable()->fetchAll(),
        ));
    }

    public function addAction() {

        if ($_SESSION['AnnieHaak']['storage']['userInfo']['roleLevel'] != 1) {
            return $this->redirect()->toRoute('business-admin/collections');
        }

        $form = new CollectionsForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $collections = new Collections();
            $form->setInputFilter($collections->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $collections->exchangeArray($form->getData());
                $this->getCollectionsTable()->saveCollections($collections);
                $this->flashmessenger()->setNamespace('info')->addMessage('Collection - ' . $collections->ProductCollectionName . ' - added.');
                return $this->redirect()->toRoute('business-admin/collections');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/collections', array(
                        'action' => 'add'
            ));
        }

        try {
            $collections = $this->getCollectionsTable()->getCollections($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('business-admin/collections', array(
                        'action' => 'index'
            ));
        }

        $form = new CollectionsForm();
        $form->bind($collections);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($collections->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getCollectionsTable()->saveCollections($collections);
                $this->flashmessenger()->setNamespace('info')->addMessage('Collection - ' . $collections->ProductCollectionName . ' - updated.');
                return $this->redirect()->toRoute('business-admin/collections');
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
            return $this->redirect()->toRoute('business-admin/collections');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getCollectionsTable()->deleteCollections($id);
            }
            $this->flashmessenger()->setNamespace('info')->addMessage('Collection - ' . $collections->ProductCollectionName . ' - deleted.');
            return $this->redirect()->toRoute('business-admin/collections');
        }

        return array(
            'id' => $id,
            'collections' => $this->getCollectionsTable()->getCollections($id)
        );
    }

    private function getCollectionsTable() {
        if (!$this->collectionsTable) {
            $sm = $this->getServiceLocator();
            $this->collectionsTable = $sm->get('AnnieHaak\Model\CollectionsTable');
        }
        return $this->collectionsTable;
    }

}
