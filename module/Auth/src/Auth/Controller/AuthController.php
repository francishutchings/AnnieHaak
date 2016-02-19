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
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()->get('Auth\Model\MyAuthStorage');
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

            // Security token check
            if (!isset($_SESSION['Zend_Validator_Csrf_salt_security']['hash']) || $_SESSION['Zend_Validator_Csrf_salt_security']['hash'] !== filter_input(INPUT_POST, 'security')) {
                return $this->redirect()->toRoute('login');
            }

            // Captcha check
            $captchaData = $request->getPost('captcha');
            $image = getcwd() . '/public/img/captcha/' . $captchaData['id'] . '.png';

            if (file_exists($image) == true) {
                unlink($image);
            }
            if (!isset($_SESSION['Zend_Form_Captcha_' . $captchaData['id']]) || $_SESSION['Zend_Form_Captcha_' . $captchaData['id']]['word'] !== $captchaData['input']) {
                return $this->redirect()->toRoute('login');
            }

            if ($form->isValid()) {

                $username = filter_var($request->getPost('username'), FILTER_VALIDATE_EMAIL);
                $password = $request->getPost('password');

                if ($username !== false) {

                    //check authentication...
                    $this->getAuthService()
                            ->getAdapter()->setIdentity($username)
                            ->setCredential($password)
                            ->getDbSelect()->where('deleted = 0');

                    $result = $this->getAuthService()->authenticate();

                    if ($result->isValid()) {

                        $redirect = 'home';

                        if ($request->getPost('rememberme') == 1) {
                            $this->getSessionStorage()->setRememberMe(1);
                            $this->getAuthService()->getStorage()->write($result->id);

                            $this->getAuthService()->setStorage($this->getSessionStorage());
                        }
                        $this->getAuthService()->setStorage($this->getSessionStorage());

                        $sm = $this->getServiceLocator();
                        $this->usersTable = $sm->get('Auth\Model\UsersTable');
                        $currUser = $this->usersTable->getUsersByUsername($request->getPost('username'));
                        $this->flashmessenger()->setNamespace('info')->addMessage('Welcome ' . $currUser->firstname . ', you are logged in under a ' . $currUser->rolename . ' account.');

                        $sessionInfo = [
                            'userInfo' => array(
                                'loggedIn' => TRUE,
                                'username' => $request->getPost('username'),
                                'roleLevel' => $currUser->rolelevel,
                            ),
                        ];
                        $this->getAuthService()->getStorage()->write($sessionInfo);
                    }
                } else {
                    foreach ($result->getMessages() as $message) {
                        $this->flashmessenger()->setNamespace('error')->addMessage($message);
                    }
                }
            }
        }

        return $this->redirect()->toRoute($redirect);
    }

    public function logoutAction() {
        if ($this->getAuthService()->hasIdentity()) {
            $this->getSessionStorage()->forgetMe();
            $this->getAuthService()->clearIdentity();
            session_start();
            session_unset();
            session_destroy();
            session_write_close();
            setcookie(session_name(), '', 0, '/');
            session_regenerate_id(true);
            $this->flashmessenger()->setNamespace('warning')->addMessage("You've been logged out.");
        }

        return $this->redirect()->toRoute('login');
    }

}
