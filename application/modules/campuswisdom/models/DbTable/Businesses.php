<?php

class campuswisdom_Model_DbTable_Businesses extends Zend_Db_Table_Abstract
{

    protected $_name = 'business';

	public function getCategorized($catid){
		$select = $this->_db->select();
		$select->distinct()
				->from($this->_name)
				->where('category_id = ?', (int)$catid)
				->where('offline = 0')
				->order('name');
		$rowset = $this->getAdapter()->fetchAll($select);
		return $rowset;
	}
	
	public function getName($bizid){
		$select = $this->_db->select();
		$select->distinct()
				->from($this->_name, array('name'=>'name'))
				->where('id = ?', (int)$bizid);
		$row = $this->getAdapter()->fetchRow($select);
		return $row['name'];
	}
	
	public function getBusiness($bizid){
		$select = $this->_db->select();
		$select->distinct()
				->from($this->_name)
				->where('id = ?',(int)$bizid);
		$row = $this->getAdapter()->fetchRow($select);
		return $row;
	}
	
	public function getImages($bizid){
		$select = $this->_db->select();
		$select->from('photos_videos')
				->where('is_business = 1')
				->where('business_id = ?',(int)$bizid)
				->where('is_photo = 1');
		$rowset = $this->getAdapter()->fetchAll($select);
		return $rowset;
	}
	
	public function getVideos($bizid){
		$select = $this->_db->select();
		$select->from('photos_videos')
				->where('business_id = ?',(int)$bizid)
				->where('is_video = 1');
		$rowset = $this->getAdapter()->fetchAll($select);
		return $rowset;
	}
	
	public function getMyBusinesses($userid){
		$select = $this->_db->select();
		$select->from($this->_name)
				->where('added_by_id = ?',$userid)
				->where('offline = 0')
				->order('date_added DESC');
		return $this->getAdapter()->fetchAll($select);
	}
	
	public function validateName($name){
		$select = $this->_db->select();
		$select->from($this->_name,array('id'=>'id'))
				->where('name = ?', $name);
		$row = $this->getAdapter()->fetchRow($select);
		if(empty($row)){
			return true;
		}else{
			return false;
		}
	}
	
	public function getId($name){
		$select = $this->_db->select();
		$select->distinct()
				->from($this->_name, array('id'=>'id'))
				->where('name = ?', $name);
		$row = $this->getAdapter()->fetchRow($select);
		return $row['id'];
	}

	/**
	 * 
	 * Takes a business ad offline
	 * @param int $bizid
	 * @return int number of rows updated
	 */
	public function takeOffline($bizid){
		$this->getAdapter()->update('products', array('offline'=>'1'), 'business_id = '.$bizid);
		return $this->update(array('offline'=>'1'), 'id = '.$bizid);
	}
	
	public function putOnline($bizid){
		$this->getAdapter()->update('products', array('offline'=>'0'), 'business_id = '.$bizid);
		return $this->update(array('offline'=>'0'), 'id = '.$bizid);
	}
	
	public function getMyOfflineBusinesses($userid){
		$select = $this->_db->select();
		$select->from($this->_name)
				->where('added_by_id = ?',$userid)
				->where('offline = 1')
				->order('date_added DESC');
		return $this->getAdapter()->fetchAll($select);
	}
	
	public function uploadImage($url, $bizid, $date_added=null, $logo=false){
		if($logo){
			$data = array('logo'=>$url);
			return $this->getAdapter()->update($this->_name, $data, 'id = '.$bizid);
		}else{
			$data = array('url'=>$url, 'business_id'=>$bizid, 'is_photo'=>1, 'is_business'=>1,'date_added'=>$date_added);
			return $this->getAdapter()->insert('photos_videos', $data);
		}
	}
}

