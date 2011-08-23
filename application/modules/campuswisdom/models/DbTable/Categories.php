<?php

class campuswisdom_Model_DbTable_Categories extends Zend_Db_Table_Abstract
{

    protected $_name = 'category';

	public function getAll(){
		$select = $this->_db->select();
		$select->from($this->_name, array('key'=>'id', 'value'=>'name'));
		$rowset = $this->getAdapter()->fetchAll($select);
		return $rowset;
	}
	
	public function getName($catid){
		if($catid == 0){
			return 'All categories';
		}
		$select = $this->_db->select();
		$select->distinct()
				->from($this->_name, array('name'=>'name'))
				->where('id = ?',(int)$catid);
		$row = $this->getAdapter()->fetchRow($select);
		return $row['name'];
	}
	
}

