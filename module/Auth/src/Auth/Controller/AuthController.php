<?php

namespace Auth\Controller;

use Auth\Model\UserLogin;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class AuthController extends AbstractActionController {

    protected $form;
    protected $storage;
    protected $authservice;

    public function getAuthService() {

        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()
                    ->get('AuthService');
        }

        return $this->authservice;
    }

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()
                    ->get('Auth\Model\MyAuthStorage');
        }

        return $this->storage;
    }

    public function getForm() {
        if (!$this->form) {
            $user = new UserLogin();
            $builder = new AnnotationBuilder();
            $this->form = $builder->createForm($user);
        }

        return $this->form;
    }

    public function loginAction() {
        //if already login, redirect to success page
        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $form = $this->getForm();
        $messages = $this->flashmessenger()->setNamespace('info')->addMessage("Please login to use the system.");

        return array(
            'form' => $form,
            'messages' => $messages
        );
    }

    public function authenticateAction() {
        $form = $this->getForm();
        $redirect = 'login';

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                //check authentication...
                $this->getAuthService()
                        ->getAdapter()->setIdentity($request->getPost('username'))
                        ->setCredential($request->getPost('password'));

                $result = $this->getAuthService()->authenticate();
//                foreach ($result->getMessages() as $message) {
                //save message temporary into flashmessenger
//                    $this->flashmessenger()->addMessage($message);
//                }

                if ($result->isValid()) {
                    $redirect = 'home';
                    //check if it has rememberMe :
                    if ($request->getPost('rememberme') == 1) {
                        $this->getSessionStorage()->setRememberMe(1);
                        $this->getAuthService()->getStorage()->write($result->id);
                        //set storage again
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->setStorage($this->getSessionStorage());

                    $sessionInfo = [
                        'loggedIn' => TRUE,
                        'username' => $request->getPost('username'),
                    ];
                    $this->getAuthService()->getStorage()->write($sessionInfo);
                } else {
                    $this->flashmessenger()->setNamespace('error')->addMessage("Details not valid.");
                }
            }
        }

        return $this->redirect()->toRoute($redirect);
    }

    public function logoutAction() {
        if ($this->getAuthService()->hasIdentity()) {
            $this->getSessionStorage()->forgetMe();
            $this->getAuthService()->clearIdentity();
            $this->flashmessenger()->setNamespace('warning')->addMessage("You've been logged out.");
        }

        return $this->redirect()->toRoute('login');
    }

}
