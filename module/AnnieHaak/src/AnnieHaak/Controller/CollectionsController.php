<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Collections;
use AnnieHaak\Model\Auditing;
use AnnieHaak\Form\CollectionsForm;

class CollectionsController extends AbstractActionController {

    protected $collectionsTable;
    protected $auditingObj;

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

        $request = $this->getRequest();
        if ($request->isPost()) {
            $collections = new Collections();
            $form->setInputFilter($collections->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $collections->exchangeArray($form->getData());
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];

                try {
                    $this->getCollectionsTable()->saveCollections($collections, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Collection -> ' . $collections->ProductCollectionName . ' -> Added.');
                    return $this->redirect()->toRoute('business-admin/collections', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/collections', array('action' => 'add'));
                }
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/collections', array('action' => 'add'));
        }

        try {
            $collections = $this->getCollectionsTable()->getCollections($id);
        } catch (\Exception $ex) {
            $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('business-admin/collections', array('action' => 'index'));
        }

        $form = new CollectionsForm();
        $form->bind($collections);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($collections->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getCollectionsTable()->saveCollections($collections, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Collection -> ' . $collections->ProductCollectionName . ' -> Updated.');
                    return $this->redirect()->toRoute('business-admin/collections', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/collections', array('action' => 'edit', 'id' => $id));
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
            return $this->redirect()->toRoute('business-admin/collections', array('action' => 'index'));
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getCollectionsTable()->deleteCollections($id, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Collection -> Deleted.');
                    return $this->redirect()->toRoute('business-admin/collections', array('action' => 'index'));
                } catch (Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/collections', array('action' => 'delete', 'id' => $id));
                }
            }
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

    private function getAuditing() {
        if (!$this->auditingObj) {
            $sm = $this->getServiceLocator();
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $this->auditingObj = new Auditing($dbAdapter);
        }
        return $this->auditingObj;
    }

}
