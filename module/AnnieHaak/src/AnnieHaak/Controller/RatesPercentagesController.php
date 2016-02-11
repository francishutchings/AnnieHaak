<?php

namespace AnnieHaak\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AnnieHaak\Model\Auditing;
use AnnieHaak\Form\RatesPercentagesForm;

class RatesPercentagesController extends AbstractActionController {

    protected $ratesPercentagesTable;
    protected $auditingObj;

    public function indexAction() {
        $ratesPercentages = $this->getRatesPercentagesTable()->getRatesPercentages(1);
        return new ViewModel(array(
            'ratesPercentages' => $ratesPercentages
        ));
    }

    public function EditAction() {
        try {
            $ratesPercentages = $this->getRatesPercentagesTable()->getRatesPercentages(1);
        } catch (\Exception $ex) {
            $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('business-admin/rates-percentages', array('action' => 'index'));
        }
        $form = new RatesPercentagesForm();
        $form->bind($ratesPercentages);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($ratesPercentages->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $auditingObj = $this->getAuditing();
                $auditingObj->UserName = $_SESSION['AnnieHaak']['storage']['userInfo']['username'];
                try {
                    $this->getRatesPercentagesTable()->saveRatesPercentages($ratesPercentages, $auditingObj);
                    $this->flashmessenger()->setNamespace('info')->addMessage('Rates Percentages -> Updated.');
                    return $this->redirect()->toRoute('business-admin/rates-percentages', array('action' => 'index'));
                } catch (\Exception $ex) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($ex->getMessage());
                    return $this->redirect()->toRoute('business-admin/rates-percentages', array('action' => 'edit'));
                }
            }
        }
        return new ViewModel(array(
            'form' => $form
        ));
    }

    private function getRatesPercentagesTable() {
        if (!$this->ratesPercentagesTable) {
            $sm = $this->getServiceLocator();
            $this->ratesPercentagesTable = $sm->get('AnnieHaak\Model\RatesPercentagesTable');
        }
        return $this->ratesPercentagesTable;
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
