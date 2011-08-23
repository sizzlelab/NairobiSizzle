<?php
/**
 * This class associates each answer with an agree or
 * post link with which a user can then add their opinion
 * @author Nkinyili kelvin <knkarithi@gmail.com>
 * @copyright 2010, Kelvin Nkinyili
 * @category NairobiSizzle
 * @package Forums
 * @subpackage models
 */
require_once APPLICATION_PATH . '/models/mappers/Abstract.php';
class Forums_Model_Misc_Mapper extends Application_Model_Mapper_Abstract
{

    /**
     * This function attaches a flag to a message that indicates whether
     * a person agrees or disagrees with what has been proposed in the
     * answer. This gives credibility to the answer given
     *
     * @param string $threadid
     * @return bool true
     */
    public function agree($channel =null,$id=null)
    {
         $result = null;
         $data= array('title'=>'agree','body'=>'agreement','reference_to'=>$id);
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
                $result = $response->getResponseMessage();
            }

        }
        catch(Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }
    public function create()
    {}
    public function fetch()
    {}
    public function update()
    {
        
    }
    public function delete()
    {}
}

