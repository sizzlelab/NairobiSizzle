<?php

class Campuswisdom_MybizadsController extends Zend_Controller_Action
{

    public function init()
    {}

    public function indexAction()
    {
		$this->view->msg = $this->_getParam('msg');
        $this->view->error = $this->_getParam('error');
        $session = new Application_Model_Session();
        $session->startSession();
        $my_businesses = new campuswisdom_Model_DbTable_Businesses();
        $pagination = new Application_Model_ZendPagination();
        $list = $my_businesses->getMyBusinesses($session->getUserId());
        if(!empty($list)){
        	$this->view->businesses = $pagination->paginate($list, $this->_getParam('page'));	
      	}else{
        	$this->view->businesses = $list;
       	}
                        
        $my_classifieds = new campuswisdom_Model_DbTable_Classifieds();
        $pagin = new Application_Model_ZendPagination();
        $list = $my_classifieds->getMyClassifieds($session->getUserId());
        if(!empty($list)){
			$this->view->classifieds = $pagination->paginate($list, $this->_getParam('page'));
      	}else{
        	$this->view->classifieds = $list;
      	}
    }

    public function addbusinessAction()
    {
       	$form = new campuswisdom_Form_Business();
        $this->view->form = $form;
        if($this->getRequest()->isPost()){
        	$request = $this->getRequest();
            if($form->isValid($request->getPost())){
            	$data = $form->getValues();
                $business = new campuswisdom_Model_DbTable_Businesses();
				if($business->validateName($data['name'])){
                	$bizid = $business->insert($data);
               	}else{
               		$this->view->error = 'The name: '.$data['name'].' already exists';
                	$this->view->exists_id = $business->getId($data['name']);
                    $form->populate($data);
               	}
                if(!empty($bizid)){
                	$this->_helper->redirector('index', 'mybizads', 'campuswisdom', array('msg'=>$business->getName($bizid).' added as #'.$bizid));
               	}
       		}
     	}
    }

    public function addbizproductAction()
    {
        $bizid = $this->_getParam('bizid');
    	if(!empty($bizid)){
        	$business = new campuswisdom_Model_DbTable_Businesses();
            $this->view->business_name = $business->getName($bizid);
            $form = new campuswisdom_Form_Product($bizid);
            $this->view->form = $form;
            if($this->getRequest()->isPost()){
            	$request = $this->getRequest();
                if($form->isValid($request->getPost())){
                	$data = $form->getValues();
                	$products = new campuswisdom_Model_DbTable_Products();
                	$pdtid = $products->insert($data);
                	if(!empty($pdtid)){
                		$this->_helper->redirector('profile', 'businesses', 'campuswisdom', array('bizid'=>$bizid,'msg'=>$products->getName($pdtid).' added as product #'.$bizid));
                	}
              	}
          	}
      	}else{
        	$this->_helper->redirector('index','mybizads');
       	}
    }

    public function editprofileAction()
    {
        $bizid = $this->_getParam('bizid');
        $form = new campuswisdom_Form_Business();
        $business = new campuswisdom_Model_DbTable_Businesses();
        $data = $business->getBusiness($bizid);
        if(!empty($data)){
        	$form->populate($data);
            $this->view->form = $form;
            if($this->getRequest()->isPost()){
            	$request = $this->getRequest();
                if($form->isValid($request->getPost())){
                	$data = $form->getValues();
                	$business = new campuswisdom_Model_DbTable_Businesses();
                	$number = $business->update($data, 'id = '.$bizid);
                	if(!empty($number)){
                		$this->_helper->redirector('index', 'mybizads', 'campuswisdom', array('msg'=>$business->getName($bizid).' edited'));
                	}
             	}
          	}
      	}else{
        	$this->_helper->redirector('categorized', 'businesses', 'campuswisdom');
       	}
    }

    public function editproductAction()
    {
        $pdtid = $this->_getParam('pdtid');
		if(!empty($pdtid)){
        	$product = new campuswisdom_Model_DbTable_Products();
            $this->view->product_name = $product->getName($pdtid);
            $product_data = $product->getProduct($pdtid);
            if(!empty($product_data)){
            	$form = new campuswisdom_Form_Product();
                $form->populate($product_data);
                $this->view->form = $form;
                if($this->getRequest()->isPost()){
                	$request = $this->getRequest();
                	if($form->isValid($request->getPost())){
                		$data = $form->getValues();
                		$rows = $product->update($data, 'id = '.$pdtid);
                		if(!empty($rows)){
                			$this->_helper->redirector('profile', 'businesses', 'campuswisdom', array('bizid'=>$data['business_id'],'msg'=>$product->getName($pdtid).' edited'));
                		}
               		}
               	}
          	}else{
            	$this->_helper->redirector('index','mybizads');
            }
       	}else{
        	$this->_helper->redirector('index','mybizads');
       	}
    }

    public function takeofflineAction()
    {
        $bizid = $this->_getParam('bizid');
        $business = new campuswisdom_Model_DbTable_Businesses();
        if($business->takeOffline($bizid)){
        	$this->_helper->redirector('index', 'mybizads', 'campuswisdom', array('msg'=>$business->getName($bizid). ' is offline'));
      	}else{
        	$this->_helper->redirector('index', 'mybizads', 'campuswisdom', array('error'=>'Failure'));
      	}
    }

    public function offlineadsAction()
    {
        /**
		* get list of businesses added by currently logged in user
        */
        $this->view->msg = $this->_getParam('msg');
        $this->view->error = $this->_getParam('error');
        $session = new Application_Model_Session();
        $session->startSession();
        $my_businesses = new campuswisdom_Model_DbTable_Businesses();
       	$pagination = new Application_Model_ZendPagination(10);
        $list = $my_businesses->getMyOfflineBusinesses($session->getUserId());
        if(!empty($list)){
        	$this->view->businesses = $pagination->paginate($list, $this->_getParam('page'));	
       	}else{
        	$this->view->businesses = $list;
       	}
        /**
        * get classifieds added by the logged in user
        */
    	$my_classifieds = new campuswisdom_Model_DbTable_Classifieds();
       	$page = new Application_Model_ZendPagination(10);
        $list = $my_classifieds->getMyOfflineClassifieds($session->getUserId());
        if(!empty($list)){
        	$this->view->classifieds = $page->paginate($list, $this->_getParam('page'));	
       	}else{
        	$this->view->classifieds = $list;
       	}
    }

    public function takeonlineAction()
    {
        $bizid = $this->_getParam('bizid');
		$business = new campuswisdom_Model_DbTable_Businesses();
        if($business->putOnline($bizid)){
        	$this->_helper->redirector('offlineads', 'mybizads', 'campuswisdom', array('msg'=>$business->getName($bizid).' is online'));
      	}else{
        	$this->_helper->redirector('offlineads', 'mybizads', 'campuswisdom', array('error'=>'Failure'));
      	}
    }

    public function addvideoAction()
    {
        // action body
    }

    public function addimageAction()
    {
        $bizid = $this->_getParam('bizid');
        $form = new campuswisdom_Form_BusinessImages($bizid);
        $form->image->setDestination(dirname(APPLICATION_PATH)."/public/uploads/business");
		$this->view->form = $form;
        $this->view->business_id = $bizid;
        if($this->getRequest()->isPost()){
        	if(!$form->isValid($this->getRequest()->getParams()))
            {
            	return $this->render('addimage');
           	}
           	if(!$form->image->receive())
            {
            	$this->view->message = '<div class="errors">Errors Receiving File.</div>';
                return $this->render('addimage');
          	}

          	if($form->image->isUploaded())
            {
            	$values = $form->getValues();
            	$file = $form->image->getFileName(null, false);
            	$file = explode('.',$file);
            	$date = new Zend_Date();
            	$new_image_name = $file[0].'_'.$date->toString('YmdHis').'.'.$file[1];
            	
            	//manual rename
            	$source = $form->image->getFileName();
            	chmod($source, 0777);
            	$url = $source;
            	$url = explode('.',$url);
            	$url = explode('/',$url[0]);
            	$size = count($url);
            	$path = '';
            	for ($i=0; $i < $size-2; $i++){
            		$path .= $url[$i].'/';
            	}
            	$path = $path.'business/'.$new_image_name;
            	$image_saved = rename($source, $path);
            	if($image_saved){   
            		//save to database
                	$business = new campuswisdom_Model_DbTable_Businesses();
                	$id = $business->uploadImage($new_image_name, $bizid, $values['date_added']);
                  	if($id){
                		$this->_helper->redirector('profile','businesses','campuswisdom',array('bizid'=>$bizid, 'msg'=>'image uploaded'));
                	}
                }
       		}
		}
    }

    public function changelogoAction()
    {
    	$bizid = $this->_getParam('bizid');
        $form = new campuswisdom_Form_BusinessImages($bizid);
        $form->image->setDestination(dirname(APPLICATION_PATH)."/public/uploads/business");
		$this->view->form = $form;
        $this->view->business_id = $bizid;
        if($this->getRequest()->isPost()){
        	if(!$form->isValid($this->getRequest()->getParams()))
            {
            	return $this->render('addimage');
           	}
           	if(!$form->image->receive())
            {
            	$this->view->message = '<div class="errors">Errors Receiving File.</div>';
                return $this->render('addimage');
          	}

          	if($form->image->isUploaded())
            {
            	$values = $form->getValues();
            	$file = $form->image->getFileName(null, false);
            	$file = explode('.',$file);
            	$date = new Zend_Date();
            	$new_image_name = $file[0].'_'.$date->toString('YmdHis').'.'.$file[1];
            	
            	//manual rename
            	$source = $form->image->getFileName();
            	chmod($source, 0777);
            	$url = $source;
            	$url = explode('.',$url);
            	$url = explode('/',$url[0]);
            	$size = count($url);
            	$path = '';
            	for ($i=0; $i < $size-2; $i++){
            		$path .= $url[$i].'/';
            	}
            	$path = $path.'business/'.$new_image_name;
            	$image_saved = rename($source, $path);
            	if($image_saved){   
            		//save to database
                	$business = new campuswisdom_Model_DbTable_Businesses();
                	$id = $business->uploadImage($new_image_name, $bizid, $values['date_added'],true);
                  	if($id){
                		$this->_helper->redirector('profile','businesses','campuswisdom',array('bizid'=>$bizid, 'msg'=>'image uploaded'));
                	}
                }
       		}
		}
    }

    public function productimageAction()
    {
    	
    	$pdtid = $this->_getParam('pdtid');
    	$bizid = $this->_getParam('bizid');
        $form = new campuswisdom_Form_ProductImages($pdtid);
        $form->image->setDestination(dirname(APPLICATION_PATH)."/public/uploads/products");
		$this->view->form = $form;
        $this->view->pdt_id = $pdtid;
        if($this->getRequest()->isPost()){
        	if(!$form->isValid($this->getRequest()->getParams()))
            {
            	return $this->render('addimage');
           	}
           	if(!$form->image->receive())
            {
            	$this->view->message = '<div class="errors">Errors Receiving File.</div>';
                return $this->render('addimage');
          	}

          	if($form->image->isUploaded())
            {
            	$values = $form->getValues();
            	$file = $form->image->getFileName(null, false);
            	$file = explode('.',$file);
            	$date = new Zend_Date();
            	$new_image_name = $file[0].'_'.$date->toString('YmdHis').'.'.$file[1];
            	
            	//manual rename
            	$source = $form->image->getFileName();
            	chmod($source, 0777);
            	$url = $source;
            	$url = explode('.',$url);
            	$url = explode('/',$url[0]);
            	$size = count($url);
            	$path = '';
            	for ($i=0; $i < $size-2; $i++){
            		$path .= $url[$i].'/';
            	}
            	$path = $path.'products/'.$new_image_name;
            	$image_saved = rename($source, $path);
            	if($image_saved){   
            		//save to database
            		$product = new campuswisdom_Model_DbTable_Products();
					$id = $product->uploadImage($new_image_name, $pdtid, $values['date_added'], true);
                	if($id){
                		$this->_helper->redirector('profile','businesses','campuswisdom',array('bizid'=>$bizid, 'msg'=>'image uploaded'));
					}
                }
       		}
		}
    }


}



