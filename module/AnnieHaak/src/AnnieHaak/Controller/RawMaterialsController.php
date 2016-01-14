<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use AnnieHaak\Model\RawMaterials;
use AnnieHaak\Form\RawMaterialsForm;

class RawMaterialsController extends AbstractActionController {

    protected $rawMaterialsTable;

    public function indexAction() {
        return new ViewModel(array(
            'rawMaterials' => $this->getRawMaterialsTable()->fetchAll(),
        ));
    }

    public function getRawMaterialsTable() {
        if (!$this->rawMaterialsTable) {
            $sm = $this->getServiceLocator();
            $this->rawMaterialsTable = $sm->get('AnnieHaak\Model\RawMaterialsTable');
        }
        return $this->rawMaterialsTable;
    }

}
