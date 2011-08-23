<?php

class Campuswisdom_ExpsController extends Zend_Controller_Action {
    public function init() {
        /* Initialize action controller here */
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
     
    }
    public function indexAction() {
        try {
        	$this->_getParam('expid');
	        $ses = new Application_Model_Session();
	        $ses->startSession();
	        $msg = $ses->getSessionParameter('msg');
	        if (!empty($msg)) {
	            $this->view->msg = $ses->getSessionParameter('msg');
	            $ses->unsetSessionParameter('msg');
	        }
            $show = new Campuswisdom_Model_ExpMapper();
            $shows = $show->showAll();
            if (empty($shows)){
                $this->view->error1='there is no experience under selected category';
            }
            $paginator = new Application_Model_ZendPagination();
            //if(!empty($shows)){
            	$this->view->result = $paginator->paginate($shows, $this->_getParam('page'));                        
        } catch (Exception $e) {
        	$this->view->error = 'kindly try again';
        }
    }

    public function experiencesAction() {
    	/* Saves User experience to the database and returns a success message */
        $id = $this->_getParam('expid');
        $ses = new Application_Model_Session();
        $ses->startSession();
        if ($ses->getSessionParameter('view') == 1) {
            $ses->unsetSessionParameter('view');
            $this->view->msg = "Thank you for sharing your views";
        }
        $request = $this->getRequest();
        $form = new Campuswisdom_Form_Exps();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $mapper = new Campuswisdom_Model_ExpMapper();
                $Category = $this->_getParam('Category');
                $Name = $this->_getParam("Name");
                $Views = $this->_getparam('Views');
                $dbTable = new Campuswisdom_Model_DbTable_Exps();
                $mapper->setDbTable($dbTable);
                $mapper->simplesave($Category, $Name, $Views);
                $ses->setSessionParameter('msg', "Your Experience has been added");
                $this->_helper->_redirector(array('controller' => 'experiences', 'action' => 'getexperiences'));
            }
        }
       
    }

    public function commentAction() {
    /* Allows addition of comments to an experience or other comments */
        $ses = new Application_Model_Session();
        $ses->startSession();
        {
            $request = $this->getRequest();
            $ExpId = $this->_getParam('expid');
            $form = new Campuswisdom_Form_Comment();
            $this->view->form = $form;
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($request->getPost())) {
                    $mapper = new Campuswisdom_Model_ExpMapper();
                    $Comment = $form->getValue("comment");
                    $mapper->setDbTable('campuswisdom_Model_DbTable_Comments');
                    $smthn = $mapper->save($ExpId, $Comment);
                    if ($smthn == null) {
                        //an error occurred so nothing was saved
                    } else {
                        $ses->setSessionParameter('comnt', 1);
                    }
                    $this->_helper->redirector('viewcomments', 'exps', 'default', array('expid' => $ExpId));
                }
            }
        }
    }

    public function viewcommentsAction() {
    /* Displays all the coments under a given experince */
        $id = $this->_getParam('expid');
        $ses = new Application_Model_Session();
        $ses->startSession();
        if ($ses->getSessionParameter('comnt') == 1) {
            $ses->unsetSessionParameter('comnt');
            $this->view->msg = "Your comment has been successfully added";        }
            $views = new Campuswisdom_Model_ExpMapper();
            $view = $views->ViewComments($id);
            $this->view->result1=$id;
            $number = count($view);
           
            if($number>0){
        try {     	

            $paginator = new Application_Model_ZendPagination();
            $this->view->result = $paginator->paginate($view, $this->_getParam('page'));
          
         }catch (Exception $e) {

            $this->view->error = 'please try another category';
        }}
        else{
            $this->view->error='No comments added see comment';
        }
    }
    public function viewnamecommentsAction() {
    /* Displays paginated comments of agiven Link Title that are more or less than five */
        $id = $this->_getParam('expid');
        $name = $this->_getParam('name');
        $ses = new Application_Model_Session();
        $ses->startSession();
        if ($ses->getSessionParameter('comnt') == 1) {
            $ses->unsetSessionParameter('comnt');
            $this->view->msg = "Your comment has been successfully added";        }
        try {
            $views = new Campuswisdom_Model_ExpMapper();
            $view = $views->ViewComments($id);
            $number = count($view);
            $this->view->count = $number;
            $this->view->result = $view;
        } catch (Exception $e) {
            $this->view->error = 'Kindly try adiffent category';
        }
    }
}

