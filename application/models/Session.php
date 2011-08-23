<?php
/**
 * This class handles requests to the /session module of the ASI platform.
 *
 * @author Eric Mutunga
 * @copyright 2010, NairobiSizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 * @version 0.1.1
 */
class Application_Model_Session {
    const MAX_RETRIES = 10;
    const CAPTCHA_RETRIES = 3;

    protected $appName;
    protected $appPassword;

    protected $cookie = null;
    protected $user_id = null;
    protected $app_id = null;
    protected $username = null;
    protected $password = null;
    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $credentials = null;
    /**
     *
     * @var Application_Model_Client
     */
    protected $client = null;

    protected $number_of_tries = 0;

    /**
     * Initiates the credentials session namespace and gets an instance of the client
     */
    public function __construct() {
        /*configure appName and appPassword*/
        $configs           = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        $this->appName     = $configs['clients']['asi']['appName'];
        $this->appPassword = $configs['clients']['asi']['appPassword'];

        $this->credentials = new Zend_Session_Namespace('credentials');
        /**
         * set the expiration time of the session namespace to 24 hours
         * $this->credentials->setExpirationSeconds(86400);
         */
        $this->client = Application_Model_Client::getInstance();
    }

    /**
     * use this function to login to ASI
     * and/or to start session
     * stores the login details in Zend_Session
     * NB: do not use this function once you are logged in to pass requests to ASI. @see {startSession()}
     * @param string $username
     * @param string $password
     * @throws Application_Model_Exception
     */
    public function startSession($username = null, $password = null) {
        try {
            //validate username and password
            if(!empty($username) && !empty($password)) {
                //check if app id and userid are existent
                if(empty($this->credentials->app_id) && empty($this->credentials->user_id)) {
                    //perform ASI login
                    return $this->ASILogin($username, $password);
                }else {
                    if(empty($this->credentials->username) && empty($this->credentials->password)) {
                        //perform ASI login
                        return $this->ASILogin($username, $password);
                    }else {
                        //compare passed username and password with session
                        if($username == $this->credentials->username && $password == $this->credentials->password) {//returns 0 if same
                            //already logged in
                            //set cookie as header to client object
                            if(!empty($this->cookie)) {
                                $this->client->getClientObject()->setHeaders('Cookie',$this->cookie);
                            }else {
                                $this->client->getClientObject()->setHeaders('Cookie',$this->credentials->cookie);
                            }
                            return true;
                        }else {
                            //delete session
                            //add cookie to header
                            if(!empty($this->cookie)) {
                                $this->client->getClientObject()->setHeaders('Cookie',$this->cookie);
                            }else {
                                $this->client->getClientObject()->setHeaders('Cookie',$this->credentials->cookie);
                            }
                            $this->endSession();
                            //perform ASI login
                            return $this->ASILogin($username, $password);
                        }
                    }
                }
            }else {// no username or password passed...start session by adding cookie to header
                //check if logged in ... not logged in
                if(empty($this->credentials->user_id) && empty($this->credentials->app_id)) {
                    //throw new Application_Model_Exception("Username or password cannot be empty",0);
                    return false;
                }elseif(empty($this->credentials->user_id) && !empty($this->credentials->app_id)) {
                    //this is when one is logged in with application but not as a user
                    //need to return false to ensure one logs in
                    return false;
                }else {
                    //logged in ...add cookie to client headers
                    if(!empty($this->cookie)) {
                        $this->client->getClientObject()->setHeaders('Cookie',$this->cookie);
                    }else {
                        $this->client->getClientObject()->setHeaders('Cookie',$this->credentials->cookie);
                        $this->cookie = $this->credentials->cookie;
                    }
                    //initiate variables
                    $this->app_id = $this->credentials->app_id;
                    $this->user_id = $this->credentials->user_id;
                    $this->username = $this->credentials->username;
                    $this->password = $this->credentials->password;

                    return true;
                }
            }
        }catch (Application_Model_Exception $e) {
            throw new Application_Model_Exception("Could not start the session", 0, $e);
        }

    }

    /**
     *
     * sends a request to ASI to login and creates the zend session
     * @param string $username
     * @param string $password
     * @return boolean true if succesful
     * @throws Application_Model_Exception if unsuccessful
     */
    private function ASILogin($username, $password) {
        $this->credentials->app_name = $this->appName;
        $this->credentials->app_password = $this->appPassword;
        $request = $this->client->sendRequest('/session', 'post', array(
                'app_name'=>$this->credentials->app_name,
                'app_password'=>$this->credentials->app_password,
                'username'=>$username,
                'password'=>$password
        ));
        if($request->isSuccessful()) {
            //successfully logged in
            //save credentials to session
            $response = $request->getResponseBody('array');
            $this->credentials->app_id = $response['entry']['app_id'];
            $this->credentials->user_id = $response['entry']['user_id'];
            $this->credentials->username = $username;
            $this->credentials->password = $password;

            //save session credentials to session object variables
            $this->app_id = $response['entry']['app_id'];
            $this->user_id = $response['entry']['user_id'];
            $this->username = $username;
            $this->password = $password;

            //get cookie to attach to next request as header
            $this->cookie = $this->client->getResponseObject()->getHeader('Set-cookie');
            $this->credentials->cookie = $this->cookie;
            $this->client->getClientObject()->setHeaders('Cookie',$this->cookie);
            return true;
        }else {
            /**
             *303 CAS login details ..N/A in UoN
             *401 Invalid login details
             *409 A session already exists
             */
            return false;
        }
    }

    /**
     * use this method if any function returns false
     * @return string error code
     */
    public function getErrorCode() {
        return $this->client->getResponseCode();
    }

    /**
     * use this method if any function returns false
     * @return string error message
     */
    public function getErrorMessage() {
        $message = $this->client->getResponseBody();
        if(!empty($message)) {
            return $this->client->getResponseMessage().': '.$message["messages"]['0'];
        }else {
            return $this->client->getResponseMessage();
        }
    }

    /**
     *
     * Log into ASI without user name and password
     * ie log into application in use on the ASI platform
     * @throws Application_Model_Exception
     */
    public function noUserSession() {
        $this->client->getClientObject()->setHeaders('Cookie',$this->credentials->cookie);
        $this->credentials->app_name = $this->appName;
        $this->credentials->app_password = $this->appPassword;
        if(empty($this->credentials->app_id)) {
            //send request to login
            $request = $this->client->sendRequest('/session', 'post', array(
                    'app_name'=>$this->credentials->app_name,
                    'app_password'=>$this->credentials->app_password
            ));
            if($request->isSuccessful()) {
                //successfully logged in
                $responses = $request->getResponseBody('array');
                $this->credentials->app_id = $responses['entry']['app_id'];
                $this->credentials->user_id = null;

                //set cookie for next request
                $this->cookie = $this->client->getResponseObject()->getHeader('Set-cookie');
                $this->credentials->cookie = $this->cookie;
                $this->client->getClientObject()->setHeaders('Cookie',$this->cookie);
                return true;
            }else {
                /**
                 *303 CAS login details ..N/A in UoN
                 *401 Invalid login details
                 *409 A session already exists
                 */
                throw new Application_Model_Exception($request->getResponseMessage(), $request->getResponseCode());
            }
        } elseif(!empty($this->credentials->app_id) && !empty($this->credentials->cookie)) {
            $this->cookie = $this->credentials->cookie;
            $this->client->getClientObject()->setHeaders('Cookie',$this->cookie);
            return true;
        } else {
            throw new Application_Model_Exception("Unable to start client session");
        }
    }

    /**
     *
     * getter for user_id once logged in
     * @return string user id if logged in
     * @return boolean false if not logged in
     */
    public function getUserId() {
        if(!empty($this->user_id)) {
            return $this->user_id;
        }elseif(!empty($credentials->user_id)) {
            return $this->credentials->user_id;
        }else {
            return false;
        }
    }

    /**
     * get application id once logged in
     * @return string application id once logged in
     * @return boolean false if not logged in
     */
    public function getAppId() {
        if(!empty($this->app_id)) {
            return $this->app_id;
        }elseif(!empty($credentials->app_id)) {
            //$credentials = new Zend_Session_Namespace('credentials');
            return $this->credentials->app_id;
        }else {
            return false;
        }
    }

    /**
     * get username once logged in
     * @return string username once logged in
     * @return boolean false if not logged in
     */
    public function getUserName() {
        //$credentials = new Zend_Session_Namespace('credentials');
        if(!empty($this->username)) {
            return $this->username;
        }elseif(!empty($this->credentials->username)) {
            return $this->credentials->username;
        }else {
            return false;
        }
    }

    /**
     * end session if logged in and clears the zend session
     * @return boolean true if session ended successfully
     * @return boolean false if not logged in
     */
    public function endSession() {
        if(!empty($this->credentials->user_id) && !empty($this->credentials->app_id)) {
            //try to delete session from ASI -- just a failsafe
            if ($this->client->sendRequest('/session', 'get')->getResponseCode() != 404)  {
                $this->client->sendRequest("/session", "delete");
            }
            unset($this->credentials->user_id);
            $this->user_id = null;
            unset($this->credentials->app_id);
            $this->app_id = null;
            unset($this->credentials->username);
            $this->username = null;
            unset($this->credentials->password);
            $this->password = null;
            unset($this->credentials->cookie);
            $this->cookie = null;
            $this->credentials->unsetAll();
            return true;
        }else {
            return false;
        }
    }

    /**
     * Appends a session parameter to the current session with name as {$key} and its value as {$value}
     * @param string $key
     * @param string $value
     * @return boolean true if successfully added
     * @return boolean false if session parameter not created
     */
    public function setSessionParameter($key, $value) {
        if(!empty($this->credentials) && !empty($key) & !empty($value)) {
            $this->credentials->$key = $value;
            if(!empty($this->credentials->$key)) {
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }

    /**
     * Gets the value of a session parameter from the session namespace
     * @param string $key
     * existing keys: user_id, app_id, username, password
     * @return string session parameter if successful
     * @return boolean false if it fails
     */
    public function getSessionParameter($key) {
        if(!empty($this->credentials) && !empty($key)) {
            return $this->credentials->$key;
        }else {
            return false;
        }
    }

    /**
     * Unsets a session parameter identified as {$key}
     * @return boolean true if successful
     * @return false if {$key} is not a session parameter
     */
    public function unsetSessionParameter($key) {
        if(!empty($key) && isset($this->credentials->$key)) {
            unset($this->credentials->$key);
            return true;
        }else {
            return false;
        }
    }

    /**
     * Returns the zend session namespace
     */
    public function getSessionNamespace() {
        if(!empty($this->credentials)) {
            return $this->credentials;
        }else {
            return false;
        }
    }

    /**
     * call to check if user has been blocked
     * and no of tries exceeds MAX_RETRIES to add to blocked users
     * @param string $username
     * @ignore only to use during login
     * @return boolean true if user is blocked; false otherwise
     */
    public function isBlocked($username) {
        $dbtable = new Zend_Db_Table();
        //check if no of max tries reached
        $this->getNumberOfTries();
        if($this->number_of_tries >= self::MAX_RETRIES) {
            //construct array with username, date, attempt number
            $date = new Zend_Date();
            $data = array('username'=>$username, 'attempt_number'=>$this->number_of_tries, 'date_time'=>$date->toString('yyyy-MM-dd hh:mm:ss'));

            //add to table of blocked users and return true
            $rows = $dbtable->getAdapter()->insert('blocked_users', $data);

            //reset number_of_tries to zero
            $this->resetTries();
            return true;
        }else {
            //check to see if the user is already blocked and return true if so
            $select = $dbtable->getAdapter()->select();
            $select->from('blocked_users', array('id'=>'id'))
                    ->where('username = ?',$username);
            $row = $dbtable->getAdapter()->fetchRow($select);
            if(!empty($row)) {
                //already blocked return true
                $this->resetTries();
                return true;
            }else {
                return false;
            }
        }
    }

    /**
     * function called to increment number of tries if login
     * was unsuccessful
     * @ignore only to use in during login
     * @return boolean true if successful
     */
    public function incrementTries() {
        //add $number_of_tries by one
        $number_of_tries = $this->getSessionParameter('number_of_tries');
        if(!empty($number_of_tries)) {
            $this->number_of_tries = $number_of_tries;
        }
        $this->number_of_tries = $this->number_of_tries + 1;
        $this->setSessionParameter('number_of_tries', $this->number_of_tries);
    }


    public function getNumberOfTries() {
        $number_of_tries = $this->getSessionParameter('number_of_tries');
        if(!empty($number_of_tries)) {
            $this->number_of_tries = $number_of_tries;
        }
        return $this->number_of_tries;
    }
    /**
     * Checks whether $number_of_tries has exceeded CAPTCHA_RETRIES
     * so that a captcha can be added to the login form
     * @ignore only to use in during login
     * @return boolean true if captcha needed; false otherwise
     */
    public function isCaptcha() {
        $this->getNumberOfTries();
        if($this->number_of_tries >= self::CAPTCHA_RETRIES && $this->number_of_tries < self::MAX_RETRIES +1 ) {
            //signal to add a captcha to the form
            return true;
        }else {
            return false;
        }
    }

    /**
     * Checks if there is a cookie, consistent to a stored cookie on
     * the database
     * @return array {username, password} or throws an exception
     */
    public function checkRememberMe() {
        $cookie_value = isset($_COOKIE['remember_me']) ? $_COOKIE['remember_me'] : null;
        if(!empty($cookie_value)) {
            $dbtable = new Zend_Db_Table();
            $select = $dbtable->getAdapter()->select();
            $select->from('remember_me')
                    ->where('cookie_value = ?',$cookie_value);
            $row = $dbtable->getAdapter()->fetchRow($select);
            if(!empty($row)) {
                return array('username'=>$row['username']);
            }else {
                return false;
            }
        }else {
            return false;
        }
    }

    /**
     * Remembers the username and password, generates a random string
     * stores these details in the database, and sets a cookie on browser
     * with the random string as the value
     * This is done if log in is successful
     * @return boolean
     */
    public function rememberMe($username, $password) {
        $util = new Application_Model_Util();
        $random_string = $util->generateRandomString(30, true, true);

        //add to remember_me table
        $dbtable = new Zend_Db_Table();
        $select = $dbtable->getAdapter()->select();
        $select->from('remember_me')
                ->where('username = ?', $username);
        $row = $dbtable->getAdapter()->fetchRow($select);
        if(!empty($row)) {
            return true;
        }else {
            setcookie('remember_me', $random_string);
            $data = array('username'=>$username, 'password'=>$password, 'cookie_value'=>$random_string);
            $rows = $dbtable->getAdapter()->insert('remember_me', $data);
            if($rows) {
                return true;
            }else {
                return false;
            }
        }
    }

    /**
     * This function logs the attempt of the user to log in
     * @param boolean true for a successful log in
     * @return boolean true if successful
     */
    public function logAttempt($loggedin, $username, $remember=false) {
        $dbtable = new Zend_Db_Table();
        $date = new Zend_Date();
        if($loggedin) {
            $data = array('username'=>$username, 'trial_number'=>$this->number_of_tries, 'date_time'=>$date->toString('yyyy-MM-dd hh:mm:ss'), 'log_in_success'=>1, 'remember'=>$remember);
            $rows = $dbtable->getAdapter()->insert('login_log', $data);
            if($rows) {
                return true;
            }else {
                return false;
            }
        }else {
            $data = array('username'=>$username, 'trial_number'=>$this->number_of_tries, 'date_time'=>$date->toString('yyyy-MM-dd hh:mm:ss'), 'blocked'=>$this->isBlocked($username));
            $rows = $dbtable->getAdapter()->insert('login_log', $data);
            if($rows) {
                return true;
            }else {
                return false;
            }
        }
    }

    public function resetTries(){
    	$this->unsetSessionParameter('number_of_tries');
    	$this->number_of_tries = 0;
    }

    public function unblockRequest($username){
    	$dbtable = new Zend_Db_Table();
    	$sql = "update blocked_users set request_unblock =1 where username = '". $username."'";
    	$updated = $dbtable->getAdapter()->query($sql);
    	if($updated){
    		return true;
    	}else{
    		return false;
    	}
    }

   	public function unsetRedirectParams(){
    	$this->unsetSessionParameter('redirect_to');
    	$this->unsetSessionParameter('params');
    	$this->unsetSessionParameter('get_params');
    }
}