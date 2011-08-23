<?php

class campuswisdom_Model_DbTable_Classifieds extends Zend_Db_Table_Abstract
{

    protected $_name = 'classifieds';

	public function getCategorized($catid){
		$select = $this->_db->select();
		$select->distinct()
				->from($this->_name)
				->where('category_id = ?', (int)$catid)
				->where('offline = 0')
				->order('date_added DESC');
		$rowset = $this->getAdapter()->fetchAll($select);
		return $rowset;
	}
    
	public function getClassified($classid){
		$select = $this->_db->select();
		$select->distinct()
				->from($this->_name)
				->where('classified_id = ?',(int)$classid);
		$row = $this->getAdapter()->fetchRow($select);
		return $row;
	}
	
	public function getName($classid){
		$select = $this->_db->select();
		$select->distinct()
				->from($this->_name, array('title'=>'title'))
				->where('classified_id = ?',(int)$classid);
		$row = $this->getAdapter()->fetchRow($select);
		return $row['title'];
	}
	
	public function takeOffline($classid){
		return $this->update(array('offline'=>'1'), 'classified_id = '.$classid);
	}
	
	public function putOnline($classid){
		return $this->update(array('offline'=>'0'), 'classified_id = '.$classid);
	}
	
	public function getMyOfflineClassifieds($userid){
		$select = $this->_db->select();
		$select->from($this->_name)
				->where('added_by = ?',$userid)
				->where('offline = 1')
				->order('date_added DESC');
		return $this->getAdapter()->fetchAll($select);
	}
	
	public function uploadImage($url, $classid, $date_added=null, $logo=false){
		if($logo){
			$data = array('image_url'=>$url);
			return $this->getAdapter()->update($this->_name, $data, 'classified_id = '.$classid);
		}else{
			$data = array('url'=>$url, 'classified_id'=>$classid, 'is_photo'=>1, 'is_classified'=>1,'date_added'=>$date_added);
			return $this->getAdapter()->insert('photos_videos', $data);
		}
	}
	
	public function getImages($classid){
		$select = $this->_db->select();
		$select->from('photos_videos')
				->where('is_classified = 1')
				->where('classified_id = ?',(int)$classid)
				->where('is_photo = 1');
		$rowset = $this->getAdapter()->fetchAll($select);
		return $rowset;
	}
	
	public function getMyClassifieds($userid){
		$select = $this->_db->select();
		$select->from($this->_name)
				->where('added_by = ?',$userid)
				->where('offline = 0')
				->order('date_added DESC');
		return $this->getAdapter()->fetchAll($select);
	}
	
	public function onAuction($classid, $user_id){
		$select = $this->_db->select();
		$select->distinct()
				->from($this->_name)
				->where('classified_id = ?',$classid)
				->where('offline = 0')
				->where('to_auction = 1')
				->where('added_by != ?', $user_id);
		$row = $this->getAdapter()->fetchRow($select);
		if(!empty($row)){
			return true;
		}else{
			return false;
		}
	}
	
	public function getBids($classid){
		$select = $this->_db->select();
		$select->distinct()
				->from('bids')
				->where('classified_id = ?',$classid);
		return $this->getAdapter()->fetchAll($select);
	}
	
}

