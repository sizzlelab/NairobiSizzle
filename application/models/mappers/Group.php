<?php
/**
 * Handles requests to /groups module of the ASI platform.
 *
 * @author Eric Mutunga rcngei@gmail.com
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Group extends Application_Model_Mapper_Abstract {
    /**
     *
     * @var Application_Model_Group
     */
    protected $group = null;

    /**
     *
     * @param Application_Model_Group $group 
     */
    public function  __construct(Application_Model_Group $group = null) {
        if ($group instanceof Application_Model_Group) {
            $this->setGroup($group);
        }
    }

    /**
     *
     * @param Application_Model_Group $group 
     */
    public function setGroup(Application_Model_Group $group) {
        $this->group = $group;
    }

    /**
     *
     * @return Application_Model_Group
     */
    public function getGroup() {
        if (!$this->group) {
            $this->setGroup(new Application_Model_Group());
        }
        return $this->group;
    }


    /**
     * Creates a new group.
     * 
     * @param array $data
     * @param bool $createChannel
     * @return 
     */
    public function create(array $data = null, $createChannel = false) {
        $data        = $data ? $data : $this->getGroup()->getData();
        if (!$data) {
            throw new Application_Model_Exception('No data to create group');
        }
        $client      = $this->getClient();
        $dataString  = $client->encodeData($data, 'group');
        $dataString .= $createChannel ? '&create_channel=true' : '&create_channel=false';
        if ($client->sendRequest('/groups', 'post', $dataString)->isSuccessful()) {
            $response = $client->getResponseBody();
            if (!isset($response['entry'])) {
                throw new Application_Model_Exception('Unexpected error: data not received');
            }
            return $this->getGroup()->setData($response['entry']);
        } else {
            $response = $client->getResponseBody();
            if (isset($response['messages'])) {
                $this->setErrors($response['messages']);
            }
            throw new Application_Model_Exception("Error creating group : ".$client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * Creates a group by performing a
     * POST to /groups
     * @param string $title
     * @param string $type, defaults to 'open'
     * @param string $description (optional)
     * @param string $create_channel (not implemented as of now)
     * @return Application_Model_Group object
     * @throws Application_Model_Exception
    public function create($title = null, $type = null, $description = null, $create_channel=null) {
        $client = $this->getClient();
        if(!empty($description)) {
            if($type == null) {
                $type = "open";
            }
            if(empty($create_channel)) {
                $create_channel = false;
            }
            $client->sendRequest('/groups', 'post', $client->encodeData(array(
                    'title'=>$title,
                    'type'=>$type,
                    'description'=>$description
                    //'create_channel'=>$create_channel
                    ), 'group').'create_channel = '.$create_channel);
        }else {
            if($type == null) {
                $type = "open";
            }
            if(empty($create_channel)) {
                $create_channel = false;
            }
            $client->sendRequest('/groups', 'post', $client->encodeData(array(
                    'title'=>$title,
                    'type'=>"open",
                    //'create_channel'=>$create_channel
                    ), 'group').'create_channel = '.$create_channel);
        }
        //check response if successful
        if($client->isSuccessful()) {
            $data = $client->getResponseBody();
            $group = new Application_Model_Group($data['entry']);
            return $group;
        }else {
            $data = $client->getResponseBody();
            if (isset($data['messages'])) {
                $this->setErrors($data['messages']);
            }
            throw new Application_Model_Exception("Error creating group : ".$client->getResponseMessage(), $client->getResponseCode());
        }
    }
     * 
     */

    /**
     * Gets a group and its details
     * GET on /groups/<group_id> : get group details
     * @param string <group_id>
     * @return Application_Model_Group object
     * @throws Application_Model_Exception
     */
    public function fetch($group_id = null) {
        $client = Application_Model_Client::getInstance();
        $session = new Application_Model_Session();
        //call start session
        $start = $session->startSession();
        if($start) {
            if(!empty($group_id)) {
                $client->sendRequest('/groups/'.$group_id, 'get');
                if($client->isSuccessful()) {
                    //get array response from ASI
                    $data = $client->getResponseBody();
                    //create new group object
                    $group = new Application_Model_Group($data['entry']);
                    return $group;
                }else {
                    throw new Application_Model_Exception("Error fetching group: ".$client->getResponseMessage(), $client->getResponseCode());
                }
            }else {
                throw new Application_Model_Exception("Error fetching group: group id not passed", 0);
            }
        }else {
            throw new Application_Model_Exception("Error fetching group: ".$client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * Edits a group's details by performing a
     * PUT on /groups/<group_id>
     * getGroup() or createGroup() then updateGroup()
     * @param string group_id of group being updated
     * @param array in the format
     * {'key1'=>'value1', 'key2'=>'value2',}
     * @return boolean true if update is successful
     */
    public function update($group_id=null, $data=null) {
        $client = Application_Model_Client::getInstance();
        $session = new Application_Model_Session();
        //start session on ASI
        if($session->startSession()) {
            //$data = array();
            if(!empty($group_id)) {
                //perform PUT to ASI
                if($client->sendRequest('/groups/'.$group_id, 'put', $data, 'group')->isSuccessful()) {
                    return true;
                }else {
                    throw new Application_Model_Exception("Error updating group: ".$client->getResponseMessage(), $client->getResponseCode());
                }
            }else {
                throw new Application_Model_Exception('Group id has not been passed');
            }
        }else {
            throw new Application_Model_Exception("Error updating group: ".$client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * Gets members of a group by performing a
     * GET on /groups/<group_id>/@members
     * returns members within the group
     * @param string $group_id
     * @return array of member objects Application_Model_Person[]
     * @throws Application_Model_Exception
     */
    public function getMembers($group_id) {
        $client = Application_Model_Client::getInstance();
        //start session
        $session = new Application_Model_Session();
        if($session->startSession()) {
            if(!empty($group_id)) {
                $client->sendRequest('/groups/'.$group_id.'/@members', 'get');
                if($client->isSuccessful()) {
                    $data = $client->getResponseBody();
                    $members = array();
                    foreach($data['entry'] as $member) {
                        $members[] = new Application_Model_Person($member);
                    }
                    return $members;
                }else {
                    throw new Application_Model_Exception("Error fetching members: ".$client->getResponseMessage(), $client->getResponseCode());
                }
            }else {
                throw new Application_Model_Exception('No group id has been passed');
            }
        }else {
            throw new Application_Model_Exception("Error fetching members: ".$client->getResponseMessage(), $client->getResponseCode());
        }
    }

    public function delete() {
        throw new Application_Model_Exception("Function delete not supported");
    }
}