<?php

class campuswisdom_Model_DbTable_Products extends Zend_Db_Table_Abstract
{

    protected $_name = 'products';
    
    public function getBizProducts($bizid){
    	$select = $this->_db->select();
    	$select->from($this->_name)
    			->where('business_id = ?',(int)$bizid)
    			->order('name');
    	return $this->getAdapter()->fetchAll($select);
    }
    
    public function getProduct($pdtid){
    	$select = $this->_db->select();
    	$select->from($this->_name)
    			->where('id = ?', (int)$pdtid);
    	return $this->getAdapter()->fetchRow($select);
    }

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
    
    public function getName($pdtid){
    	$select = $this->_db->select();
    	$select->distinct()
    			->from($this->_name, array('name'=>'name'))
    			->where('id = ?',$pdtid);
    	$row = $this->getAdapter()->fetchRow($select);
    	return $row['name'];
    }
    
	public function uploadImage($url, $pdtid, $date_added=null, $logo=false){
		if($logo){
			$data = array('url'=>$url);
			return $this->getAdapter()->update($this->_name, $data, 'id = '.$pdtid);
		}else{
			$data = array('url'=>$url, 'product_id'=>$bizid, 'is_photo'=>1, 'is_product'=>1,'date_added'=>$date_added);
			return $this->getAdapter()->insert('photos_videos', $data);
		}
	}
    
}

