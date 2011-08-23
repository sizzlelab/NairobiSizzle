<?php

class Campuswisdom_ClassifiedsController extends Zend_Controller_Action
{

    public function init()
    {}

    public function indexAction()
    {
    	//form to select category
        $catform = new campuswisdom_Form_Category();
        $this->view->form = $catform;
        $this->view->db = new campuswisdom_Model_DbTable_Classifieds();
        $catid = $this->_getParam('catid');
        $session = new Application_Model_Session();
       	$session->startSession();
        //check if category has been selected from dropdown
        if($this->getRequest()->isPost()){
        //get category id or name
        	$request = $this->getRequest();
            //validate form
            if($catform->isValid($request->getPost())){
            	$value = $catform->getValues();
                $catid = $value['Category'];
                //query db for businesses under cat id
                $classifieds = new campuswisdom_Model_DbTable_Classifieds();
                $category = new campuswisdom_Model_DbTable_Categories();
                //all classifieds
                if($value['Category'] == 0){
                	$list = $classifieds->fetchAll('offline = 0', 'title DESC');
               	}else{
                	//category selected
                    $list = $classifieds->getCategorized($catid);
				}
                $this->view->category_name = $category->getName($catid);
                $this->view->user_id = $session->getUserId();
                $pagination = new Application_Model_ZendPagination(10);
                if(!empty($list)){
                	$this->view->classifieds = $pagination->paginate($list,$this->_getParam('page'));	
               	}else{
                	$this->view->classifieds = $list;
              	}
       		}
       	}else{
            //use catid passed as get param
            if(!empty($catid)){
	            //query db for businesses under cat id
	            $classifieds = new campuswisdom_Model_DbTable_Classifieds();
	            $category = new campuswisdom_Model_DbTable_Categories();
	           	$pagination = new Application_Model_ZendPagination();
	            $pagination->setItemCount(10);
	            $list = $classifieds->getCategorized($catid);
	            if(!empty($list)){
	             	$this->view->classifieds = $pagination->paginate($list, $this->_getParam('page'));	
				}else{
	           		$this->view->classifieds = $list;	
				}
					$this->view->user_id = $session->getUserId();
	            	$this->view->category_name = $category->getName($catid); 
	       	}else{
	       		//query db for businesses under cat id
	            $classifieds = new campuswisdom_Model_DbTable_Classifieds();
	            $category = new campuswisdom_Model_DbTable_Categories();
	           	$pagination = new Application_Model_ZendPagination();
	            $pagination->setItemCount(10);
	            $list = $classifieds->fetchAll('offline = 0', 'title DESC');
	            if(!empty($list)){
	             	$this->view->classifieds = $pagination->paginate($list, $this->_getParam('page'));	
				}else{
	           		$this->view->classifieds = $list;	
				}
				$this->view->user_id = $session->getUserId();
	            $this->view->category_name = $category->getName($catid);
	       	}
     	}
    }

    public function profileAction()
    {
     	//get classid
        $classid = $this->_getParam('classid');
        $this->view->msg = $this->_getParam('msg');
        if(!empty($classid)){ 
        	
          	$classified = new campuswisdom_Model_DbTable_Classifieds();
			$category = new campuswisdom_Model_DbTable_Categories();
			$person = new Application_Model_Mapper_Person();
            $session = new Application_Model_Session('credentials');
            $session->startSession();
            $owner = $person->fetch($session->getUserId());
            $this->view->onAuction = $classified->onAuction($classid, $session->getUserId());
            $this->view->classified = $classified->getClassified($classid);
			$this->view->cat = $category;
            $this->view->category = $category->getName($this->view->classified['category_id']);
			$this->view->user_id = $session->getUserId();
			$name = $owner->getName();
			if(!empty($name)){
				$this->view->owner = $owner->getName()->getUnstructured();	
			}else{
				$this->view->owner = $owner->getUsername();
			}
            $pagin = new Application_Model_ZendPagination(4);
            $images = $classified->getImages($classid);
			if(!empty($images)){
           		$this->view->class_images = $pagin->paginate($images, $this->_getParam('page'));
           	}else{
            	$this->view->class_images = $images;
			}
			$requests = $classified->getBids($classid);
			if(!empty($requests)){
           		$this->view->requests = $pagin->paginate($requests, $this->_getParam('page'));
           	}else{
            	$this->view->requests = $requests;
			}
			
      	}else{
        	$this->_helper->redirector('index', 'classifieds');
      	}
    }

    public function placebidAction()
    {
    	$classid = $this->_getParam('classid');
    	$session = new Application_Model_Session();
    	$session->startSession();
    	$form = new campuswisdom_Form_Bid($classid, $session->getUserId());
        $this->view->form = $form;
      	if($this->getRequest()->isPost()){
        	$request = $this->getRequest();
            if($form->isValid($request->getPost())){
            	$data = $form->getValues();
                $classified = new campuswisdom_Model_DbTable_Classifieds();
                $number = $classified->getAdapter()->insert('bids', $data);
                if(!empty($number)){
                	$this->_helper->redirector('profile', 'classifieds', 'campuswisdom', array('msg'=>'Bid placed', 'classid'=>$classid));
                }
          	}
       	}
    }

    public function addclassifiedAction()
    {
    	$form = new campuswisdom_Form_Classified();
        $this->view->form = $form;
      	if($this->getRequest()->isPost()){
        	$request = $this->getRequest();
            if($form->isValid($request->getPost())){
            	$data = $form->getValues();
                $classified = new campuswisdom_Model_DbTable_Classifieds();
                $classid = $classified->insert($data);
                if(!empty($classid)){
                	$this->_helper->redirector('index', 'mybizads', 'campuswisdom', array('msg'=>$classified->getName($classid).' added as #'.$classid));
                }
          	}
       	}
    }


}







