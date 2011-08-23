<?php

class Yearbook_Model_Jobs extends Zend_Db_Table
{
    
protected $_name='jobs';

public function insertjobs($jobfield,$dateadv,$datedue,$company,$description,$qualifica,$howtoapp,$username,$userid)
{
    $data=array('field'=>$jobfield ,'dateadvertised'=>$dateadv, 'duedate'=>$datedue,'company'=>$company,'description'=>$description,'qualifications'=>$qualifica,'appprocedure'=>$howtoapp,'postedby'=>$username,'userid'=>$userid);
    
	try{
		return $this->insert($data);
    }catch(Exception $e)
    {
    	echo $e->getMessage();
    }
}


 public function getavailablejobs($jobfield)
 {

   $select = $this->select()
             ->from('jobs')
         
             ->where('field = ?', $jobfield)
             ->order('duedate');

   
   return $this->getAdapter()->fetchAll($select);
       
   
 }

 public function viewjobdetails($jobid)
 {

 $select = $this->select()
             ->from('jobs')

             ->where('uniqueid = ?', $jobid);


   return $this->getAdapter()->fetchAll($select);




 }


 
}

?>