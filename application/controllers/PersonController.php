<?php
class PersonController extends Zend_Controller_Action {
    /**
     * Stores an instance of {@link Application_Model_Person}.
     *
     * @var Application_Model_Person
     */
    protected $person = null;

    /**
     * Stores an instance of {@link Application_Model_Mapper_Person} for mapping
     * {@link Application_Model_Person} data requests to ASI.
     *
     * @var Application_Model_Mapper_Person
     */
    protected $mapper = null;

    public function init() {
        /*get global session*/
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        /*set up the person's mapper*/
        $id = $session->getUserId();
        if (!$id) {
            //something must have gone wrong
            $this->_helper->redirector('index','login');
        }
        $mapper = new Application_Model_Mapper_Person(new Application_Model_Person(array('id' => $id)));
        /*prefetch person's profile*/
        try {
            $this->person = $mapper->fetch();
        } catch (Application_Model_Mapper_Person_Exception $e) {
            //proceed to action, that'll trigger an error in the view
        }
        /*globalize the mapper in the scope of this class*/
        $this->mapper = $mapper;
        $this->session = $session;
    }

    public function indexAction() {
        $request = $this->getRequest();
        $updated = $request->getParam('updated');
        $statusUpdated = $request->getParam('status-updated');
        if ($updated && $updated == 1) {
            $this->view->updated = true;
        }
        if ($statusUpdated && $statusUpdated == 1) {
            $this->view->statusUpdated = true;
        }
        $this->view->person = $this->person;
        return $this->render();
    }

    public function contactInfoAction() {
        $updated = $this->getRequest()->getParam('updated');
        if ($updated && $updated == 1) {
            $this->view->updated = true;
        }
        $this->view->person = $this->person;
        return $this->render();
    }

    public function courseInfoAction() {
        $this->view->message = $this->session->getSessionParameter('message');
        $this->session->unsetSessionParameter('message');
        $watu = new Yearbook_Model_Classlist();
        $this->view->course = $watu->getCourse($this->person->getId());
        $this->view->person = $this->person;
    }

    public function editCourseAction() {
        $this->view->person = $this->person;
        $request = $this->getRequest();
        $form = new Yearbook_Form_Classlist();
        $form->submit->setLabel('Join');
        $this->view->form = $form;
        
        $user_id = $this->person->id;

        $watu = new Yearbook_Model_Classlist();

        $info = $watu->getCourse($user_id);

        if($request->isGet()){
            if ($info) {
                $form->getElement('course')->setValue($info['course']);
                $form->getElement('year')->setValue($info['year']);
            }
        } elseif($request->isPost()){
            if($form->isValid($request->getpost())) {
                $course = $form->getValue('course');
                $year = $form->getValue('year');
                if($info){
                    $watu->update(array(
                        'course' => $course,
                        'year'   => $year
                    ), array('user_id = ?' => $info['user_id']));
                }else{
                    $affected = $watu->insertclasslist($user_id, $course, $year);
                    if(empty($affected)){
                        $this->view->error='Sorry an error occurred. Please try again later';
                        return $this->render();
                    }
                }
                $this->session->setSessionParameter('message', "You have been added to {$course}, {$year}");
                $this->_helper->redirector('course-info', 'person', 'default');
            }
        }
        return $this->render();
    }

    public function statusAction() {
        $request = $this->getRequest();
        $form = new Application_Form_Person_StatusMessage();
        $this->view->form = $form;
        if ($request->isPost()) {
            if (!$form->isValid($request->getPost())) {
                $this->view->form = $form;
                return $this->render();
            }
            $status = $form->getValue('status_message');
            try {
                $this->mapper->update(array('status_message' => $status));
                return $this->_helper->redirector('index', null, null, array('status-updated' => 1));
            } catch (Application_Model_Mapper_Person_Exception $e) {
                $this->view->errors = $this->mapper->getErrors();
            }
        }
    }

    public function settingsAction() {
        $request = $this->getRequest();
        $emailUpdated = $request->getParam('email-updated');
        $usernameUpdated = $request->getParam('username-updated');
        $passwordUpdated = $request->getParam('password-updated');
        if ($emailUpdated && $emailUpdated == 1) {
            $this->view->emailUpdated = true;
        }
        if ($usernameUpdated && $usernameUpdated == 1) {
            $this->view->usernameUpdated = true;
        }
        if ($passwordUpdated && $passwordUpdated == 1) {
            $this->view->passwordUpdated = true;
        }
        $this->view->person = $this->person;
        return $this->render();
    }

    public function editUsernameAction() {
        $request = $this->getRequest();
        $form = new Application_Form_Person_Username();
        $this->view->form = $form;
        if ($request->isGet()) {
            if ($this->person->username) {
                $form->getElement('username')->setValue($this->person->username);
            }
        } elseif ($request->isPost()) {
            if (!$form->isValid($request->getPost())) {
                $this->view->form = $form;
                return $this->render();
            }
            $username = $form->getValue('username');
            try {
                $this->mapper->update(array('username' => $username));
                $this->person->setUsername($username);
                return $this->_forward('settings', null, null, array('username-updated' => 1));
            } catch (Application_Model_Mapper_Person_Exception $e) {
                $this->view->errors = $this->mapper->getErrors();
                return $this->render();
            }
        }
    }

    public function editPasswordAction() {
        $request = $this->getRequest();
        $form = new Application_Form_Person_Password();
        $this->view->form = $form;
        if ($request->isPost()) {
            $error = false;
            if (!$form->isValid($request->getPost())) {
                $this->view->form = $form;
                $error = true;
            }
            $creds = new Zend_Session_Namespace('credentials');
            if ($form->getValue('old_password') !== $creds->password) {
                $form->getElement('old_password')->addError('The password you entered is incorrect');
                $this->view->form = $form;
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
            $password = $form->getValue('password');
            try {
                $this->mapper->update(array('password' => $password));
                return $this->_forward('settings', null, null, array('password-updated' => 1));
            } catch (Application_Model_Mapper_Person_Exception $e) {
                $this->view->errors = $this->mapper->getErrors();
                return $this->render();
            }
        }
    }

    public function editEmailAction() {
        $request = $this->getRequest();
        $form = new Application_Form_Person_Email();
        $this->view->form = $form;
        if ($request->isGet()) {
            if ($this->person->email) {
                $form->getElement('email')->setValue($this->person->email);
            }
        } elseif ($request->isPost()) {
            if (!$form->isValid($request->getPost())) {
                $this->view->form = $form;
                return $this->render();
            }
            $password = $form->getValue('email');
            try {
                $this->mapper->update(array('email' => $password));
                $this->person->setEmail($password);
                return $this->_forward('settings', null, null, array('email-updated' => 1));
            } catch (Application_Model_Mapper_Person_Exception $e) {
                $this->view->errors = $this->mapper->getErrors();
                return $this->render();
            }
        }
    }

    public function editNameAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        if ($clear && $clear == 1) {
            $this->mapper->update(array(
                'name' => array(
                    'given_name' => '',
                    'family_name' => ''
                )
            ));
            $this->person->setName(null);
            return $this->_forward('index', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_Name();
            $this->view->form = $form;
            if ($request->isGet()) {
                if ($this->person->name) {
                    $form->getElement('given_name')->setValue($this->person->name->given_name);
                    $form->getElement('family_name')->setValue($this->person->name->family_name);
                }
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $formData = $form->getValues();
                try {
                    $this->mapper->update(array('name' => $formData));
                    $this->person->setName($formData);
                    return $this->_forward('index', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                    return $this->render();
                }
            }
        }
    }

    public function editAvatarAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        $avatarMapper = new Application_Model_Mapper_Person_Avatar();
        $avatarMapper->setPerson($this->person);
        if ($clear && $clear == 1) {
            try {
                $avatar = $avatarMapper->delete();
            } catch (Application_Model_Mapper_Person_Avatar_Exception $e) {
                
            }
            return $this->_forward('index', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_Avatar();
            $this->view->form = $form;
            if ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $avatar = $form->getElement('avatar');
                if (!$avatar->receive()) {
                    $avatar->addError('An error occurred while receiving your file, please try again');
                    $this->view->form = $form;
                    return $this->render();
                }
                $filename = $avatar->getFileName();
                try {
                    //update avatar
                    $avatarMapper->update($filename);
                    //delete file on disk
                    unlink($filename);
                    return $this->_forward('index', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Avatar_Exception $e) {
                    $this->view->errors = $avatarMapper->getErrors();
                }
            }
        }
    }

    public function editGenderAction() {
        $request = $this->getRequest();
        $form    = new Application_Form_Person_Gender();
        $this->view->form = $form;
        if ($request->isGet()) {
            if ($this->person->gender) {
                $form->getElement('gender')->setValue($this->person->gender);
            }
        } elseif ($request->isPost()) {
            if (!$form->isValid($request->getPost())) {
                $this->view->form = $form;
                return $this->render();
            }
            $gender = $form->getValue('gender');
            try {
                $this->mapper->update(array('gender' => $gender));
                $this->person->setGender($gender);
                return $this->_forward('index', null, null, array('updated' => 1));
            } catch (Application_Model_Mapper_Person_Exception $e) {
                $this->view->errors = $this->mapper->getErrors();
                return $this->render();
            }
        }
    }

    public function editBirthdateAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        if ($clear && $clear == 1) {
            $this->mapper->update(array(
                'birthdate' => ''
            ));
            $this->person->setBirthdate('');
            return $this->_forward('index', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_Birthdate();
            $this->view->form = $form;
            if ($request->isGet()) {
                if ($this->person->birthdate) {
                    $date  = new Zend_Date($this->person->birthdate);
                    $day   = $date->get('d');
                    $month = $date->get('MM');
                    $year  = $date->get('y');
                    $form->getElement('day')->setValue($date->get('d'));
                    $form->getElement('month')->setValue($date->get('MM'));
                    $form->getElement('year')->setValue($date->get('y'));
                }
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $day   = $form->getValue('day');
                $month = $form->getValue('month');
                $year  = $form->getValue('year');
                $date  = $year . '-' . $month . '-' . $day;
                try {
                    $this->mapper->update(array('birthdate' => $date));
                    $this->person->setBirthdate($date);
                    return $this->_forward('index', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                    return $this->render();
                }
            }
        }
    }

    public function editDescriptionAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        if ($clear && $clear == 1) {
            $this->mapper->update(array(
                'description' => ''
            ));
            $this->person->setDescription('');
            return $this->_forward('index', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_Description();
            $this->view->form = $form;
            if ($request->isGet()) {
                if ($this->person->description) {
                    $form->getElement('description')->setValue($this->person->description);
                }
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $description = $form->getValue('description');
                try {
                    $this->mapper->update(array('description' => $description));
                    $this->person->setDescription($description);
                    return $this->_forward('index', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                    return $this->render();
                }
            }
        }
    }

    public function editPhoneNumberAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        if ($clear && $clear == 1) {
            $this->mapper->update(array(
                'phone_number' => ''
            ));
            $this->person->setPhoneNumber('');
            return $this->_forward('contact-info', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_PhoneNumber();
            $this->view->form = $form;
            if ($request->isGet()) {
                if ($this->person->phone_number) {
                    $form->getElement('phone_number')->setValue($this->person->phone_number);
                }
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $phone_number = $form->getValue('phone_number');
                try {
                    $this->mapper->update(array('phone_number' => $phone_number));
                    $this->person->setPhoneNumber($phone_number);
                    return $this->_forward('contact-info', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                    return $this->render();
                }
            }
        }
    }

    public function editMsnNickAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        if ($clear && $clear == 1) {
            $this->mapper->update(array(
                'msn_nick' => ''
            ));
            $this->person->setMsnNick('');
            return $this->_forward('contact-info', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_MsnNick();
            $this->view->form = $form;
            if ($request->isGet()) {
                if ($this->person->msn_nick) {
                    $form->getElement('msn_nick')->setValue($this->person->msn_nick);
                }
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $msnNick = $form->getValue('msn_nick');
                try {
                    $this->mapper->update(array('msn_nick' => $msnNick));
                    $this->person->setMsnNick($msnNick);
                    return $this->_forward('contact-info', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                    return $this->render();
                }
            }
        }
    }

    public function editIrcNickAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        if ($clear && $clear == 1) {
            $this->mapper->update(array(
                'irc_nick' => ''
            ));
            $this->person->setIrcNick('');
            return $this->_forward('contact-info', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_IrcNick();
            $this->view->form = $form;
            if ($request->isGet()) {
                if ($this->person->irc_nick) {
                    $form->getElement('irc_nick')->setValue($this->person->irc_nick);
                }
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $ircNick = $form->getValue('irc_nick');
                try {
                    $this->mapper->update(array('irc_nick' => $ircNick));
                    $this->person->setIrcNick($ircNick);
                    return $this->_forward('contact-info', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                    return $this->render();
                }
            }
        }
    }

    public function editWebsiteAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        if ($clear && $clear == 1) {
            $this->mapper->update(array(
                'website' => ''
            ));
            $this->person->setWebsite('');
            return $this->_forward('contact-info', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_Website();
            $this->view->form = $form;
            if ($request->isGet()) {
                if ($this->person->website) {
                    $form->getElement('website')->setValue($this->person->website);
                }
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $website = $form->getValue('website');
                if (!Zend_Uri::check($website)) {
                    $form->getElement('website')->addError('The URL you provided is not valid');
                    $this->view->form = $form;
                    return $this->render();
                }
                try {
                    $this->mapper->update(array('website' => $website));
                    $this->person->setWebsite($website);
                    return $this->_forward('contact-info', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                    return $this->render();
                }
            }
        }
    }

    public function editLocationAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        if ($clear && $clear == 1) {
            $mapper = new Application_Model_Mapper_Person_Location();
            $mapper->setPerson($this->person)
                   ->setLocation(new Application_Model_Person_Location(array(
                       'label'     => '',
                       'latitude'  => 0.0,
                       'longitude' => 0.0,
                       'accuracy'  => 0.0
                   )))
                   ->update();
            $this->person->setLocation(null);
            return $this->_forward('contact-info', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_Location();
            $this->view->form = $form;
            if ($request->isGet()) {
                if ($this->person->location) {
                    $form->getElement('label')->setValue($this->person->location->label);
                }
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $formData = $form->getValues();
                $mapper = new Application_Model_Mapper_Person_Location();
                try {
                    $mapper->setPerson($this->person)
                           ->setLocation(new Application_Model_Person_Location($formData));
                    if ($this->person->location) {
                        $mapper->update();
                    } else {
                        $mapper->create();
                    }
                    $this->person->setLocation($formData);
                    return $this->_forward('contact-info', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                    return $this->render();
                }
            }
        }
    }

    public function editAddressAction() {
        $request = $this->getRequest();
        $clear   = $request->getParam('clear');
        if ($clear && $clear == 1) {
            $this->mapper->update(array(
                'address' => array(
                    'postal_code'    => '',
                    'locality'       => '',
                    'street_address' => ''
                )
            ));
            $this->person->setAddress(null);
            return $this->_forward('contact-info', null, null, array('updated' => 1));
        } else {
            $form = new Application_Form_Person_Address();
            $this->view->form = $form;
            if ($request->isGet()) {
                if ($this->person->address) {
                    $form->getElement('postal_code')->setValue($this->person->address->postal_code);
                    $form->getElement('locality')->setValue($this->person->address->locality);
                    $form->getElement('street_address')->setValue($this->person->address->street_address);
                }
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $formData = $form->getValues();
                try {
                    $this->mapper->update(array('address' => $formData));
                    $this->person->setAddress($formData);
                    return $this->_forward('contact-info', null, null, array('updated' => 1));
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                    return $this->render();
                }
            }
        }
    }

    public function deleteAction() {
        try {
            $this->mapper->delete();
        } catch (Application_Model_Mapper_Person_Exception $e) {
            $this->view->errors[] = 'An error occurred while processing your
                        request, please try again later';
        }
        return $this->render();
    }

    public function viewFullInfoAction() {
        $id = $this->getRequest()->getParam('id');
        $id = $id ? $id : $this->person->getId();
        return $this->_helper->redirector('index', 'profile', 'default', array('id' => $id));
    }

    public function viewBasicInfoAction() {
        $id = $this->getRequest()->getParam('id');
        $id = $id ? $id : $this->person->getId();
        return $this->_helper->redirector('basic-info', 'profile', 'default', array('id' => $id));
    }

    public function viewContactInfoAction() {
        $id = $this->getRequest()->getParam('id');
        $id = $id ? $id : $this->person->getId();
        return $this->_helper->redirector('contact-info', 'profile', 'default', array('id' => $id));
    }

    public function viewCourseInfoAction() {
        $id = $this->getRequest()->getParam('id');
        $id = $id ? $id : $this->person->getId();
        return $this->_helper->redirector('course-info', 'profile', 'default', array('id' => $id));
    }
}