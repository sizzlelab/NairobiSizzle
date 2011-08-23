<?php

class Campuswisdom_BusinessesController extends Zend_Controller_Action
{

    public function init()
    {}

    public function indexAction()
    {
        // action body
    }

    public function categorizedAction()
    {
        //form to select category
        $catform = new campuswisdom_Form_Category();
        $this->view->form = $catform;
        $catid = $this->_getParam('catid');
        //check if category has been selected from dropdown
        if($this->getRequest()->isPost()){
        //get category id or name
        	$request = $this->getRequest();
            //validate form
            if($catform->isValid($request->getPost())){
            	$value = $catform->getValues();
                $catid = $value['Category'];
                //query db for businesses under cat id
                $businesses = new campuswisdom_Model_DbTable_Businesses();
                $category = new campuswisdom_Model_DbTable_Categories();
                if($value['Category'] == 0){
                	$list = $businesses->fetchAll('offline = 0', 'name');
               	}else{
                	$list = $businesses->getCategorized($catid);
				}
                $this->view->category_name = $category->getName($catid);
                $pagination = new Application_Model_ZendPagination(10);
                if(!empty($list)){
                	$this->view->businesses = $pagination->paginate($list,$this->_getParam('page'));	
               	}else{
                	$this->view->businesses = $list;
              	}
       		}
       	}else{
            //use catid passed as get param
            if(!empty($catid)){
            	//query db for businesses under cat id
                $businesses = new campuswisdom_Model_DbTable_Businesses();
                $category = new campuswisdom_Model_DbTable_Categories();
                $pagination = new Application_Model_ZendPagination();
                $pagination->setItemCount(10);
                $list = $businesses->getCategorized($catid);
                if(!empty($list)){
                	$this->view->businesses = $pagination->paginate($list, $this->_getParam('page'));	
               	}else{
                	$this->view->businesses = $list;	
               	}
              	$this->view->category_name = $category->getName($catid); 
           	}
                                                }
    }

    public function profileAction()
    {
        //get bizid
    	$bizid = $this->_getParam('bizid');
        $this->view->msg = $this->_getParam('msg');
        if(!empty($bizid)){ 
        	$business = new campuswisdom_Model_DbTable_Businesses();
            $category = new campuswisdom_Model_DbTable_Categories();
            $person = new Application_Model_Mapper_Person();
            $session = new Application_Model_Session('credentials');
            $session->startSession();
            $owner = $person->fetch($session->getUserId());
            $this->view->business = $business->getBusiness($bizid);
            $this->view->cat = $category;
            $this->view->category = $category->getName($this->view->business['category_id']);
            $this->view->user_id = $session->getUserId();
            $name = $owner->getName();
			if(!empty($name)){
				$this->view->owner = $owner->getName()->getUnstructured();	
			}else{
				$this->view->owner = $owner->getUsername();
			} 
            $pagin = new Application_Model_ZendPagination(4);
            $images = $business->getImages($bizid);
            if(!empty($images)){
            	$this->view->biz_images = $pagin->paginate($images, $this->_getParam('page'));
           	}else{
            	$this->view->biz_images = $images;
           	}
            //$this->view->biz_videos = $business->getVideos($bizid);
            $products = new campuswisdom_Model_DbTable_Products();
            $pagination = new Application_Model_ZendPagination();
            $list = $products->getBizProducts($bizid);
            if(!empty($list)){
            	$this->view->products = $pagination->paginate($list,$this->_getParam('page'));	
           	}else{
            	$this->view->products = $list;
          	}
                                                   	
      	}else{
        	$this->_helper->redirector('allbusinesses', 'index');
       	}
    }

    public function viewproductsAction()
    {
        //form to select category
        $catform = new campuswisdom_Form_Category();
        $this->view->form = $catform;
        $catid = $this->_getParam('catid');
        if(!empty($catid)){
        	$this->view->catid = $catid;	
        }else{
        	$this->view->catid = 0;
       	}               
        //check if category has been selected from dropdown
        if($this->getRequest()->isPost()){
	        //get category id or name
	        $request = $this->getRequest();
	        //validate form
	        if($catform->isValid($request->getPost())){
	        	$value = $catform->getValues();
	            $catid = $value['Category'];
	            $this->view->catid = $catid;
	            //query db for businesses under cat id
	            $products = new campuswisdom_Model_DbTable_Products();
	            $category = new campuswisdom_Model_DbTable_Categories();
	            /**
	            * all products selected
	            */
	            if($value['Category'] == 0){
	            	$list = $products->fetchAll('offline = 0', 'name');
	           	}else{
	            	/**
	            	* a particular category selected
	            	*/
	            	$list = $products->getCategorized($catid);
	            } 
	            $this->view->business = $business = new campuswisdom_Model_DbTable_Businesses();
	            $this->view->category_name = $category->getName($catid); 
	            $pagination = new Application_Model_ZendPagination(10);
	            if(!empty($list)){
	            	$this->view->products = $pagination->paginate($list,$this->_getParam('page'));	
	          	}else{
	            	$this->view->products = $list;
	           	}
	                                        	        	
	     	}
         }else{
        	//use catid passed as get param
            if($catid>=0){
            	//query db for businesses under cat id
                $products = new campuswisdom_Model_DbTable_Products();
                $category = new campuswisdom_Model_DbTable_Categories();
                $this->view->business = $business = new campuswisdom_Model_DbTable_Businesses();
                $pagination = new Application_Model_ZendPagination(10);
                if($catid == 0){
                	$list = $products->fetchAll('offline = 0', 'name');
              	}else{
                	$list = $products->getCategorized($catid);	
              	}
                if(!empty($list)){
                	$this->view->products = $pagination->paginate($list, $this->_getParam('page'));	
               	}else{
                	$this->view->products = $list;
               	}
                $this->view->category_name = $category->getName($catid);
        	}
       	}
    }

    public function productlistAction()
    {
        //form to select category
        $catform = new campuswisdom_Form_Category();
        $this->view->form = $catform;
        $catid = $this->_getParam('catid');
		if(!empty($catid)){
        	$this->view->catid = $catid;	
       	}else{
        	$this->view->catid = 0;
      	} 
                                                
        //check if category has been selected from dropdown
       	if($this->getRequest()->isPost()){
        	//get category id or name
        	$request = $this->getRequest();
            //validate form
            if($catform->isValid($request->getPost())){
            	$value = $catform->getValues();
                $catid = $value['Category'];
                //query db for businesses under cat id
                $products = new campuswisdom_Model_DbTable_Products();
                $category = new campuswisdom_Model_DbTable_Categories();
                /**
                * all products selected
                */
                if($value['Category'] == 0){
                	$list = $products->fetchAll('offline = 0', 'name');
               	}else{
                	/**
                    * a particular category selected
                    */
                    $list = $products->getCategorized($catid);
              	} 
                $this->view->business = $business = new campuswisdom_Model_DbTable_Businesses();
                $this->view->category_name = $category->getName($catid); 
                $pagination = new Application_Model_ZendPagination(10);
                if(!empty($list)){
                	$this->view->products = $pagination->paginate($list,$this->_getParam('page'));	
               	}else{
                	$this->view->products = $list;
               	}
         	}
            }else{
            	//use catid passed as get param
                if($catid >= 0){
                	//query db for businesses under cat id
                    $products = new campuswisdom_Model_DbTable_Products();
                    $category = new campuswisdom_Model_DbTable_Categories();
                    $this->view->business = $business = new campuswisdom_Model_DbTable_Businesses();
                   	$pagination = new Application_Model_ZendPagination(10);
                    if($catid == 0){
                    	$list = $products->fetchAll('offline = 0', 'name');
                   	}else{
                    	$list = $products->getCategorized($catid);	
                   	}
                  	if(!empty($list)){
                    	$this->view->products = $pagination->paginate($list, $this->_getParam('page'));	
                   	}else{
                    	$this->view->products = $list;
                   	}
                   	$this->view->category_name = $category->getName($catid);
              	}
         	}
    }

}

















