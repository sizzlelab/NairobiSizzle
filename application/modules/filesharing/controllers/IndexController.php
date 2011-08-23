<?php

class Filesharing_IndexController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
        // action body
    }

    public function registerAction()
 {
        $str = new Application_Model_Session();
        $str->startSession();
        $this->view->shedef = "Uploads";
        $form = new Filesharing_Form_Register();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $name = $form->getValue('name');

                $userid = 'Userid';

                $upload = new Zend_File_Transfer_Adapter_Http();
                //getFileAdapter
                ///../uploads/
                $upload->setDestination(APPLICATION_PATH . '/../public/uploads');
                try {
                    // upload received file(s)
                    $upload->receive();
                } catch (Zend_File_Transfer_Exception $e) {
                    $e->getMessage();
                }
                try {
                    $uploadedData = $form->getValues();
                    //Zend_Debug::dump($uploadedData, 'Form Data:');
                    // you MUST use following functions for knowing about uploaded file
                    # Returns the file name for 'doc_path' named file element
                    $name2 = $upload->getFileName('doc_path');
                    //chmod($name2, 0777);
                    // Rename uploaded file using Zend Framework
                    # Returns the mimetype for the 'doc_path' form element
                    $mimeType = $upload->getMimeType($name2);
                    $name = $form->getValue('name');
                    $description = $form->getValue('description');
                    $currentDate = new Zend_Date();
                    $date = $currentDate->toString('y-MM-dd');
                    $user_id = $this->session->getUserId();
                    $dbinsert = new Filesharing_Model_Saving();
                    $dbinsert->insertShedef($name, $mimeType, $description, $date, $userid);
                } catch (Exception $e) {
                    $e->getMessage();
                }
            }
        }
    }

    public function getRegisteredAction()
    {
        // action body
        
        
        	$db = new Fileshring_Model_Saving();
        	$all =$db->getSavedIdiots();
        	$this->view->all = $all;
    }

    public function getfileAction()
    {
       	$form= new Filesharing_Form_Download();
        $db = new Filesharing_Model_Saving();
       	$all =$db->select();
       	$this->view->all = $all;
    }
}







