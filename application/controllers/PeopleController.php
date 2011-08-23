<?php
class PeopleController extends Zend_Controller_Action {
    public function init() {
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        $this->id = $session->getUserId();
        $this->namespace = new Zend_Session_Namespace('credentials');
    }

    public function indexAction() {
        $requested = $this->namespace->requested;
        if ($requested) {
            $this->view->requested = $requested;
            unset($this->namespace->requested);
        }
        $request = $this->getRequest();
        $mapper  = new Application_Model_Mapper_People();
        $form    = new Application_Form_Search();
        $this->view->form = $form;
        $this->view->page = $request->getParam('page');

        //if a search
        if ($request->isPost()) {
            if (!$form->isValid($request->getPost())) {
                $this->view->form = $form;
                return $this->render();
            }
            $search = $form->getValue('search');
            //restart paging
            $request->setParam('page', 1);
            //set search term
            $mapper->setSearchTerm($search);
            $this->view->searchTerm = $search;
        }

        //pagination
        $page = $request->getParam('page');
        $page = $page ? $page : 1;
        $mapper->setPage($page)
               ->setPerPage(10)
               ->setSortBy('status_changed')
               ->setSortOrder('descending');

        //fetch
        try {
            $people     = $mapper->fetch();
            $pagination = $mapper->getPagination();
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Null($pagination->getSize()));
            $paginator->setItemCountPerPage($pagination->getPerPage())
                      ->setCurrentPageNumber($pagination->getPage())
                      ->setPageRange(5);
            $this->view->paginator = $paginator;
            $this->view->people = $people;
        } catch (Application_Model_Mapper_People_Exception $e) {
            $errors = $mapper->getErrors();
            $this->view->errors = $errors;
        }
        return $this->render();
    }
}