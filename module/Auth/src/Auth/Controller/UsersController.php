<?php

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Model\Users;
use Auth\Form\UsersForm;
use Zend\Validator\EmailAddress;

class UsersController extends AbstractActionController {

    protected $usersTable;

    public function indexAction() {
        return new ViewModel(array(
            'users' => $this->getUsersTable()->fetchAll(),
        ));
    }

    public function addAction() {
        $form = new UsersForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new Users();
            $validator = new EmailAddress();

            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if (!$validator->isValid($form->get('username')->getValue())) {
                foreach ($validator->getMessages() as $message) {
                    $this->flashmessenger()->addMessage($message);
                }
            } else if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $result = $this->getUsersTable()->saveUsers($user);

                if ($result['error']) {
                    $this->flashmessenger()->setNamespace('error')->addMessage($result['message']);
                } else {
                    $this->flashmessenger()->setNamespace('error')->addMessage('User details saved');
                    return $this->redirect()->toRoute('user-admin');
                }
            }
        }

        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {

            return $this->redirect()->toRoute('user-admin', array(
                        'action' => 'add'
            ));
        }

        try {
            $user = $this->getUsersTable()->getUsers($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user-admin', array(
                        'action' => 'index'
            ));
        }

        $form = new UsersForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Update');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getUsersTable()->saveUsers($user);

                return $this->redirect()->toRoute('user-admin');
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
            return $this->redirect()->toRoute('user-admin');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getUsersTable()->deleteUsers($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('user-admin');
        }

        return array(
            'id' => $id,
            'album' => $this->getUsersTable()->getUsers($id)
        );
    }

    public function getUsersTable() {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Auth\Model\UsersTable');
        }
        return $this->usersTable;
    }

}
