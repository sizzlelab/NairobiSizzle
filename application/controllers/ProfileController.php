<?php
class ProfileController extends Zend_Controller_Action {
    public function init() {}

    public function indexAction() {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            return $this->render();
        }
        $mapper = new Application_Model_Mapper_Person();
        $this->view->person = $mapper->fetch($id);
    }

    public function basicInfoAction() {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            return $this->render();
        }
        $mapper = new Application_Model_Mapper_Person();
        $this->view->person = $mapper->fetch($id);
    }

    public function contactInfoAction() {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            return $this->render();
        }
        $mapper = new Application_Model_Mapper_Person();
        $this->view->person = $mapper->fetch($id);
    }

    public function courseInfoAction() {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            return $this->render();
        }
        $mapper = new Application_Model_Mapper_Person();
        $person = $mapper->fetch($id);
        $listMapper = new Yearbook_Model_Classlist();
        $this->view->course = $listMapper->getCourse($person->getId());
        $this->view->person = $person;
    }

    public function friendsAction() {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            return $this->render();
        }
        $mapper = new Application_Model_Mapper_Person();
        $this->view->person = $mapper->fetch($id);
        $mapper = new Application_Model_Mapper_Person_Friends();
        $request = $this->getRequest();
        $page = $request->getParam('page');
        $page = $page ? $page : 1;
        $mapper->setPage($page)
               ->setPerPage(10)
               ->setSortBy('status_changed')
               ->setSortOrder('descending');
        $friends    = $mapper->fetch($id);
        $pagination = $mapper->getPagination();
        $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Null($pagination->getSize()));
        $paginator->setItemCountPerPage($pagination->getPerPage())
                  ->setCurrentPageNumber($pagination->getPage())
                  ->setPageRange(5);
        $this->view->paginator = $paginator;
        $this->view->friends = $friends;
    }
}
