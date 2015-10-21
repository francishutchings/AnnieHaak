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
        
    }

    public function deleteAction() {
        
    }

    public function getUsersTable() {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Auth\Model\UsersTable');
        }
        return $this->usersTable;
    }

}
