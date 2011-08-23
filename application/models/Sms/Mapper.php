<?php
/**
 * This class enables the application to send smses to any
 * number using the ASI platform
 * @author Kelvin Nkinyili <knkarithi@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Sms_Mapper extends Application_Model_Mapper_Abstract {
    //put your code here

    /**
     * Send an sms to the specified number containing the given message
     * @param String $phoneNumber
     * @param String $message
     * @param array $errors
     * @return bool $result
     */
     public function create($number,$message,$replyto=null,$error=null)
    {
        if(isset($error))
            {
            if(is_null($number)||is_null($message))
             {
             $result = array('Error'=>'please enter a number and a message to send');
             }
             try
             {
                 $client = $this->getClient();
                 $response =$client->sendRequest('/sms', 'post',$data);
                 if($response->isSuccessful())
                     {
                     $result= true;
                     }
                     else
                         {
                         $result=$response->getResponseBody();
                         }
             }
             catch (Exception $e)
             {
                 $result= $e->getMessage();
             }
             
            }
         if(is_null($number)||is_null($message))
             {
             $result = false;
             }
             try
             {
                 $client = $this->getClient();
                 $response =$client->sendRequest('/sms', 'post',$data);
                 if($response->isSuccessful())
                     {
                     $result= true;
                     }
                     else
                         {
                         $result=false;
                         }
             }
             catch (Exception $e)
             {
                 $result= false;
             }
             return $result;
    }
    /**
     * Get the sent smses
     * @param String $phoneNumber
     * @return bool $result
     */
    public function fetch()
    {}

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
?>
