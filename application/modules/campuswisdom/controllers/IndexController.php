<?php

class Campuswisdom_IndexController extends Zend_Controller_Action
{

  /**
      *
      * @var int the person_id of the currently logged in user
      */
     protected $person_id=null;

     protected $app_id=null;


     public function init() {
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
       		$person_id = $session->getUserId();
        if (empty($person_id)) {
            //user is nolonger logged in
            $this->_helper->redirector('index','login','default');
        }
        else{
             $this->person_id=$person_id;
        }

        $this->app_id=$session->getAppId();
        //echo $this->app_id;
        if(empty($this->app_id)){

             //not logged in. Redirect to logging in page
             $this->_helper->redirector('index','login','default');

        

        }
    }

    public function indexAction()
    {
        // action body .. display the menus for campus wisdom in view
        $this->view->title="Campus Wisdom";
        //get events due this week
       $week_end=date("Y-m-d", strtotime(date("Y").'W'.date('W')."7"));
       $week_start=date("Y-m-d", strtotime(date("Y").'W'.date('W')."1"));
       //get events
        $eventMapper=new Application_Model_Mapper_Appdata_Collection();
        $all_events=$eventMapper->fetchAll($this->app_id,$tags='events');
        //var_dump($all_events);
        //check if event is in this week
        $this_week_events=0;
        foreach($all_events as $events)
            {
                $meta=$events->getMetadata();
                //echo $meta['event_date'].$week_start.$week_end;
                //get all events happening today and beyond
                if($meta['event_date']>=$week_start && $meta['event_date']<=$week_end )
                {
                     $this_week_events+=1;
                }
            }
        $this->view->week=$this_week_events;
    }

 	public function bizadsAction()
    {
		$categories = new Campuswisdom_Model_DbTable_Categories();
        $list = $categories->fetchAll();
        $pagination = new Application_Model_ZendPagination();
        $this->view->categories = $pagination->paginate($list, $this->_getParam('page'));
    }

    public function allbusinessesAction()
    {
       	$businesses = new campuswisdom_Model_DbTable_Businesses();
    	$list = $businesses->fetchAll('offline = 0', 'name');
    	$category = new campuswisdom_Model_DbTable_Categories();
    	$this->view->category = $category;
    	$pagination = new Application_Model_ZendPagination();
    	if(!empty($list)){
    		$this->view->businesses = $pagination->paginate($list, $this->_getParam('page'));	
    	}else{
    		$this->view->businesses = $list;	
    	}
    }

}





