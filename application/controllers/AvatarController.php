<?php

class AvatarController extends Zend_Controller_Action {

    public function init() {
        $this->session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        /*disable views*/
        $this->_helper->ViewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    public function indexAction() {
        $id = $this->getRequest()->getParam('id');
        $id = $id ? $id : $this->session->getUserId();
        if ($id) {
            $mapper = new Application_Model_Mapper_Person_Avatar();
            $avatar = $mapper->fetch($id);
            $this->getResponse()->setHeader('Content-type', 'image/jpeg')
                    ->setBody($avatar->getFileData());
        } else {
            return false;
        }
    }

    public function largeThumbnailAction() {
        $id = $this->getRequest()->getParam('id');
        $id = $id ? $id : $this->session->getUserId();
        if ($id) {
            $mapper = new Application_Model_Mapper_Person_Avatar_Thumbnail_Large();
            $avatar = $mapper->fetch($id);
            $this->getResponse()->setHeader('Content-type', 'image/jpeg')
                    ->setBody($avatar->getFileData());
        } else {
            return false;
        }
    }

    public function smallThumbnailAction() {
        $id = $this->getRequest()->getParam('id');
        $id = $id ? $id : $this->session->getUserId();
        if ($id) {
            $mapper = new Application_Model_Mapper_Person_Avatar_Thumbnail_Small();
            $avatar = $mapper->fetch($id);
            $this->getResponse()->setHeader('Content-type', 'image/jpeg')
                    ->setBody($avatar->getFileData());
        } else {
            return false;
        }
    }
}