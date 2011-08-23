<?php

class Yearbook_IndexController extends Zend_Controller_Action {

    public function init() {
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        $this->session = $session;
    }

    public function indexAction() {
        $mapper = new Yearbook_Model_Classlist();
        $user_id = $this->session->getUserId();
        $info = $mapper->getCourse($user_id);

        if ($info) {
            //pagination
            $page = $this->getRequest()->getParam('page');
            $page = $page ? $page : 1;

            $select = $mapper->select()->from($mapper)->where('course = ? ', $info['course'])->where('year = ? ', $info['year']);
            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
            $paginator->setItemCountPerPage(5)
                      ->setCurrentPageNumber($page);
            $courses = $paginator->getCurrentItems();
            $people = array();
            foreach ($courses as $course):
                $personMapper = new Application_Model_Mapper_Person();
                $person=$personMapper->fetch($course->user_id);
                $people[] = $person;
            endforeach;
            $this->view->people = $people;
            $viewPaginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($paginator->getTotalItemCount()));
            $viewPaginator->setItemCountPerPage($paginator->getItemCountPerPage())
                          ->setCurrentPageNumber($paginator->getCurrentPageNumber())
                          ->setPageRange(5);
            $this->view->paginator = $viewPaginator;
            $this->view->course = $info;
            

        }else {
                    $this->view->notJoined=true;
                    
                    $form = new Yearbook_Form_Classlist();
                    $this->view->form = $form->setAction('/Yearbook/index/list');

        }
    }


    public function listAction() {
        $usernames=array();
        $form = new Yearbook_Form_Classlist();
        $this->view->form = $form;
	$request = $this->getRequest();

        $mapper = new Yearbook_Model_Classlist();

        if ($request->isGet()) {
	    $course = $this->session->getSessionParameter('course');
	    $year = $this->session->getSessionParameter('year');
	} elseif ($request->isPost()) {
            if (!$form->isValid($request->getPost())) {
		$this->view->form = $form;
		return $this->render();
	    }
            $course = $form->getValue('course');
            $year = $form->getValue('year');
	    $this->session->setSessionParameter('course', $course);
	    $this->session->setSessionParameter('year', $year);
	}
	if (isset($course, $year)) {
                //pagination
                $page = $this->getRequest()->getParam('page');
                $page = $page ? $page : 1;

                $select = $mapper->select()->from($mapper)->where('course = ? ', $course)->where('year = ? ', $year);
                $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
                $paginator->setItemCountPerPage(5)
                          ->setCurrentPageNumber($page);
                $courses = $paginator->getCurrentItems();
                if(count($courses)>0) {
                    $people = array();
                    foreach ($courses as $course):
                        $personMapper = new Application_Model_Mapper_Person();
                        $person=$personMapper->fetch($course->user_id);
                        $people[] = $person;
                    endforeach;
                    $this->view->people = $people;
                    $viewPaginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($paginator->getTotalItemCount()));
                    $viewPaginator->setItemCountPerPage($paginator->getItemCountPerPage())
                                  ->setCurrentPageNumber($paginator->getCurrentPageNumber())
                                  ->setPageRange(5);
                    $this->view->paginator = $viewPaginator;
                } else {
                    $this->view->noMembers=true;
                }
                $this->view->course = $form->getValues();
            }
	
	
    }

    public function personAction() {
        $form = new Yearbook_Form_Search();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $name = $form->getValue('name');

                $peopleHandle = new Application_Model_Mapper_People();
                $people = $peopleHandle->search($name);

                $this->view->people = $people;
            }
        }
    }

    public function addmeAction() {
        $request = $this->getRequest();
        $form = new Yearbook_Form_Addmyself();
        $form->submit->setLabel('Add');
        $this->view->form = $form;

        $user_id = $this->session->getUserId();
        if($this->getRequest()->isPost()) {
            $formdata=$this->getRequest()->getpost();
            if($form->isValid($formdata)) {
                $course = $form->getValue('course');
                $year = $form->getValue('year');

                $watu = new Yearbook_Model_Classlist();

                if($watu->checkuser($user_id)) {
                    $affected = $watu->insertclasslist($user_id, $course, $year);
                    if(!empty ($affected)) {
                        $this->session->setSessionParameter('message', "you have been added to this classlist");
                        $this->session->setSessionParameter('course',$course);
                        $this->session->setSessionParameter('year',$year);
                        $this->_helper->redirector('list', 'index', 'Yearbook');
                    }else {
                        $this->view->error='Sorry an error occurred.Please try again later';
                    }
                }else {
                    $this->view->error = "You already registered in ".$watu->getCourse($user_id);
                }
            }
        }
    }

    public function jobsmenuAction() {
        $message = $this->_getParam('message');
        $this->view->message = $message;
        // action body
    }

    public static function isEarlier($date1,$date2=null) {
        if($date2==null) {
            $date2=new Zend_Date(Zend_Date::ISO_8601);
        }
        else {
            $date2=new Zend_Date($date2,Zend_Date::ISO_8601);
        }
        $date1=new Zend_Date($date1,Zend_Date::ISO_8601);

        if($date1->isEarlier($date2)) {
            return TRUE;
        }
        else {
            return FALSE;
        }

    }



    public function postjobAction() {

        $request = $this->getRequest();
        $form = new Yearbook_Form_Postjob();
        $this->view->form = $form;


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $jobfield = $form->getValue('jobfield');
                $dateadv = $form->getValue('dateadv');
                $datedue = $form->getValue('datedue');
                $company = $form->getValue('company');
                $description = $form->getValue('description');
                $qualifica = $form->getValue('qualifica');
                $howtoapp = $form->getValue('howtoapp');
                $source = $form->getValue('source');


                $userid= $this->session->getUserId();
                $username= $this->session->getUserName();


                $insert = new Yearbook_Model_Jobs();
                $num = $insert->insertjobs($jobfield, $dateadv, $datedue, $company, $description, $qualifica, $howtoapp, $username,$userid);

                if($num) {
                    $this->_helper->redirector('jobsmenu', 'index', 'Yearbook', array('message' => 'Job posted successfully'));
                }else {
                    $form->populate($formData);
                }
            } else {
                $form->populate($formData); // fills form with user input and redisplays for correct filling
            }
        }
    }
    public function availjobsAction() {
        $request = $this->getRequest();
        $form = new Yearbook_Form_Availjobs();

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $jobfield = $form->getValue('jobfield');



                try {

                    $getjobs = new Yearbook_Model_Jobs();
                    $jobs = $getjobs->getavailablejobs($jobfield);
                }

                Catch(Exception $e) {

                    echo "An error was encountered while retrieving data.Try again after some minutes";

                }
                if(count($jobs) <1) {
                    $this->view->shedef=true;
                }


                $this->view->jobs = $jobs;
                $this->view->jobfield =$jobfield;


            }
        }
    }

    public function viewjobdetailsAction() {
        $jobfield= $this->_getParam('jobfield');
        $this->view->jobfield=$jobfield;

        $jobid=$this->_getParam('jobid');
        $this->view->jobid=$jobid;

        $viewdetails = new Yearbook_Model_Jobs();

        try {
            $jobdetails = $viewdetails->viewjobdetails($jobid);
        }

        Catch(Exception $e) {

            echo "An error was encountered while retrieving data.Try again after some minutes";

        }

        $this->view->jobdetails = $jobdetails;
    }

    public function reviewjoblistAction() {
        $jobfield=$this->_getParam('jobfield');

        $getjobs = new Yearbook_Model_Jobs();
        try {
            $jobs = $getjobs->getavailablejobs($jobfield);
        }

        Catch(Exception $e) {

            echo "An error was encountered while retrieving data.Try again after some minutes";

        }

        $this->view->jobs = $jobs;
        $this->view->jobfield=$jobfield;
    }

    public function shareexperienceAction() {
        $request = $this->getRequest();
        $form    = new Yearbook_Form_Postexperience();
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData= $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $field=$form->getValue('field');
                $company=$form->getValue('company');
                $jobdescription=$form->getValue('description');
                $experience=$form->getValue('experience');
                $dateposted=date('Y-m-d');


                //$userid= $this->session->getUserId();
                //$username= $this->session->getUserName();

                $userid="12345";
                $username="omosh";



                $insert =new Yearbook_Model_Jobexperiences();

                try {

                    $insert->savejobexperience($field,$company,$jobdescription,$experience,$dateposted,$username,$userid);

                    $this->view->shedef=true;

                }

                Catch(Exception $e) {

                    echo "There was an error while saving data.Try again after some minutes";

                }

            }
            else {
                $form->populate($formData); // fills form with user input and redisplays for correct filling
            }
        }
    }

    public function getjobexperiencesAction() {
        $request = $this->getRequest();
        $form = new Yearbook_Form_Availjobs();
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {

            $formData= $this->getRequest()->getPost();

            if($form->isValid($formData)) {

                $jobfield=$form->getValue('jobfield');
                $this->view->jobfield=$jobfield;


                $experiences =new Yearbook_Model_Jobexperiences();
                try {
                    $jobexperiences=$experiences->getjobexperiences($jobfield);
                }

                Catch(Exception $e) {

                    echo "An error was encountered while retrieving data.Try again after some minutes";

                }
                if(count($jobexperiences)>0) {

                    $this->view->mycheck=true;
                }

                elseif(count($jobexperiences)<1) {


                    $this->view->novalue=true;
                }



                $this->view->jobexperiences=$jobexperiences;

            }
        }
    }

    public function experiencedetailsAction() {
        $jobfield=$this->_getParam('field');
        $this->view->jobfield=$jobfield;


        $experienceid   =$this->_getParam('id');

        $details=new Yearbook_Model_Jobexperiences();
        try {
            $data=$details->jobexperiencedetails($experienceid);
        }

        Catch(Exception $e) {

            echo "An error was encountered while retrieving data.Try again after some minutes";

        }
        $this->view->data=$data;
    }

    public function reviewexperiencesAction() {
        $jobfield=$this->_getParam('jobfield');

        $experiences =new Yearbook_Model_Jobexperiences();

        $jobexperiences=$experiences->getjobexperiences($jobfield);

        $this->view->jobexperiences=$jobexperiences;
    }

    public function expmenuAction() {
        // action body
    }

    public function tipsmenuAction() {
        // action body
    }

    public function cvtipsAction() {

    }
    public function interviewtipsAction() {

    }

}
