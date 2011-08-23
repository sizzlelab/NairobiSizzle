<?php
/**
 * This plugin ensures that whoever is trying to access the admin module is a Nairobi Sizzle admin.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Admin
 * @subpackage Plugins
 */
class Admin_Plugin_AdminCheck extends Zend_Controller_Plugin_Abstract {
    /**
     * Stores the user attempting to access the admin module.
     * 
     * @var Application_Model_Person|null
     */
    protected $user = null;

    /**
     * Get the user attempting to access the admin module.
     *
     * @return Application_Model_Person
     */
    public function getUser() {
        if (!$this->user) {
            /*fetch the user's profile*/
            $mapper     = new Application_Model_Mapper_Person();
            $this->user = $mapper->fetch(Zend_Controller_Front::getInstance()->getPlugin('Application_Plugin_Util')->getSession()->getUserId());
        }
        return $this->user;
    }

    /**
     * Checks whether the user attempting to access the admin module is actually
     * an admin.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * 
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        /*only perform check for admin module*/
        if ('admin' !== $request->getModuleName()) {
            return;
        }
        /*if trying to setup, let it happen, this should be removed as soon as the first super admin has been setup*/
        if ($request->getControllerName() === 'set-up' ) {
            return;
        }
        $user       = $this->getUser();
        /*get configs*/
        $configs    = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        /*admin check*/
        $role       = $user->getRole();
        if (!$role) {
            $request->setControllerName('unauthorized')
                    ->setActionName('index')
                    ->setDispatched();
        }
        if ($role !== $configs['clients']['asi']['admins']['super']) {
            /*re-route to unauthorized action*/
            $request->setControllerName('unauthorized')
                    ->setActionName('index')
                    ->setDispatched();
        }
    }
}