<?php
/**
 * 
 * @author ricko
 * @copyright eric 2010
 * @category ASI/groups
 * This class contains methods to implement ASI/groups
 * 
 */
	
/**
 * @deprecated
 */
class Application_Model_PublicGroup extends Application_Model_Abstract{
	/**
	 * group object to store details of a group visible while logged in
	 * different from Application_Model_Group since it has an extra field - is_member
	 */
	protected $title=null;
	protected $numberOfMembers = null;
	protected $createdAt = null;
	protected $groupType = null;
	protected $id = null;
	protected $createdBy = null;
	protected $description = null;
	protected $error = null;
	protected $isMember = null;
	protected $isAdmin = null;
	
	/**
	 * stores group details as an object $this
	 * @deprecated
	 */
	private function initiateGroup(){
		$client = Application_Model_Client::getInstance();
		$response = $client->getResponseBody('array');
		$this->setCreatedAt($response['createdAt']);
		$this->setCreatedBy($response['createdBy']);
		$this->setTitle($response['title']);
		$this->setNumberOfMembers($response['numberOfMembers']);
		$this->setGroupType($response['groupType']);
		$this->setId($response['id']);
		$this->setDescription($response['description']);
	}
	
	/**
	 * call function to return HTTP response message
	 * @return HTTP response message (in case of error)
	 */
	public function getResponseMessage(){
		if(!empty($this->error)){
			return $this->error;
		}
	}
	
	/**
	 * 
	 * setters and getters
	 * NOTE: can only change title, type & description in ASI
	 */
	
	public function getID(){
		return $this->id;
	}
	/**
	 * Uneditable in ASI platform
	 * @param $id
	 */
	public function setID($id){
		$this->id = $id;
		return $this;
	}
	
	public function getDescription(){
		return $this->description;
	}

	public function setDescription($description){
		$this->description = $description;
		return $this;
	}
		
	public function getNumberOfMembers(){
		return $this->numberOfMembers;
	}
	/**
	 * Uneditable in ASI platform
	 * @param unknown_type $numberOfMembers
	 */
	public function setNumberOfMembers($numberOfMembers){
		$this->numberOfMembers = $numberOfMembers;
		return $this;	
	}
	
	public function getCreatedAt(){
		return $this->createdAt;
	}
	/**
	 * Uneditable in ASI platform
	 * @param $createdAt
	 */
	public function setCreatedAt($createdAt){
		$this->createdAt = $createdAt;
		return $this;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function setTitle($title){
		$this->title = $title;
		return $this;
	}
	
	public function getGroupType(){
		return $this->groupType;
	}
	
	public function setGroupType($groupType){
		if($groupType === "open" || $groupType === "closed" || $groupType === "hidden" || $groupType === "personal"){
			$this->groupType = $groupType;	
		}
		return $this;
	}
	/**
	 * 
	 * @todo to get details of creator or not?
	 */
	public function getCreatedBy(){
		return $this->createdBy;
	}
	
	/**
	 * Uneditable in ASI platform
	 * @param unknown_type $createdBy
	 */
	public function setCreatedBy($createdBy=null){
		$this->createdBy = $createdBy;
		return $this;
	}
	
	public function setIsMember($isMember){
		$this->isMember = $isMember;
		return $this;
	}
	
	/**
	 * if user is member
	 * @return boolean
	 */
	public function getIsMember(){
		return $this->isMember;
	}
	
	public function setIsAdmin($isAdmin){
		$this->isAdmin = $isAdmin;
		return $this;
	}
	
	/**
	 * if user is member
	 * @return boolean
	 */
	public function getisAdmin(){
		return $this->isAdmin;
	}
}