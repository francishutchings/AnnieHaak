<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Controller\AuthController;
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
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $productTypes = new ProductTypes();
            $form->setInputFilter($productTypes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $productTypes->exchangeArray($form->getData());
                $this->getProductTypesTable()->saveProductTypes($productTypes);

                // Redirect to list of albums
                return $this->redirect()->toRoute('productTypes');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('productTypes', array(
                        'action' => 'add'
            ));
        }


        try {
            $productTypes = $this->getProductTypesTable()->getProductTypes($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('productTypes', array(
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

                // Redirect to list of albums
                return $this->redirect()->toRoute('productTypes');
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
            return $this->redirect()->toRoute('productTypes');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getProductTypesTable()->deleteProductTypes($id);
            }

            // Redirect to list of productTypes
            return $this->redirect()->toRoute('productTypes');
        }

        return array(
            'id' => $id,
            'productTypes' => $this->getProductTypesTable()->getProductTypes($id)
        );
    }

    public function getProductTypesTable() {
        if (!$this->productTypesTable) {
            $sm = $this->getServiceLocator();
            $this->productTypesTable = $sm->get('AnnieHaak\Model\ProductTypesTable');
        }
        return $this->productTypesTable;
    }

}
