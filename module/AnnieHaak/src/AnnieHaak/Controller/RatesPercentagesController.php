<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use AnnieHaak\Model\RatesPercentages;
use AnnieHaak\Form\RatesPercentagesForm;

class RatesPercentagesController extends AbstractActionController {

    protected $ratesPercentagesObj;

    public function indexAction() {
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $this->ratesPercentagesObj = new RatesPercentages($dbAdapter);

        return new ViewModel(array(
            'ratesPercentages' => $this->ratesPercentagesObj->fetchAll(),
        ));
    }

    public function EditAction() {
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $this->ratesPercentagesObj = new RatesPercentages($dbAdapter);

        $form = new RatesPercentagesForm();
        $form->bind($this->ratesPercentagesObj->fetchAll());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($this->ratesPercentagesObj->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->ratesPercentagesObj->saveRatesPercents($this->ratesPercentagesObj);
                return $this->redirect()->toRoute('business-admin/rates-percentages');
            }
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

}
