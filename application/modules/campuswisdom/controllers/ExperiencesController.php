<?php

class Campuswisdom_ExperiencesController extends Zend_Controller_Action {

    public function init() {
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        $this->session=$session;
    }

    public function indexAction() {
        /* Index action is initialized here
         */
    }

    public function getexperiencesAction() {
        /* Shows a select box for filtering Experiences in different Catgegories */
           $this->view->message = $this->session->getSessionParameter('message');
        $course = $this->session->getSessionParameter('Category');
        $this->session->unsetSessionParameter('message');
        $this->session->unsetSessionParameter('Category');
        $request = $this->getRequest();
        $form = new Campuswisdom_Form_Getexperiences();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $mapper = new Campuswisdom_Model_ExpMapper();
                $view = $form->getValue('Category');
                if ($mapper->checkCategory($view)) {
                    $table = new Campuswisdom_Model_DbTable_Exps();
                    $mapper->setDbTable($table);
                    if(count($result = $mapper->getExps($view))>0){

                    try {
                        
                    } catch (Exception $e) {
                        var_dump($e->getMessage());
                    }
                    $this->view->results = $result;
                }
                else{
                    $this->view->ans = 'Category has no Experience see +experience';
                }
                } else {
                    $this->view->ans = 'Category has no Experience see +experience';
                }
            }
        }
    }

    public function titleDetailsAction() {
        /* Filters Category Experiences by Specific Name of the category */
        $name = $this->_getParam('name');
        $views = new Campuswisdom_Model_ExpMapper();
        try {
            $view = $views->showView($name);
            $this->view->Vform = $view;
        } catch (Exception $e) {
            $this->view->error = 'No results for the selected category try again later';
        }
    }

    public function namedetailsAction() {
        /* Displays an Experience and Comments under a selected link to specific name from a given Category */
        $this->view->message = $this->session->getSessionParameter('message');
        $course = $this->session->getSessionParameter('Category');
        $this->session->unsetSessionParameter('message');
        $this->session->unsetSessionParameter('Category');
        $request = $this->getRequest();
        $form = new Campuswisdom_Form_Getexperiences();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $mapper = new Campuswisdom_Model_ExpMapper();
                $view = $form->getValue('Category');
                $this->session->setSessionParameter('Category', $view);
                if (count($mapper->Filter($view))>0) {
                    $table = new Campuswisdom_Model_DbTable_Exps();
                    $mapper->setDbTable($table);
                    try {
                        $result = $mapper->Filter($view);
                    } catch (Exception $e) {
                        var_dump($e->getMessage());
                    }
                    $pagination = new Application_Model_ZendPagination();
                    if(!empty ($result)){
                        $this->view->Filts = $pagination->paginate($result, $this->_getParam('page'));
                    }else{
                        $this->view->Filts = $result;
                    }

                } else {
                    $this->view->ans = 'No Experience for selected category see +experience';
                }
            }
        }elseif(!empty($course)){
            //this escapes the form
            //echo $course;exit;
            $mapper = new Campuswisdom_Model_ExpMapper();
            if (count($mapper->Filter($course))>0) {
                $table = new Campuswisdom_Model_DbTable_Exps();
                $mapper->setDbTable($table);
                try {
                    $result = $mapper->Filter($course);
                } catch (Exception $e) {
                    $this->view->ans = $e->getMessage();
                }
                $pagination = new Application_Model_ZendPagination();
                if(!empty ($result)){                    
                    $this->view->Filts = $pagination->paginate($result, $this->_getParam('page'));
                    //var_dump($this->view->Filts);exit;
                }else{
                    $this->view->Filts = $result;
                }
            } else {
                $this->view->ans = 'No Experience for selected category see +experience';
            }
        }else{
             $mapper = new Campuswisdom_Model_ExpMapper();
                $table = new Campuswisdom_Model_DbTable_Exps();
                $mapper->setDbTable($table);
                try {
                    $result = $mapper->showAll();
                } catch (Exception $e) {
                    $this->view->ans = $e->getMessage();
                }
                $pagination = new Application_Model_ZendPagination();
                if(!empty ($result)){
                    $this->view->Filts = $pagination->paginate($result, $this->_getParam('page'));
                    //var_dump($this->view->Filts);exit;
                }else{
                    $this->view->Filts = $result;
                }
            

        }
    }

}

