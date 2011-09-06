<?php

class LoginController extends Zend_Controller_Action {
    protected $sessionHandle = null;

    public function init() {
        //if user is already logged in, unless they're trying to log out
        if ($this->view->isUserLogged && $this->getRequest()->getActionName() != 'logout') {
            $this->_helper->redirector('index', 'person', 'default');
        }
        $this->sessionHandle = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
    }

    public function indexAction() {
        // action body
        //get conditional variable that determines where to redirect to
        $redirect_to = $this->sessionHandle->getSessionParameter('redirect_to');
        $msg = $this->sessionHandle->getSessionParameter('msg');
        $this->sessionHandle->unsetSessionParameter('msg');
        $error = $this->sessionHandle->getSessionParameter('error');
        $this->sessionHandle->unsetSessionParameter('error');
        if(!empty($msg)) {
            $this->view->msg = $msg;
        }
        if(!empty($error)) {
            $this->view->error = $error;
        }
        $this->view->attempts = 10 - $this->sessionHandle->getNumberOfTries();
        $loginForm = new Application_Form_Login ($this->sessionHandle->isCaptcha());
        // if one of the users whose usernames were changed
        $userIdNamespace = new Zend_Session_Namespace('iserIdNamespace');
        if (!is_null($userIdNamespace->change_id)) {
            $mapper = new Application_Model_Mapper_Person();
            $person = $mapper->fetch($userIdNamespace->change_id);
            $loginForm->populate(array(
                'username' => $person->getUsername()
            ));
            $loginForm->setAction($this->view->url(array(
                'module'     => 'default',
                'controller' => 'login',
                'action'     => 'index'
            ), null, true));
            $userIdNamespace->unsetAll();
            Zend_Session::namespaceUnset('userIdNamespace');
        // if in remember me table, populate form with credentials
        } elseif ($credentials = $this->sessionHandle->checkRememberMe()) {
            $loginForm->populate($credentials);
        }
        $this->view->loginForm = $loginForm;
        if ($this->getRequest ()->isPost ()) {
            $formData = $this->getRequest ()->getPost ();
            if ($loginForm->isValid ( $formData )) {
                $username = $loginForm->getValue('username');
                $password = $loginForm->getValue('password');
                
                //check if blocked
                if($this->sessionHandle->isBlocked($username)) {
                    //log the login attempt to database
                    $this->sessionHandle->logAttempt(false, $username);
                    $this->sessionHandle->setSessionParameter('error', 'That username has been blocked. Please contact the administrator');
                    $this->_helper->redirector('blocked','login','default', array('username'=>$username));
                } else {
                    //not blocked
                    if($this->sessionHandle->startSession($username, $password)) {
                        //check if user has selected remember me
                        if($loginForm->remember->isChecked()) {
                            /*
                             * $this->sessionHandle->rememberMe($username, $password);
                             * This remembers the user session for a year and regenerates session ID
                             */
                            Zend_Session::rememberMe(365 * 24 * 60 * 60);
                            //log the login attempt to database
                            $this->sessionHandle->logAttempt(true, $username, true);
                            //redirect back to source
                            if(empty($redirect_to)) {
                                $this->_helper->redirector('index','index', 'default');
                            }else {
                                //get module, controller, action, params to redirect to
                                $params = $this->sessionHandle->getSessionParameter('params');
                                $gets = $this->sessionHandle->getSessionParameter('get_params');
                                $this->sessionHandle->unsetRedirectParams();
                                $params = $params ? $params : array(
                                    'module' => 'default',
                                    'controller' => 'index',
                                    'action'     => 'index');
                                $gets = $gets ? $gets : array();
                                //create url and redirect
                                $this->_helper->redirector($params['action'],$params['controller'],$params['module'], $gets);
                            }
                        }else {
                            //regenerate session id - enhance security of user's session
                            Zend_Session::regenerateId();
                            //log the login attempt to database
                            $this->sessionHandle->logAttempt(true, $username);
                            //redirect
                            if(empty($redirect_to)) {
                                $this->_helper->redirector('index','index', 'default');
                            }else {
                                //get module, controller, action, params to redirect to
                                $params = $this->sessionHandle->getSessionParameter('params');
                                $gets = $this->sessionHandle->getSessionParameter('get_params');
                                $this->sessionHandle->unsetRedirectParams();
                                $params = $params ? $params : array(
                                    'module' => 'default',
                                    'controller' => 'index',
                                    'action'     => 'index');
                                $gets = $gets ? $gets : array();
                                //create url and redirect
                                $this->_helper->redirector($params['action'],$params['controller'],$params['module'],$gets);
                            }
                        }
                    }else {
                        //log the login attempt to database
                        $this->sessionHandle->logAttempt(false, $username);
                        $this->sessionHandle->incrementTries();
                        $this->view->error = $this->sessionHandle->getErrorMessage();
                        $details = array();
                        $details['username'] = $loginForm->getValue('username');
                        $loginForm->populate($details);
                    }
                }
            }
        }
    }

    public function logoutAction() {
        // action body
        $this->sessionHandle->endSession();
        $this->_helper->redirector('index', 'index', 'default');
    }

    public function recoverpasswordAction() {
        // action body
        $error = $this->sessionHandle->getSessionParameter('error');

        $this->sessionHandle->unsetSessionParameter('error');

        if(!empty($error)) {
            $this->view->error = $error;
        }

        if($this->sessionHandle->noUserSession()) {
            $form = new Application_Form_Recover();
            $this->view->form = $form;

            if($this->getRequest()->isPost()) {
                $request = $this->getRequest();

                if($form->isValid($request->getPost())) {
                    $data = $form->getValues();
                    $person = new Application_Model_Mapper_Person_PasswordRecovery();
                    try {
                        $result = $person->create($data['email']);
                        if($result) {
                            $this->sessionHandle->setSessionParameter('msg', 'A password recovery email has been sent to '.$data['email']);
                            $this->sessionHandle->endSession();
                            $this->_helper->redirector('index', 'login','default');
                        }else {
                            $client = Application_Model_Client::getInstance();
                            $this->sessionHandle->setSessionParameter('error', $client->getResponseMessage());
                            $form->populate($data);
                        }
                    }catch (Application_Model_Mapper_Person_PasswordRecovery_Exception $ex) {
                        $client = Application_Model_Client::getInstance();
                        $body = $client->getResponseBody();
                        $this->sessionHandle->setSessionParameter('error', $client->getResponseMessage().' '.$body['messages'][0]);
                        
                        $form->populate($data);
                        $this->_helper->redirector('recoverpassword', 'login','default');
                    }
                }
            }
        }else {
            $this->sessionHandle->setSessionParameter('Error', 'An error occurred');
            $this->_helper->redirector('index', 'login');
        }
    }

    public function blockedAction() {
        $error = $this->sessionHandle->getSessionParameter('error');
        $this->sessionHandle->unsetSessionParameter('error');
        if(!empty($error)) {
            $this->view->error = $error;
        }
        $this->view->username = $this->_getParam('username');
    }

    public function reinstateAction() {
        if($this->_getParam('username')) {
            if($this->sessionHandle->unblockRequest($this->_getParam('username'))) {
                $this->sessionHandle->setSessionParameter('msg', 'Request has been sent. Be assured that our support team is working on reactivating your account');
                $this->_helper->redirector('index','login');
            }else {
                $this->sessionHandle->setSessionParameter('error', 'Could send request');
                $this->_helper->redirector('blocked','login');
            }
        }else {
            $this->sessionHandle->setSessionParameter('error', 'No user name specified');
            $this->_helper->redirector('blocked','login');
        }
    }
}







