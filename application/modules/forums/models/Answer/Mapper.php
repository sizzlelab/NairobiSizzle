<?php
/**
 * This class creates Answers in the ASI platform for each
 * particular group to enable the sharing of questions and
 * answers to facilitate the online discussions
 * @author Nkinyili kelvin <knkarithi@gmail.com>
 * @copyright 2010, Kelvin Nkinyili
 * @category NairobiSizzle
 * @package Forums
 * @subpackage models
 */
require_once APPLICATION_PATH . '/models/mappers/Abstract.php';
class Forums_Model_Answer_Mapper extends Application_Model_Mapper_Abstract
{
    /**
     *Creates an answer in the server for a particular thread identified by its ID
     *
     *  @param string $answer
     *
     * @param string $reference
     *
     * @param string $channel
     *
     * @return array response
     */
    
 public function create($channel=null,$answer=null,$reference=null)
    {
        //insert the given answer to the server
       //and give feedback
        $result = null;
         $data= array('title'=>'answer','body'=>$answer,'reference_to'=>$reference);
        try {
            $client = $this->getClient();
            $response=$client->sendRequest("/channels/{$channel}/@messages", 'post',$data,'message');
            //$result=$response->getResponseCode();
            if($response->isSuccessful()) {
                $result =null;
                //var_dump($result);
            }
            else {
                //var_dump($client->getResponseObject());
                $result = $response->getResponseBody();
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
    public function fetch($channel=null,$threadid=null)
    {
        $result = null;
        $querystring='?sort_order=ascending';
        try{
            $client = $this->getClient();
            $response=$client->sendRequest("/channels/{$channel}/@messages/{$threadid}/@replies{$querystring}", 'get');
            
            if($response->isSuccessful()) {
                $result =$response->getResponseBody('array');
            }
            else {
                $result = $response->getResponseMessage();
            }
        }
        catch (Exception $e)
        {
            $result = $e;
        }
        return $result;
    }

    /**
     * Update the status of an sms in the platform as being either read or unread
     * @param String $phoenumber
     * @return bool $result
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

