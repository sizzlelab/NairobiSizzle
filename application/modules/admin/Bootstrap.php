<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {
    public function _initAdmin() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Admin_Plugin_AdminCheck());
    }
}