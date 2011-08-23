<?php
class FriendsController extends Zend_Controller_Action {
    public function init() {
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        $this->id = $session->getUserId();
        $this->namespace = new Zend_Session_Namespace('credentials');
    }

    public function indexAction() {
        $removed = $this->namespace->removed;
        if ($removed) {
            $this->view->removed = $removed;
            unset($this->namespace->removed);
        }
        $mapper = new Application_Model_Mapper_Person_Friends();
        $request = $this->getRequest();
        $page = $request->getParam('page');
        $page = $page ? $page : 1;
        $mapper->setPage($page)
               ->setPerPage(10)
               ->setSortBy('status_changed')
               ->setSortOrder('descending');
        
        $id = $request->getParam('id');
        $id = $id ? $id : $this->id;
        try {
            $friends    = $mapper->fetch($id);
            $pagination = $mapper->getPagination();
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Null($pagination->getSize()));
            $paginator->setItemCountPerPage($pagination->getPerPage())
                      ->setCurrentPageNumber($pagination->getPage())
                      ->setPageRange(5);
            $this->view->paginator = $paginator;
            $this->view->friends = $friends;
        } catch (Application_Model_Mapper_Person_Friends_Exception $e) {
            $errors = $mapper->getErrors();
            $this->view->errors = $errors;
        }
        return $this->render();
    }
    
    public function addAction() {
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        $names   = $request->getParam('names');
        if ($id) {
            $mapper = new Application_Model_Mapper_Person_Friends();
            try {
                $mapper->create($id, $this->id);
                $this->namespace->requested = $names;
                return $this->_helper->redirector('index', 'people', 'default', array('page' => $request->getParam('page')));
            } catch (Application_Model_Mapper_Person_Friends_Exception $e) {
                $errors = $mapper->getErrors();
                $this->view->errors = $errors;
            }
        } else {
            $this->view->errors = array('An error occurred while processing your request. Please <a href="'.$this->view->url(array('action' => 'index')).'">go back</a> and try again');
        }
    }

    public function acceptAction() {
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        $names   = $request->getParam('names');
        if ($id) {
            $mapper = new Application_Model_Mapper_Person_Friends();
            try {
                $mapper->create($id, $this->id);
                $this->namespace->accepted = $names;
                return $this->_helper->redirector('requests');
            } catch (Application_Model_Mapper_Person_Friends_Exception $e) {
                $errors = $mapper->getErrors();
                $this->view->errors = $errors;
            }
        } else {
            $this->view->errors = array('An error occurred while processing your request. Please <a href="'.$this->view->url(array('action' => 'index')).'">go back</a> and try again');
        }
    }

    public function ignoreAction() {
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        $names   = $request->getParam('names');
        if ($id) {
            $mapper = new Application_Model_Mapper_Person_PendingFriendRequests();
            try {
                $mapper->delete($id, $this->id);
                $this->namespace->ignored = $names;
                return $this->_helper->redirector('requests');
            } catch (Application_Model_Mapper_Person_PendingFriendRequests_Exception $e) {
                $errors = $mapper->getErrors();
                $this->view->errors = $errors;
            }
        } else {
            $this->view->errors = array('An error occurred while processing your request. Please <a href="'.$this->view->url(array('action' => 'index')).'">go back</a> and try again');
        }
    }

    public function removeAction() {
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        $names   = $request->getParam('names');
        if ($id) {
            $mapper = new Application_Model_Mapper_Person_Friends();
            try {
                $mapper->delete($id, $this->id);
                $this->namespace->removed = $names;
                return $this->_helper->redirector('index');
            } catch (Application_Model_Mapper_Person_Friends_Exception $e) {
                $errors = $mapper->getErrors();
                $this->view->errors = $errors;
            }
        } else {
            $this->view->errors = array('An error occurred while processing your request. Please <a href="'.$this->view->url(array('action' => 'index')).'">go back</a> and try again');
        }
    }

    public function requestsAction() {
        $accepted = $this->namespace->accepted;
        if ($accepted) {
            $this->view->accepted = $accepted;
            unset($this->namespace->accepted);
        }
        $ignored = $this->namespace->ignored;
        if ($ignored) {
            $this->view->ignored = $ignored;
            unset($this->namespace->ignored);
        }
        $mapper = new Application_Model_Mapper_Person_PendingFriendRequests();
        $request = $this->getRequest();
        $page = $request->getParam('page');
        $page = $page ? $page : 1;
        $mapper->setPage($page)
               ->setPerPage(10)
               ->setSortBy('status_changed');
        try {
            $people     = $mapper->fetch($this->id);
            $pagination = $mapper->getPagination();
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Null($pagination->getSize()));
            $paginator->setItemCountPerPage($pagination->getPerPage())
                      ->setCurrentPageNumber($pagination->getPage())
                      ->setPageRange(5);
            $this->view->paginator = $paginator;
            $this->view->requests = $people;
        } catch (Application_Model_Mapper_Person_PendingFriendRequests_Exception $e) {
            $errors = $mapper->getErrors();
            $this->view->errors = $errors;
        }
        return $this->render();
    }
}