<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\RatesPercentages;
use AnnieHaak\Model\Auditing;
use AnnieHaak\Form\RatesPercentagesForm;

class RatesPercentagesController extends AbstractActionController {

    protected $ratesPercentagesObj;
    protected $auditingObj;

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

                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];

                try {
                    $this->ratesPercentagesObj->saveRatesPercents($this->ratesPercentagesObj, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Rates and Percentages updated.');
                    return $this->redirect()->toRoute('business-admin/rates-percentages', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/rates-percentages', array('action' => 'delete', 'id' => $id));
                }
            }
        }

        return new ViewModel(array(
            'form' => $form
        ));
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
