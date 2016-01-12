<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use AnnieHaak\Model\RatesPercentages;
use AnnieHaak\Form\RatesPercentagesForm;

class RatesPercentagesController extends AbstractActionController {

    public function indexAction() {
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $ratesPercentagesObj = new RatesPercentages($dbAdapter);

        return new ViewModel(array(
            'ratesPercentages' => $ratesPercentagesObj->fetchAll(),
        ));
    }

    public function EditAction() {
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $ratesPercentagesObj = new RatesPercentages($dbAdapter);

        $form = new RatesPercentagesForm();
        $form->bind($ratesPercentagesObj->fetchAll());

        return new ViewModel(array(
            'form' => $form
        ));
    }

}
