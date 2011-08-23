<?php
class TrafficUpdates_IndexController extends Zend_Controller_Action {

    public function init() {
        /*set up user session*/
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        $this->id = $session->getUserId();
    }

    public function indexAction() {
        $mapper  = new TrafficUpdates_Model_Mapper_Routes();
        $request = $this->getRequest();
        $this->view->unsubscribed = $request->getParam('unsubscribed');
        $this->view->name = $request->getParam('name');

        //fetch routes
        $routes = $mapper->fetchRoutesSubscribed($this->id);

        //fetch most recent update, paginate
        if ($routes) {
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Array($routes));
            $paginator->setItemCountPerPage(3)
                      ->setCurrentPageNumber($request->getParam('page'))
                      ->setPageRange(5);
            $this->view->paginator = $paginator;
            $this->view->routes = $paginator->getCurrentItems();
            $this->view->id     = $this->id;
        }
    }
}