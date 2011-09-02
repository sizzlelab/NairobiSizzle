<?php
/**
 * Plugin that establishes a user session and logs the type of device used to access
 * the application. This plugin also provides an interface to performing post-dispatch
 * functions.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Plugins
 */
class Application_Plugin_Util extends Zend_Controller_Plugin_Abstract {
    /**
     * Contains a list of all controllers in the 'default' module that can be
     * accessed without requiring a user session.
     *
     * Note that the {@link SignUpController} is not included in this link as it's
     * bypassed directly in the {@link Application_Plugin_Util::preDispatch()}
     * method.
     *
     * @var array
     */
    protected $openResources = array(
            'index',
            'login',
            'error',
            'about-us',
            'help',
            'terms',
            'privacy-policy',
            'credits',
	    'feedback',
            'avatar',
            'cron',
            'search'
    );

    /**
     * Stores callbacks to be executed after dipatch is complete.
     * 
     * @var array
     */
    protected $postDispatchCallbacks = array();

    /**
     * Stores a session instance.
     *
     * @var Application_Model_Session.
     */
    protected $session = null;

    /**
     * Get the global session instance.
     *
     * @return Application_Model_Session
     */
    public function getSession() {
        if (!$this->session) {
            $this->session = new Application_Model_Session();
        }
        return $this->session;
    }

    /**
     * Establishes if there's a user session for all controllers besides those in
     * {@link Application_Plugin_SessionAndLogin::openResources}.
     *
     * @return void
     */
    public function preDispatch() {
        $request    = $this->getRequest();
        $session    = $this->getSession();
        $controller = $request->getControllerName();
        $module     = $request->getModuleName();
        if ($module == 'default') {
            //if sign-up, do an app login
            if ($controller == 'sign-up') {
                //end session
                $session->endSession();
                //app login
                $session->noUserSession();
            // check for users whose usernames were changed from the database merge
            // links are as such: nairobisizzle.com/login/index/rand/
            } elseif ($controller == 'login' && $request->getActionName() == 'index' && $request->getParam("rand")) {
                // end any current session, just to be sure
                $session->endSession();
                // do an app login
                $session->noUserSession();
                // notify login page
                $userIdNamespace = new Zend_Session_Namespace('iserIdNamespace');
                $userIdNamespace->change_id = $request->getParam("rand");
                // redirect to login page, no need we're
                // $request->setModuleName('default')
                //        ->setControllerName('login')
                //        ->setActionName('index')
                //        ->setDispatched(false);
            //every other controller, start a user session
            } else {
                $credentials = new Zend_Session_Namespace('credentials');
                $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
                //attempt to start a session
                if (!$session->startSession() && empty($credentials->user_id)) {
                    //if controller is not an open resource, do a redirect
                    if (!in_array($controller, $this->openResources)) {
                        //set redirect to
                        $session->setSessionParameter('redirect_to', 1);
                        $session->setSessionParameter('get_params', $request->getUserParams());
                        $session->setSessionParameter('params', $request->getParams());
                        //notify view
                        $view->loginRequired = true;
                        //add a dispatch cycle that redirects to the login page
                        $request->setModuleName('default')
                                ->setControllerName('login')
                                ->setActionName('index')
                                ->setDispatched(false);
                    }
                //else, notify view that user is logged
                } else {
                    $view->isUserLogged = true;
                }
            }
            //for every other module, start user session
        } else {
            $credentials = new Zend_Session_Namespace('credentials');
            $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
            if(!$session->startSession() && empty($credentials->user_id)) {
                //set redirect to
                $session->setSessionParameter('redirect_to', 1);
                $session->setSessionParameter('get_params', $request->getUserParams());
                $session->setSessionParameter('params', $request->getParams());
                //notify view
                $view->loginRequired = true;
                //add a dispatch cycle to the login page
                $request->setModuleName('default')
                        ->setControllerName('login')
                        ->setActionName('index')
                        ->setDispatched(false);
            } else {
                /*if session exists, notify view*/
                $view->isUserLogged = true;
            }
        }
    }

    /**
     * Adds a function to execute after the dispatch sequence is complete.
     * 
     * @param array|string $callback
     * @param mixed $args
     * 
     * @return Application_Plugin_Util
     */
    public function addPostDispatchCallback($callback, $args = null) {
        $this->postDispatchCallbacks[] = array(
            'callback' => $callback,
            'args'     => $args
        );
        return $this;
    }

    /**
     * Gets the post-dispatch callback functions.
     * 
     * @return array
     */
    public function getPostDispatchCallbacks() {
        return $this->postDispatchCallbacks;
    }

    /**
     * Executes any post dispatch action.
     */
    public function postDispatch() {
        foreach ($this->postDispatchCallbacks as $callback) {
            if ($callback['args']) {
                if (is_array($callback['args'])) {
                    call_user_func_array($callback['callback'], $callback['args']);
                } else {
                    call_user_func($callback['callback'], $callback['args']);
                }
            } else {
                call_user_func($callback['callback']);
            }
        }
    }

    /**
     * Logs the type of device used to access the site. This is done after the dispatch
     * sequence is completed.
     *
     * @return void
     */
    public function dispatchLoopShutdown() {
    }
}
