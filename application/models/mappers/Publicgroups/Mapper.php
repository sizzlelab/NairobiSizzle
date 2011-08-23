<?php
/**
 * Handles requests to /groups/@public module of the ASI platform.
 *
 * @author Eric Mutunga rcngei@gmail.com
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Publicgroups_Mapper extends Application_Model_Mapper_Abstract {
	
	/**
	 * Gets groups visible in the current session and is accessible by application
	 * GET on /groups/@public
	 * @return array of group objects visible in current session Application_Model_Group[]
	 * @param string query - to limit group listing, to match against title and description
	 * @param int per_page - number of entries to display
	 * @param int page - page to display
	 * @param string sort_by : Default is updated_at. Possible values are created_by, updated_at, title, description, creator
	 * @param string sort_order : Default is descending. Possible values are ascending and descending
	 * @throws Application_Model_Exception
	 */
	public function fetch($query=null, $per_page=null, $page=null, $sort_by='updated_at', $sort_order='descending'){
		$client = Application_Model_Client::getInstance();
		$session = new Application_Model_Session();
		if($session->startSession()){
			if(!empty($query) || !empty($per_page) || !empty($page) || $sort_by !== 'updated_at' || $sort_order !== 'descending'){
				$querystring = '?';
				if(!empty($query)){
					$querystring = $querystring.'query='.$query; 
				}if(!empty($per_page)){
					$querystring = $querystring.'&per_page='.$per_page;
				}if(!empty($page)){
					$querystring = $querystring.'&page='.$page;					
				}if($sort_by !== 'updated_at'){
					$querystring = $querystring.'&updated_at='.$sort_by;
				}if($sort_order !== 'descending'){
					$querystring = $querystring.'&sort_order='.$sort_order;
				}
				//send request
				$client->sendRequest('/groups/@public'.$querystring, 'get');	
			}else{
				$client->sendRequest('/groups/@public', 'get');	
			}
			if($client->isSuccessful()){
				$groups = array();
				$data = $client->getResponseBody();
				foreach ($data['entry'] as $group){
					$groups[] = new Application_Model_Group($group);
				}
				return $groups;
			}else{
				$msg = $client->getResponseBody();
				if(isset($msg['messages'])){
					$this->setErrors($msg['messages']);
				}
				throw new Application_Model_Exception('Error fetching groups: '.$client->getResponseMessage(), $client->getResponseCode());
			}
		}else{
			throw new Application_Model_Exception("Error fetching groups: ".$client->getResponseMessage(), $client->getResponseCode());
		}
	}
	
	public function update(){
		throw new Application_Model_Exception("Function update not supported");
	}
	
	public function delete(){
		throw new Application_Model_Exception("Function delete not supported");
	}
	
	public function create(){
		throw new Application_Model_Exception("Function create not supported");
	}
}