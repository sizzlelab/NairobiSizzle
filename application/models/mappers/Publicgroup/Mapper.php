<?php

class Application_Model_Mapper_Publicgroup_Mapper extends Application_Model_Mapper_Abstract {
	
	/**
	 * GET on /groups/@public/<group_id> : get this group's details
	 * @param <group_id>
	 * @return group object Application_Model_Group
	 * @throws Application_Model_Exception
	 */
	public function fetch($group_id=null){
		$client = Application_Model_Client::getInstance();
		$session = new Application_Model_Session();
		//call start session
		$start = $session->startSession();
		if($start){
			if(!empty($group_id)){
				$client->sendRequest('/groups/@public/'.$group_id, 'get');
				if($client->isSuccessful()){
					//get array response from ASI
					$data = $client->getResponseBody();
					//create new group object
					$group = new Application_Model_Group($data['entry']);
					return $group;
				}else{
					$msg = $client->getResponseBody();
					if(isset($msg['messages'])){
						$this->setErrors($msg['messages']);
					}
					throw new Application_Model_Exception("Error fetching group: ".$client->getResponseMessage(), $client->getResponseCode());
				}
			}else{
				throw new Application_Model_Exception("Error fetching group: group id not passed", 0);
			}
		}else{
			throw new Application_Model_Exception("Error fetching group: ".$client->getResponseMessage(), $client->getResponseCode());
		}
	}
	
	/**
	 * PUT on /groups/@public/<group_id>
	 * fetchPublicGroup() to get group_id
	 * @param string group_id of group being updated 
	 * @param string $title
	 * @param string $type : can only be open, closed or hidden
	 * @param string $description
	 * @return boolean true if update is successful
	 */
	//public function update($group_id=null, $title=null, $type=null, $description=null){
	public function update($group_id=null, $data=null){
		$client = Application_Model_Client::getInstance();
		$session = new Application_Model_Session();
		//start session on ASI
		if($session->startSession()){
			//$data = array();
			if(!empty($group_id)){
//				if(!empty($title)){
//					array_unshift($data,array('key'=>'title','value'=>$title));
//					//$data['title'] = $title; 
//				}
//				if(!empty($type)){
//					array_unshift($data,array('key'=>'type','value'=>$type));
//					//$data['type'] = $type; 
//				}
//				if(!empty($description)){
//					array_unshift($data,array('key'=>'description','value'=>$description));
//					//$data['description'] = $description; 
//				}
				//perform PUT to ASI
				if($client->sendRequest('/groups/@public/'.$group_id, 'put', $data, 'group')->isSuccessful()){
					return true;
				}else{
					$msg = $client->getResponseBody();
					if(isset($msg['messages'])){
						$this->setErrors($msg['messages']);
					}
					throw new Application_Model_Exception("Error updating group: ".$client->getResponseMessage(), $client->getResponseCode());
				}		
			}else{
				throw new Application_Model_Exception('Group id has not been passed');
			}
		}else{
			throw new Application_Model_Exception("Error updating group: ".$client->getResponseMessage(), $client->getResponseCode());
		}	
	}
	
	/**
	 * GET on /groups/@public/<group_id>/@members
	 * returns members within the group
	 * @param string <group_id>
	 * @return array of member objects Application_Model_Person[]
	 * @throws Application_Model_Exception
	 */
	public function getMembers($group_id){
		$client = Application_Model_Client::getInstance();
		//start session
		$session = new Application_Model_Session();
		if($session->startSession()){
			if(!empty($group_id)){
				$client->sendRequest('/groups/'.$group_id.'/@members', 'get');
				if($client->isSuccessful()){
						$data = $client->getResponseBody();
						$members = array(); 
						foreach($data['entry'] as $member){
							$members[] = new Application_Model_Person($member);
						}
						return $members;
					
				}else{
					$msg = $client->getResponseBody();
					if(isset($msg['messages'])){
						$this->setErrors($msg['messages']);
					}
					throw new Application_Model_Exception("Error fetching members: ".$client->getResponseMessage(), $client->getResponseCode()); 
				}
			}else{
					throw new Application_Model_Exception('No group id has been passed');
			}		
		}else{
			throw new Application_Model_Exception("Error fetching members: ".$client->getResponseMessage(), $client->getResponseCode());
		}
	}

	/**
	 * GET on /groups/@public/<group_id>/@pending
	 * returns the pending members of this group. members that have requested for membership
	 * @see /people/user_id/@groups/<group_id> to accept requests
	 * @param string <group_id>
	 * @return array of people objects Application_Model_Person[]
	 * @throws Application_Model_Exception
	 */
	public function pendingRequests($group_id){
		$client = Application_Model_Client::getInstance();
		//start session
		$session = new Application_Model_Session();
		if($session->startSession()){
			if(!empty($group_id)){
				$client->sendRequest('/groups/@public/'.$group_id.'/@pending', 'get');
				if($client->isSuccessful()){
					$data = $client->getResponseBody();
					$members = array(); 
					foreach($data['entry'] as $member){
						$members[] = new Application_Model_Person($member);
					}
					return $members;
				}else{
					$msg = $client->getResponseBody();
					if(isset($msg['messages'])){
						$this->setErrors($msg['messages']);
					}
					throw new Application_Model_Exception("Error fetching members: ".$client->getResponseMessage(), $client->getResponseCode()); 
				}	
			}else{
				throw new Application_Model_Exception('No group id has been passed');
			}	
		}else{
			throw new Application_Model_Exception("Error fetching pending requests: ".$client->getResponseMessage(), $client->getResponseCode());
		}
	}
	
	public function delete(){
		throw new Application_Model_Exception("Function delete not supported");
	}
	
	public function create(){
		throw new Application_Model_Exception("Function create not supported");
	}
}