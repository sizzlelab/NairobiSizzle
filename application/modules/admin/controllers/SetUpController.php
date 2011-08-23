<?php

class Admin_SetUpController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $request      = $this->getRequest();
        $peoplemapper = new Application_Model_Mapper_People();
        $form         = new Application_Form_Search();
        $configs      = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        $adminRole    = $configs['clients']['asi']['admins']['super'];
        $this->view->adminRole = $adminRole;
        $this->view->form = $form;
        $this->view->page = $request->getParam('page');

        if ($request->isPost()) {
            //if setting admin
            if ($request->getPost('submit') == 'Set Admin') {
                $ids = $request->getPost('user');
                if (!$ids) {
                    $this->view->errors = array('Please select a person to set as admin');
                } else {
                    /*update the selected profiles*/
                    foreach ($ids as $id) {
                        $personMapper = new Application_Model_Mapper_Person();
                        $personMapper->update(array(
                            'role' => $adminRole
                        ), $id);
                        $this->view->adminSet = true;
                    }
                }
            //if unsetting admin
            } elseif ($request->getPost('submit') == 'Remove Admin') {
                $ids = $request->getPost('user');
                if (!$ids) {
                    $this->view->errors = array('Please select a person to remove as admin');
                } else {
                    /*update the selected profiles*/
                    foreach ($ids as $id) {
                        $personMapper = new Application_Model_Mapper_Person();
                        $personMapper->update(array(
                            'role' => 'user'
                        ), $id);
                        $this->view->adminRemoved = true;
                    }
                }
            //if a search
            } elseif ($request->getPost('submit') == 'Search') {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $search = $form->getValue('search');
                //restart paging
                $request->setParam('page', 1);
                //set search term
                $peoplemapper->setSearchTerm($search);
                $this->view->searchTerm = $search;
            }
        }

        //pagination
        $page = $request->getParam('page');
        $page = $page ? $page : 1;
        $peoplemapper->setPage($page)
               ->setPerPage(10)
               ->setSortOrder('ascending');

        //fetch
        try {
            $people     = $peoplemapper->fetch();
            $pagination = $peoplemapper->getPagination();
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Null($pagination->getSize()));
            $paginator->setItemCountPerPage($pagination->getPerPage())
                      ->setCurrentPageNumber($pagination->getPage())
                      ->setPageRange(5);
            $this->view->paginator = $paginator;
            $this->view->people = $people;
        } catch (Application_Model_Mapper_People_Exception $e) {
            $errors = $peoplemapper->getErrors();
            $this->view->errors = $errors;
        }
        return $this->render();
    }


}

