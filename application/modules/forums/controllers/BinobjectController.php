<?php

class Forums_BinobjectController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $error=null;
        $str = new Application_Model_Session();
        $str->startSession('frankenstyn','franko');
        $form = new Zend_Form();
        $form->setAttrib('enctype', 'multipart/form-data')
                ->setAttrib('name', 'upload')
                ->addElement('file', 'upload', array(
                'label'    => 'Choose a file',
                'required' => true,
                'validators' => array(
                        array('Size', false, 10485760),
                        array('Count', false, 1),
                        array('Extension', false, 'png,gif,jpg,txt,zip')
                )
                ))
                ->addElement('submit', 'submit', array(
                'label'    => 'Upload',
                'ignore'   => true
        ));
        try {
            $form->upload->setDestination(APPLICATION_PATH."/../public/uploads");
        }catch(Zend_File_Transfer_Exception $e) {

            $error=$e->getMessage();
        }
        $this->view->form = $form;
        if($this->getRequest()->isPost()) {
            try {
                $form->upload->receive();

            }catch (Zend_File_Transfer_Exception $e) {
                $error=$e->getMessage();
            }
            $fileName = $form->upload->getFileName();
            $contenttype = mime_content_type($fileName);
            $name =basename($fileName);
          
            $uploader = new Application_Model_binObjects_Mapper();
            try{
            $stuff =$uploader->create($name,$fileName,$contenttype);
            $this->view->msg="File successfully uploaded";
            }catch(Exception $e)
            {
                $this->view->error=$e->getMessage();
            }
            
            
            //var_dump($stuff);
        }




    }
    public function getFileAction() {
        //display the image
        $img = $this->_getParam('imgid',0);
        if($img==0)
            {
            $img='a5jml8YHKr3589akdrL-mr';
            }

        $str = new Application_Model_Session();
        $str->startSession('frankenstyn','franko');
        $file = new Application_Model_binObjects_Mapper();
        $result = $file->download($img);
        if($result)
            {


        $this->getHelper ( 'viewRenderer' )->setNoRender ();
        $this->_helper->layout()->disableLayout();

        $this->getResponse()
        ->setHeader('Content-Disposition', 'attachment; filename='.$result['entry']['name'])
        ->setHeader('Content-type', $result['entry']['content_type']);
        readfile($result['entry']['orig_name']);
            }else
                {
                echo "Sorry,an error occurred";
                }





    }
}

