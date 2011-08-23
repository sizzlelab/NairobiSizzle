<?php
/**
 * This class creates threads in the ASI platform for each
 * particular group to enable the sharing of questions and
 * answers to facilitate the online discussions
 * @author Nkinyili kelvin <knkarithi@gmail.com>
 * @copyright 2010, Kelvin Nkinyili
 * @category NairobiSizzle
 * @package Forums
 * @subpackage models
 */
require_once APPLICATION_PATH . '/models/mappers/Abstract.php';
class Forums_Model_Thread_Mapper extends Application_Model_Mapper_Abstract
{
    protected $_values=null;
    public function __construct($thread = null) {
        //here load the values obtained from the form and plug them into the database

        if($thread instanceof Forums_Model_ThreadMapper) {
            $this->_values = $thread;
        }
        else {
            $this->_values=null;
        }

    }
    /**
     *This function takes a series of params from a user and creates
     * a thread in the ASI platform. The users can then reply to this
     * thread as answers are made to this post.
     *
     * @param $channel
     *
     * @param @var Forums_Model_ThreadMapper
     * 
     * @return boolean result
     */
 public function create($channel=null,$thread=null)
    {
     $result =null;
        $data= array('title'=>$this->_values->getTitle(),'body'=>$this->_values->getThread());
        //$encodeuri='/appdata/'.$this->_values->getOwner().'collection';
        $client = $this->getClient();
        
        try {
//              $str = new Application_Model_Session();
//              $str->startSession();
            $response=$client->sendRequest("/channels/{$channel}/@messages", 'post',$data,'message');
            
            if($response->isSuccessful()) {
                $result=false;
            }
            else {
                $msg=$response->getResponseBody();
                $result=$response->getResponseMessage().': '.$msg['messages'][0];
            }
            
        }catch(Exception $e) {
            $result = array('code'=>'600','message'=>$e->getMessage());
        }
        return $result;

       
    }
    /**
     * This function takes a group id and uses it to retrieve all threads that belong
     * to that group. The function gets all threads that have ever been posted on a
     * perticular group
     *
     * @param string $channelid
     *
     * @param string $threadid
     *
     * @return array $posts
     * 
     */
    public function fetch($channel=null,$threadid=null)
    {
        //send the request with the get parameters
        //check if a thread value exists to fetch all answers of a channel 
        //questions
        $qstring="";
        $requesturi="";
        if(isset($threadid))
            {
            $result = null;
            $requesturi="/channels/{$channel}/@messages/{$threadid}";
         }
         else{
        $result = null;
        $qstring ='?exclude_replies=true';
        $requesturi="/channels/{$channel}/@messages{$qstring}";
         }
        try {
            $client = $this->getClient();
            $response=$client->sendRequest($requesturi, 'get');
            $result=$response->getResponseCode();
            if($response->isSuccessful()) {
                $result =$response->getResponseBody('array');
            }
            else {
                $result = $response->getResponseObject()->isError();
            }

        }
        catch(Exception $e) {
            $result = $e->__toString();
        }
        return $result;
    }


    /**
     * Edit a question that has already been posted to enable the user to
     * be more specific or
     * @param String $thread
     * @return bool $result
     */
    public function update()
    {}

    /**
     * Delete a thread on the ASI platform under the channels module
     *
     * This method should implement a DELETE HTTP request on the platform.
     *
     * @param string $threadid
     */
    public function delete()
    {
        //To be implemented soon
    }

}

