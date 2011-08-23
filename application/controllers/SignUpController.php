<?php
class SignUpController extends Zend_Controller_Action {

    public function init() {
    }

    public function indexAction() {
        $form = new Application_Form_SignUp();
        $this->view->form = $form;

        $request = $this->getRequest();

        if ($request->isPost()) {
            $error = false;
            if (!$form->isValid($request->getPost())) {
                $this->view->form = $form;
                $error = true;
            }
            if (!$form->getElement('consent')->isChecked()) {
                $form->getElement('consent')->addError('You must agree to the terms of use');
                $error = true;
            }
            if ($form->getValue('password') !== $form->getValue('confirm_password')) {
                $form->getElement('confirm_password')->addError('The passwords you entered do not match');
                $this->view->form = $form;
                $error = true;
            }
            if ($error) {
                return $this->render();
            }

            $mapper = new Application_Model_Mapper_Person();
            try {
                $person = $mapper->create(array(
                        'username' => $form->getValue('username'),
                        'password' => $form->getValue('password'),
                        'email'    => $form->getValue('email'),
                        'consent'  => 'EN1.5',
                        'is_association' => false
                        ), true);
                //store newly created person in session
                $credentials = new Zend_Session_Namespace('credentials');
                $credentials->user_id = $person->getId();
                //go to profile page
                return $this->_helper->redirector('index', 'person');
            } catch (Application_Model_Mapper_Person_Exception $e) {
                $errors = $mapper->getErrors();
                //reformat errors
                if (in_array('Username has already been taken', $errors)) {
                    $key = array_keys($errors, 'Username has already been taken');
                    $form->getElement('username')->addError('This username has
                        already been taken. Please try a different one.');
                    unset($errors[$key[0]]);
                }
                if (in_array('Email has already been taken', $errors)) {
                    $key = array_keys($errors, 'Email has already been taken');
                    $form->getElement('email')->addError('This email is already
                        in use by another user. Please enter a different email.');
                    unset($errors[$key[0]]);
                }
                //pass along any other errors
                $this->view->errors = $errors;
                $this->view->form   = $form;
                return $this->render();
            }
        }
    }
}