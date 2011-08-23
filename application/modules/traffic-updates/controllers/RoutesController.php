<?php
class TrafficUpdates_RoutesController extends Zend_Controller_Action {
    /**
     * Logged person's ID.
     *
     * @var string
     */
    protected $id = '';
    
    public function init() {
        /*set up user session*/
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        $this->id = $session->getUserId();
    }

    public function indexAction() {
        $request = $this->getRequest();
        $this->view->already = $request->getParam('already-subscribed');
        $this->view->subscribed = $request->getParam('subscribed');
        $this->view->name = $request->getParam('name');
        $this->view->error = $request->getParam('error');
        $mapper = new TrafficUpdates_Model_Mapper_Routes();

        //fetch
        $routes = $mapper->fetchAllRoutes();
        
        if ($routes) {
            //paginate
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Array($routes));
            $paginator->setItemCountPerPage(10)
                      ->setCurrentPageNumber($request->getParam('page'))
                      ->setPageRange(5);

            $this->view->routes    = $paginator->getCurrentItems();
            $this->view->paginator = $paginator;
        }
    }

    public function newAction() {
        $form = new TrafficUpdates_Form_Route();
        $this->view->form = $form;
        $request = $this->getRequest();
        if ($request->isPost()) {
            if (!$form->isValid($request->getPost())) {
                $this->view->form = $form;
                return $this->render();
            }
            $data = $form->getValues();
            $mapper = new TrafficUpdates_Model_Mapper_Routes();
            $mapper->createRoute($data);
            $this->_helper->redirector('my-routes', null, null, array('created' => 1));
        }
    }

    public function editAction() {
        $form = new TrafficUpdates_Form_RouteEdit();
        $this->view->form = $form;
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        if ($id) {
            $mapper = new TrafficUpdates_Model_Mapper_Routes();
            if ($request->isGet()) {
                $route = $mapper->fetchRoute($id);
                $form->getElement('name')->setValue($route->getName());
                $form->getElement('description')->setValue($route->getDescription());
            } elseif ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $data = $form->getValues();
                $mapper->updateRoute($id, $data);
                return $this->_helper->redirector('my-routes', null, null, array('updated' => 1));
            }
        } else {
            $this->view->errors = array('An error occurred, please <a href="'.$this->view->url(array('module' => 'traffic-updates', 'controller' => 'routes', 'action' => 'my-routes')).'">go back</a> and try again');
        }
    }

    public function confirmDeleteAction() {
        $request = $this->getRequest();
        $this->view->name = $request->getParam('name');
        $this->view->subs = $request->getParam('subs');
        $this->view->id = $request->getParam('id');
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $id      = $request->getParam('id');
        if ($id) {
            $mapper = new TrafficUpdates_Model_Mapper_Routes();
            $mapper->deleteRoute($id);
            $this->_helper->redirector('my-routes', null, null, array('deleted' => 1));
        } else {
            $this->view->errors = array('An error occurred, please <a href="'.$this->view->url(array('module' => 'traffic-updates', 'controller' => 'routes', 'action' => 'my-routes')).'">go back</a> and try again');
        }
    }

    public function myRoutesAction() {
        $mapper  = new TrafficUpdates_Model_Mapper_Routes();
        $request = $this->getRequest();
        $this->view->created = $request->getParam('created');
        $this->view->deleted = $request->getParam('deleted');
        $this->view->updated = $request->getParam('updated');

        //fetch
        $routes = $mapper->fetchRoutesByOwner($this->id);

        if ($routes) {
            //paginate
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Array($routes));
            $paginator->setItemCountPerPage(10)
                      ->setCurrentPageNumber($request->getParam('page'))
                      ->setPageRange(5);

            $this->view->routes    = $paginator->getCurrentItems();
            $this->view->paginator = $paginator;
        }
    }

    public function postUpdateAction() {
        $request = $this->getRequest();
        $routeId = $request->getParam('id');
        if ($routeId) {
            $form = new TrafficUpdates_Form_Update();
            $this->view->form = $form;
            if ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $data = $form->getValues();
                $mapper = new TrafficUpdates_Model_Mapper_Routes();
                $mapper->postUpdate($routeId, $data);
                $this->_helper->redirector('index', 'index', null, array('posted' => 1));
            }
        } else {
            $this->view->errors = array('An error occurred, please <a href="'.$this->view->url(array('module' => 'traffic-updates', 'controller' => 'index', 'action' => 'index')).'">go back</a> and try again');
        }
    }

    public function commentsAction() {
        $request   = $this->getRequest();
        $this->view->commented = $request->getParam('commented');
        $routeId = $request->getParam('route');
        $updateId = $request->getParam('update');
        if ($routeId && $updateId) {
            $form   = new TrafficUpdates_Form_Comment();
            $mapper = new TrafficUpdates_Model_Mapper_Routes();
            $this->view->form = $form;
            if ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $data = $form->getValues();
                $mapper->postUpdateComment($routeId, $updateId, $data);
                $this->view->commented = true;
                $form->reset();
            }
            $this->view->update   = $mapper->fetchUpdate($routeId, $updateId);
            $this->view->route    = $mapper->fetchRoute($routeId);
            $this->view->id       = $this->id;

            //paginate comments
            $page = $request->getParam('page');
            $page = $page ? $page : 1;
            $mapper->getChannelMessageRepliesMapper()->setPage($page)
                                                     ->setPerPage(10)
                                                     ->setSortOrder('ascending');
            $this->view->comments = $mapper->fetchUpdateComments($routeId, $updateId);

            //init paginator
            $pagination = $mapper->getChannelMessageRepliesMapper()->getPagination();
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Null($pagination->getSize()));
            $paginator->setItemCountPerPage($pagination->getPerPage())
                      ->setCurrentPageNumber($pagination->getPage())
                      ->setPageRange(5);
            $this->view->paginator = $paginator;
        } else {
            $this->view->errors = array('An error occurred, please <a href="'.$this->view->url(array('module' => 'traffic-updates', 'controller' => 'index', 'action' => 'index')).'">go back</a> and try again');
        }
    }

    public function updatesAction() {
        $request = $this->getRequest();
        $this->view->deleted  = $request->getParam('deleted');
        $routeId = $request->getParam('id');
        if ($routeId) {
            $form   = new TrafficUpdates_Form_Update();
            $mapper = new TrafficUpdates_Model_Mapper_Routes();
            $this->view->form = $form;
            if ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $data = $form->getValues();
                $mapper->postUpdate($routeId, $data);
                $this->view->posted = true;
                $form->reset();
            }
            $this->view->route   = $mapper->fetchRoute($routeId);
            $this->view->id      = $this->id;

            //paginate updates
            $page = $request->getParam('page');
            $page = $page ? $page : 1;
            $mapper->getChannelMessageMapper()->setPage($page)
                                                     ->setPerPage(10)
                                                     ->setSortOrder('descending');
            $this->view->updates = $mapper->fetchUpdates($routeId);

            //init paginator
            $pagination = $mapper->getChannelMessageMapper()->getPagination();
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Null($pagination->getSize()));
            $paginator->setItemCountPerPage($pagination->getPerPage())
                      ->setCurrentPageNumber($pagination->getPage())
                      ->setPageRange(5);
            $this->view->paginator = $paginator;
        } else {
            $this->view->errors = array('An error occurred, please <a href="'.$this->view->url(array('module' => 'traffic-updates', 'controller' => 'index', 'action' => 'index')).'">go back</a> and try again');
        }
    }

    public function deleteUpdateAction() {
        $request   = $this->getRequest();
        $routeId = $request->getParam('route');
        $updateId = $request->getParam('update');
        if ($routeId && $updateId) {
            $mapper = new TrafficUpdates_Model_Mapper_Routes();
            $mapper->deleteUpdate($routeId, $updateId);
            $this->_helper->redirector('updates', 'routes', 'traffic-updates', array('deleted' => 1, 'id' => $routeId));
        } else {
            $this->view->errors = array('An error occurred, please <a href="'.$this->view->url(array('module' => 'traffic-updates', 'controller' => 'index', 'action' => 'index')).'">go back</a> and try again');
        }
    }

    public function subscribeAction() {
        $request = $this->getRequest();
        $routeId = $request->getParam('id');
        if ($routeId) {
            $mapper = new TrafficUpdates_Model_Mapper_Routes();
            try {
                $mapper->subscribe($routeId, $this->id);
            } catch (Zend_Db_Statement_Exception $e) {
                $this->_helper->redirector('index', null, null, array('already-subscribed' => 1, 'name' => $request->getParam('name')));
            }
            $this->_helper->redirector('index', null, null, array('subscribed' => 1, 'name' => $request->getParam('name')));
        } else {
            $this->_helper->redirector('index', null, null, array('error' => 1));
        }
    }

    public function unsubscribeAction() {
        $request = $this->getRequest();
        $routeId = $request->getParam('id');
        if ($routeId) {
            $mapper = new TrafficUpdates_Model_Mapper_Routes();
            $mapper->unsubscribe($routeId, $this->id);
            $this->_helper->redirector('index', 'index', 'traffic-updates', array('unsubscribed' => 1, 'name' => $request->getParam('name')));
        } else {
            $this->_helper->redirector('index', null, null, array('error' => 1));
        }
    }

    public function subscribersAction() {
        $request = $this->getRequest();
        $routeId = $request->getParam('id');
        if ($routeId) {
            $mapper = new TrafficUpdates_Model_Mapper_Routes();
            $this->view->route   = $mapper->fetchRoute($routeId);
            $subscribers = $mapper->fetchSubscribers($routeId);
            //init paginator
            $paginator  = new Zend_Paginator(new Zend_Paginator_Adapter_Array($subscribers));
            $paginator->setItemCountPerPage(10)
                      ->setCurrentPageNumber($request->getParam('page'))
                      ->setPageRange(5);
            $this->view->subscribers = $paginator->getCurrentItems();
            $this->view->paginator = $paginator;
        } else {
            $this->_helper->redirector('index', null, null, array('error' => 1));
        }
    }
}