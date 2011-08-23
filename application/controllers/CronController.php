<?php

class CronController extends Zend_Controller_Action {

    protected $person_id=null;

    protected $app_id=null;


    public function init() {
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        $person_id = $session->getUserId();
        if (empty($person_id)) {
            //user is nolonger logged in
            $this->_helper->redirector('index','login', 'default');
        } else {

            $this->person_id=$person_id;
        }
        $this->app_id=$session->getAppId();
        if(empty($this->app_id)) {
            //not logged in. Redirect to logging in page
            $this->_helper->redirector('index','login', 'default');

        }        
    }

    public function indexAction() {
        /**
         * The action sends SMS & Email notifications to
         * all members of a group subscribed to that
         * Group.
         */
        try {

            //get today's date
            $today=date('Y-m-d');
            $date=new Zend_Date($today,Zend_Date::ISO_8601);
            //add two days to the date
            $date= $date->addDay(2);
            $notification_date=$date->toString('YYYY-MM-dd');
            //get all events
            $collection_mapper =new Application_Model_Mapper_Appdata_Collection();

            //get all events that are to occur two days from now
            // $scheduled_events=array();
            foreach($collection_mapper->fetchAll($this->app_id,'Events') as $events) {
                //get event details
                $event=$events->getMetadata();
                //check if the event's date concide to the notification date
                if($event['event_date']==$notification_date) {
                    //get the channel for the group
                    $channel_mapper=new Application_Model_Mapper_Channel();
                    //loop through and get channel for the group
                    foreach($channel_mapper->fetchAll() as $channels) {
                        if($event['group_id']==$channels->getName()) {
                            $channel=$channels->getId();
                        }
                    }
                    //get the user subscribed to the channel
                    $subscriber_mapper=new Application_Model_Mapper_Channel_Subscription();
                    $subscribers=$subscriber_mapper->fetchAll($channel);
                    $subscribers=$subscribers->getUserSubscribers();
                    $phone_numbers=array();
                    foreach($subscribers as $member) {
                        $phone_number=$member->getPhoneNumber();
                        //check if user has a phone number
                        if(!empty($phone_number)) {
                            array_push($phone_numbers,$phone_number);
                        }
                    }
                    $message="The ".$event['event_name']." event is scheduled to take place on ".$event['event_date']." at ".$event['event_time']." hrs at ".$event['event_venue'];
                    //send SMSs

                    $sms_model=new Application_Model_Sms_Mapper();
                    $send_sms=$sms_model->create($phone_numbers, $message);

                }
            }
        } catch(Exception $e) {

        }
    }





}

