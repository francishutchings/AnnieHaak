<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use AnnieHaak\Model\RawMaterialTypes;
use AnnieHaak\Form\RawMaterialTypesForm;

class RawMaterialTypesController extends AbstractActionController {

    protected $rawMaterialTypesTable;

    public function indexAction() {
        return new ViewModel(array(
            'rawMaterialTypes' => $this->getRawMaterialTypesTable()->fetchAll(),
        ));
    }

    public function addAction() {
        $form = new RawMaterialTypesForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $rawMaterialTypes = new RawMaterialTypes();
            $form->setInputFilter($rawMaterialTypes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $rawMaterialTypes->exchangeArray($form->getData());
                $this->getRawMaterialTypesTable()->saveRawMaterialTypes($rawMaterialTypes);

                return $this->redirect()->toRoute('business-admin/raw-material-types');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('business-admin/raw-material-types', array(
                        'action' => 'add'
            ));
        }

        try {
            $rawMaterialTypes = $this->getRawMaterialTypesTable()->getRawMaterialTypes($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('business-admin/raw-material-types', array(
                        'action' => 'index'
            ));
        }

        $form = new RawMaterialTypesForm();
        $form->bind($rawMaterialTypes);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter($rawMaterialTypes->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getRawMaterialTypesTable()->saveRawMaterialTypes($rawMaterialTypes);

                return $this->redirect()->toRoute('business-admin/raw-material-types');
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
            return $this->redirect()->toRoute('business-admin/raw-material-types');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getRawMaterialTypesTable()->deleteRawMaterialTypes($id);
            }

            return $this->redirect()->toRoute('business-admin/raw-material-types');
        }

        return array(
            'id' => $id,
            'rawMaterialTypes' => $this->getRawMaterialTypesTable()->getRawMaterialTypes($id)
        );
    }

    public function getRawMaterialTypesTable() {
        if (!$this->rawMaterialTypesTable) {
            $sm = $this->getServiceLocator();
            $this->rawMaterialTypesTable = $sm->get('AnnieHaak\Model\RawMaterialTypesTable');
        }
        return $this->rawMaterialTypesTable;
    }

}
