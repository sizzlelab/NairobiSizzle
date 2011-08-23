<?php
/**
 * This class contains the core functions that make use of the ASI platform
 * to create a question that can be discussed upon by other users of the platform
 * @author kelvin Nlinyili
 * @copyright 2010, Kelvin Nkinyili
 * @category NairobiSizzle
 * @package forums
 * @subpackage models
 */
require_once APPLICATION_PATH.'/../application/models/Mapper/Abstract.php';
class Forums_Model_Thread extends Application_Model_Mapper_Abstract {
    /**
     * This holds the current object to be sent to the server
     *
     * @var Forum_Model_ThreadMapper
     */
    private $_values = null;
    /*
     * This holds the response code got from the server
     *
     * @var Forum_Model_Thread
    */
    public $_lastresponse= null;

    public function __construct($thread = null) {
        //here load the values obtained from the form and plug them into the database

        if($thread instanceof Forums_Model_ThreadMapper) {
            $this->_values = $thread;
        }
        else {
            $this->_values=null;
        }

    }
    public function get_current_credentials() {


        $response=$client->sendRequest('/session', 'get');

        if($response->isSuccessful()) {
            $result = $response->getResponseBody();
        }
        else {
            throw new Exception('You are not logged in  '.$response->getResponseCode());
        }
        return $result;
    }
    public function create_app_log_in() {
        $client = $this->getClient();
        // $data= array('app_name'=>'test','app_password'=>'test');
        try {

            $response=$client->sendRequest('/session', 'post', $data);
            if($response->isSuccessful()) {
                $result = $response->getResponseBody();
            }
            else {
                $result= $response->getResponseCode();
            }


        }catch(Exception $e) {
            $result = array('code'=>'600','message'=>$e->getMessage());
        }
        return $result;
    }
    /**
     *This function takes a series of params from a user and creates
     * a thread in the ASI platform. The users can then reply to this
     * thread as answers are made to this post.
     * @return boolean result
     */
    public function create_to_server() {


        $result =null;
        $data= array('title'=>$this->_values->getTitle(),'body'=>$this->_values->getThread());
        $encodeuri='/appdata/'.$this->_values->getOwner().'collection';
        $client = $this->getClient();
        // $client->setConfig($config);
        try {
            //  $str = new Application_Model_Session();
            //  $str->startSession();
            $response=$client->sendRequest('/channels/aJr-EqWfmr35FWakdrL-mr/@messages', 'post',$data,'message');
            $result=$response->getResponseCode();
            if($result==201) {
                $result=true;
            }
            else {
                $result=false;
            }
            return $result;
        }catch(Exception $e) {
            $result = array('code'=>'600','message'=>$e->getMessage());
        }
        return $result;

    }
    /**
     * This function takes a group id and uses it to retrieve all threads that belong
     * to that group. The function gets all threads that have ever been posted on a
     * perticular group
     * @return array $posts
     */
    public function getAllThreadsForGroup() {
        //send the request with the get parameters
        $result = null;
        $qstring ='?exclude_replies=true';
        try {
            $client = $this->getClient();
            $response=$client->sendRequest("/channels/aJr-EqWfmr35FWakdrL-mr/@messages{$qstring}", 'get');
            $result=$response->getResponseCode();
            if($response->isSuccessful()) {
                $result =$response->getResponseBody('array');
            }
            else {
                $result = null;
            }

        }
        catch(Exception $e) {
            $result = false;
        }
        return $result;
    }
    public function getAllMyThreads() {

    }
    /**
     *Creates an answer in the server for a particular thread identified by its ID
     * @param string $answer
     * @param string $reference
     * @return array response
     */
    public function insertAnswerToThread($answer,$reference) {
        //insert the given answer to the server
       //and give feedback
        $result = null;
         $data= array('title'=>'answer','body'=>$answer,'reference_to'=>$reference);
        try {
            $client = $this->getClient();
            $response=$client->sendRequest('/channels/aJr-EqWfmr35FWakdrL-mr/@messages', 'post',$data,'message');
            //$result=$response->getResponseCode();
            if($response->isSuccessful()) {
                $result =null;
                //var_dump($result);
            }
            else {
                //var_dump($client->getResponseObject());
                $result = $response->getResponseMessage();
            }

        }
        catch(Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }
    /**
     *Obtains all the answers to a particular thread identified by its ID
     * @param string $threadid
     * @return array $answers
     */
    public function obtainAnswers($threadid)
    {
        $result = null;
        $querystring='?sort_order=ascending';
        try{
            $client = $this->getClient();
            $response=$client->sendRequest("/channels/aJr-EqWfmr35FWakdrL-mr/@messages/{$threadid}/@replies{$querystring}", 'get');
            $result=$response->getResponseCode();
            if($response->isSuccessful()) {
                $result =$response->getResponseBody('array');
            }
            else {
                $result = null;
            }
        }
        catch (Exception $e)
        {
            $result = $e->getMessage();
        }
        return $result;
    }
    public function agreeWithComment() {

    }
    public function deleteQuestion() {

    }
    public function create()
    {}

    /**
     * Fetch a record(s) from the ASI platform under some module e.g. '/people', '/groups'
     *
     * This method should implement a GET HTTP request on the platform.
     */
    public function fetch()
    {}

    /**
     * Update a record on the ASI platform under some module e.g. '/people', '/groups'
     *
     * This method should implement a PUT HTTP request on the platform.
     */
    public function update()
    {}

    /**
     * Delete a record on the ASI platform under some module e.g. '/people', '/groups'
     *
     * This method should implement a DELETE HTTP request on the platform.
     */
    public function delete()
    {}

}

