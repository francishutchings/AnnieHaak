<?php

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Model\Users;
use Auth\Form\UsersForm;

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
        /*
          echo '<pre>';
          var_dump($form);
          echo '</pre>';
          exit();
         */

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new Users();
            $form->setInputFilter($user->getInputFilter());

            $form->get('username')->getValidatorChain()->attach(new Validator\EmailAddress());

            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $this->getUsersTable()->saveUsers($user);

                return $this->redirect()->toRoute('admin-users');
            }
        }

        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin-users', array(
                        'action' => 'add'
            ));
        }

        try {
            $album = $this->getAlbumTable()->getUsers($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('admin-users', array(
                        'action' => 'index'
            ));
        }

        $form = new UsersForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getUsersTable()->saveUsers($user);

                // Redirect to list of albums
                return $this->redirect()->toRoute('admin-users');
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
            return $this->redirect()->toRoute('admin-users');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getUsersTable()->deleteUsers($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('admin-users');
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
