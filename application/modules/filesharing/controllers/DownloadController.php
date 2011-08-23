<?php

class Filesharing_DownloadController extends Zend_Controller_Action
{

    protected $_name = 'upload';

    public function init()
    {}

    public function indexAction()
    {
//        $paginator = new Application_Model_ZendPagination();
//        $paginator->setItemCount(5);
//        $this->view->events = $paginator->paginate($group_events, $this->_getParam('page'));
    }

    public function categoriesAction()
    {
        $db = new Filesharing_Model_Saving();
        $all = $db->selectDocuments();
        $this->view->all = $all;
        //$paginator = new Application_Model_ZendPagination();
//        $paginator->setItemCount(5);
//        $this->view->events = $paginator->paginate($group_events, $this->_getParam('page'));
    }

    public function listMoviesAction()
    {
        $db = new Filesharing_Model_Saving();
                $all = $db->selectMovie();
                $this->view->all = $all;
    }

    public function listMusicAction()
    {
        $db = new Filesharing_Model_Saving();
                $all = $db->selectMusic();
                $this->view->all = $all;
    }

    public function listPicturesAction()
    {
        $db = new Filesharing_Model_Saving();
                $all = $db->selectPictures();
                $this->view->all = $all;
    }

    public function downloadAction()
    {


        $form = new Filesharing_Form_Download();
        $this->view->form = $form;

        try {
            $mimetype = $form->getValues('name');
            header('Content-Type: image/jpeg');
            //header('Content-Disposition: attatchment; filename="DSC00876.JPG"');
            header('content-Disposition: attatchment; filename=' . $name);
            //readfile('/../uploads/DSC00876.JPG');
            readfile(APPLICATION_PATH.'/../public/uploads' . $name);

            // disable layout and view
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

}



