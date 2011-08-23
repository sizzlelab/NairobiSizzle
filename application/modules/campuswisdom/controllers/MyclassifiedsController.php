<?php

class Campuswisdom_MyclassifiedsController extends Zend_Controller_Action
{

    public function init()
    {}

    public function indexAction()
    {
        // action body
    }

    public function editprofileAction()
    {
    	$classid = $this->_getParam('classid');
		$form = new campuswisdom_Form_Classified();
		$classified = new campuswisdom_Model_DbTable_Classifieds();
      	$data = $classified->getClassified($classid);
      	if(!empty($data)){
        	$form->populate($data);
           	$this->view->form = $form;
            if($this->getRequest()->isPost()){
            	$request = $this->getRequest();
                if($form->isValid($request->getPost())){
                	$data = $form->getValues();
                	$classified = new campuswisdom_Model_DbTable_Classifieds();
					$number = $classified->update($data, 'classified_id = '.$classid);
					if(!empty($number)){
                		$this->_helper->redirector('profile', 'classifieds', 'campuswisdom', array('classid'=>$classid ,'msg'=>$classified->getName($classid).' edited'));
               		}
				}
           	}
  		}else{
        	$this->_helper->redirector('index', 'classifieds', 'campuswisdom');
       	}
    }

    public function offlineadsAction()
    {
        // action body
    }

    public function takeonlineAction()
    {
    	$classid = $this->_getParam('classid');
		$classified = new campuswisdom_Model_DbTable_Classifieds();
		if($classified->putOnline($classid)){
			$this->_helper->redirector('offlineads', 'mybizads', 'campuswisdom', array('msg'=>$classified->getName($classid).' is online'));
       	}else{
       		$this->_helper->redirector('offlineads', 'mybizads', 'campuswisdom', array('error'=>'Failure'));
        }
    }

    public function takeofflineAction()
    {
    	$classid = $this->_getParam('classid');
		$classified = new campuswisdom_Model_DbTable_Classifieds();
		if($classified->takeOffline($classid)){
			$this->_helper->redirector('index', 'mybizads', 'campuswisdom', array('msg'=>$classified->getName($catid). ' is offline'));
       	}else{
       		$this->_helper->redirector('index', 'mybizads', 'campuswisdom', array('error'=>'Failure'));
       	}
    }

    public function addlogoAction()
    {
    	$classid = $this->_getParam('classid');
        $form = new campuswisdom_Form_Classifiedimages($classid);
        $form->image->setDestination(dirname(APPLICATION_PATH)."/public/uploads/classifieds");
		$this->view->form = $form;
        $this->view->classid = $classid;
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
            	//$form->image->setDestination(APPLICATION_PATH."/../public/uploads/classifieds");
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
            	$path = $path.'classifieds/'.$new_image_name;
            	$image_saved = rename($source, $path);
            	if($image_saved){   
            		//save to database
                	$classified = new campuswisdom_Model_DbTable_Classifieds();
                	$id = $classified->uploadImage($new_image_name, $classid, $values['date_added'], true);
					if($id){
                		$this->_helper->redirector('profile','classifieds','campuswisdom',array('classid'=>$classid, 'msg'=>'image uploaded'));
                	}
                }
       		}
		}
    }

    public function addimageAction()
    {
    	$classid = $this->_getParam('classid');
        $form = new campuswisdom_Form_Classifiedimages($classid);
        $form->image->setDestination(dirname(APPLICATION_PATH)."/public/uploads/classifieds");
		$this->view->form = $form;
        $this->view->classid = $classid;
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
            	$path = $path.'classifieds/'.$new_image_name;
            	$image_saved = rename($source, $path);
            	if($image_saved){   
            		//save to database
                	$classified = new campuswisdom_Model_DbTable_Classifieds();
                	$id = $classified->uploadImage($new_image_name, $classid, $values['date_added']);
					if($id){
                		$this->_helper->redirector('profile','classifieds','campuswisdom',array('classid'=>$classid, 'msg'=>'image uploaded'));
                	}
                }
       		}
		}
    }


}













