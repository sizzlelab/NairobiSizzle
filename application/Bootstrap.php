<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Registers the {@link Application_Plugin_Util} plugin with the
     * application.
     */
    public function _initPlugins() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_Util());
    }
}