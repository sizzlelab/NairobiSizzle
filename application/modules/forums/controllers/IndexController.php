<?php

class Forums_IndexController extends Zend_Controller_Action
{
    private $sessionHandle = null;
    private $groupHandle = null;
    private $credentials = null;
    protected $instance = null;
    private $paginator = null;

    public function init() {
        $session = new Application_Model_Session();
        $credentials = new Zend_Session_Namespace('credentials');
        if(!$session->startSession() && empty($credentials->user_id)) {
            $session->setSessionParameter('redirect_to', 1);
            $session->setSessionParameter('get_params', $this->_getAllParams());
            $session->setSessionParameter('params', $this->getRequest()->getParams());
            $this->_helper->redirector('index','login','default');
        }
        $this->sessionHandle = $session;
    }

    public function indexAction() {
        $sessionHandle = new Application_Model_Session();
        $role = $sessionHandle->getSessionParameter('role');
        $msg = $sessionHandle->getSessionParameter('msg');
        $error = $sessionHandle->getSessionParameter('err');

        $this->view->role = $role;
        if($msg) {$this->view->msg = $msg; $msg = $sessionHandle->unsetSessionParameter('msg');} else $this->view->msg = null;
        if($error) {$this->view->error = $error; $error = $sessionHandle->unsetSessionParameter('err');} else $this->view->error = null;

        try {
            $this->sessionHandle->unsetSessionParameter('public');
            $userID = $this->sessionHandle->getUserId();
            $userName = $this->sessionHandle->getUserName();
            $personHandle = new Application_Model_Mapper_Person_Groups();
            $myGroups = $personHandle->fetch($userID); 

            $paginator = Zend_Paginator::factory($myGroups);
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
            $paginator->setItemCountPerPage(5);
            $paginator->setCurrentPageNumber($this->_getParam('page'));

            try {
                $personHandle = new Application_Model_Mapper_Person_GroupInvites();
                $ignoredInvite = new Forums_Model_DbTable_IgnoredInvites();
                $sessionHandle = new Application_Model_Session();
                $sessionHandle->startSession();

                $myInvites = $personHandle->fetch($sessionHandle->getUserId());
                $ignored = $ignoredInvite->getIgnored($userID);

                $invites = array();
                foreach ($myInvites as $invite) {
                    $invites[] = $invite->getId();
                }

                foreach ($ignored as $shittyInvite) {
                    $index = array_search($shittyInvite['groupID'], $invites);                    
                    if($index >= 0)
                        unset ($myInvites[$index]);
                }
                $sessionHandle->setSessionParameter('invites', $myInvites);
            } catch (Exception $exc) {
                if($exc->getCode() == 403)
                    $this->view->error = "Sorry, you do not have sufficent rights to perform this action. Please contact your group admin.";
                else
                    $this->view->error = "Error. Cannot retrieve pending invites. Please try again later.";
            }

            $sessionHandle->setSessionParameter('invites', $myInvites);

            $this->view->paginator = $paginator;
            $this->view->invites = $myInvites;
            $this->view->userID = $userID;
            $this->view->userName = $userName;
            $this->view->myGroups = $myGroups;
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
    }

    public function newAction() {
        $this->view->title = "Create Group";
        $form = new Forums_Form_Groups();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $title = $form->getValue('title');
                $description = $form->getValue('description');
                $type = $form->getValue('type');
                
                if ($type == 0)
                    $type = "open";
                else if ($type == 1)
                    $type = "closed";
                else if ($type == 2)
                    $type = "hidden";
                else
                    $type = "open";

                $this->sessionHandle = new Application_Model_Session();
                $this->sessionHandle->startSession();

                try {
                    $this->groupHandle = new Application_Model_Mapper_Group();
                    $createdGroup = $this->groupHandle->create(array(
                        'title' => $title,
                        'type'  => $type,
                        'description' => $description) , false);
                } catch (Exception $exc) {
                    if ($exc->getCode() == 400)
                        $this->view->error = "Incorrect entry. Make sure the title of the group is unique";
                    else
                        $this->view->error = "An error occured while creating a new group. Please try again later.";
                    $form->populate($formData);
                }

            if(isset ($createdGroup)) {
                    $groupInfo = new Forums_Model_DbTable_GroupInfo();
                    $channelHandle = new Application_Model_Mapper_Channel();

                    $data = array('name' => $createdGroup->getTitle(),
                        'description' => $createdGroup->getDescription(),
                        'channel_type' => 'group',
                        'group_id' => $createdGroup->getId()
                        );

                        try {
                            $createdChannel = $channelHandle->create($data);
                            $groupInfo->setRecord($createdGroup->getId(), $createdChannel->getId());
                        } catch (Exception $exc) {
                            $this->view->error = "An error occured while creating a new group. Please try again later.";
                        }

                    $this->_helper->redirector('profile', 'index', 'forums',array('id' => $createdGroup->getID()));
                } else {
                    $form->populate($formData);
                    $this->view->error = "Error: Could not create group. Please try again later.";
                }
            }
        }
    }

    public function profileAction() {
        $form = new Forums_Form_GroupProfile();
        $sessionHandle = new Application_Model_Session();

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $index = $form->getValue('group');
                $groupID = $form->getGroupID($index);

                try {
                    $this->groupHandle = new Application_Model_Mapper_Group();
                    $group = $this->groupHandle->fetch($groupID);
                } catch (Exception $exc) {
                    $this->view->error = "Error fetching group information. Please try again later.";
                    return;
                }

                try {
                    $groupInfo = new Forums_Model_DbTable_GroupInfo();
                    $record = $groupInfo->getRecord($groupID);
                } catch (Exception $exc) {}
                
                if(!$group->getIsMember()) {
                    $role = 10;
                } else {
                    if($group->getisAdmin())
                        $role = 1;
                    else
                        $role = 0;
                }
                $sessionHandle->unsetSessionParameter('role');
                $sessionHandle->setSessionParameter('role', $role);
                $this->view->role = $role;

                $dateFormatter = new Application_Model_Date();
                $date = $dateFormatter->relativeTime($group->getCreatedAt());
                
                $group = array('id' => $group->getID(),
                    'title' => $group->getTitle(),
                    'type' => $group->getGroupType(),
                    'description' => $group->getDescription(),
                    'created_at' => $date,
                    'created_by' => $group->getCreatedBy(),
                    'members' => $group->getNumberOfMembers(),
                );

                $this->view->group = $group;
                $this->view->form = null;
            } else {
                $form->populate($formData);
            }
        } else {
            if ($this->_hasParam('id')) {
                $this->view->form = null;
                $groupID = $this->_getParam('id');

                $sessionHandle->setSessionParameter('group', $groupID);

                try {
                    $this->groupHandle = new Application_Model_Mapper_Group();
                    $group = $this->groupHandle->fetch($groupID);
                } catch (Exception $exc) {
                    $this->view->error = "Error occured while fetching group information.";
                    return;
                }

                try {
                    $groupInfo = new Forums_Model_DbTable_GroupInfo();
                    $record = $groupInfo->getRecord($groupID);
                } catch (Exception $exc) {}

                if(!$group->getIsMember()) {
                        $role = 10;
                } else {
                    if($group->getisAdmin())
                        $role = 1;
                    else
                        $role = 0;
                }
                $sessionHandle->unsetSessionParameter('role');
                $sessionHandle->setSessionParameter('role', $role);

                $this->view->role = $role;

                $dateFormatter = new Application_Model_Date();
                $date = $dateFormatter->relativeTime($group->getCreatedAt());

                $group = array('id' => $group->getID(),
                    'title' => $group->getTitle(),
                    'type' => $group->getGroupType(),
                    'description' => $group->getDescription(),
                    'created_at' => $date,
                    'created_by' => $group->getCreatedBy(),
                    'members' => $group->getNumberOfMembers(),
                );

                $this->view->group = $group;
                $this->view->form = null;
            }
        }
    }

    public function allgroupsAction() {
        $form = new Forums_Form_AllGroups();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $query = $form->getValue('query');
                $per_page = $form->getValue('per_page');
                $sort_by = $form->getValue('sort_by');
                $sort_order = $form->getValue('sort_order');

                if ($sort_by == 0)
                    $sort_by = "title";
                else if ($sort_by == 1)
                    $sort_by = "updated_at";
                else if ($sort_by == 2)
                    $sort_by = "created_at";
                else if ($sort_by == 3)
                    $sort_by = "description";
                else if ($sort_by == 4)
                    $sort_by = "creator";
                else
                    $sort_by = "title";

                if ($sort_order == 0)
                    $sort_order = "ascending";
                else if ($sort_order == 1)
                    $sort_order = "descending";
                else
                    $sort_order = "ascending";

                $this->_helper->redirector('view-groups', 'index', 'forums', array('query' => urlencode($query), 'per_page' => $per_page, 'sort_by' => $sort_by, 'sort_order' => $sort_order));
            }
        }
    }

    public function membersAction() {
        $sessionHandle = new Application_Model_Session();
        $role = $sessionHandle->getSessionParameter('role');

        $this->view->role = $role;
        
        if ($this->_hasParam('id')) {
            $groupID = $this->_getParam('id');
            $this->view->msg = $this->_getParam('msg',null);
            $this->groupHandle = new Application_Model_Mapper_Group();

            try {
                $groupMembers = $this->groupHandle->getMembers($groupID);
            } catch (Exception $exc) {
                if($exc->getCode() == 403)
                    $this->view->error = "Sorry, you do not have sufficent rights to perform this action. Please contact your group admin.";
                else
                    $this->view->error = "Could not fetch group members.";
                return;
            }

            $paginator = Zend_Paginator::factory($groupMembers);
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
            $paginator->setItemCountPerPage(5);
            $paginator->setCurrentPageNumber($this->_getParam('page'));

            $this->view->groupMembers = $paginator;
            $this->view->groupTitle = $this->_getParam('title','');
            $this->view->groupType = $this->_getParam('type','open');
            $this->view->groupID = $groupID;
        }
    }

    public function personAction() {
        if ($this->_hasParam('persID')) {
            $this->_helper->redirector('view-full-info', 'person', 'default', array('id'=>$this->_getParam('persID')));
        }
    }

    public function requestsAction() {
        $sessionHandle = new Application_Model_Session();
        $role = $sessionHandle->getSessionParameter('role');
        
        $msg = $sessionHandle->getSessionParameter('rqstMsg');
        if($msg) { $this->view->msg = $msg; $sessionHandle->unsetSessionParameter('rqstMsg'); } else $this->view->msg = null;
        $error = $sessionHandle->getSessionParameter('rqstErr');
        if($error) { $this->view->error = $error; $sessionHandle->unsetSessionParameter('rqstErr'); } else $this->view->error = null;

        $this->view->role = $role;
        
        if ($this->_hasParam('id')) {
            $groupID = $this->_getParam('id');
            $this->view->msg = $this->_getParam('msg',null);

            try {
                $this->groupHandle = new Application_Model_Mapper_Publicgroup_Mapper();
                $requestors = $this->groupHandle->pendingRequests($groupID);
            } catch (Exception $exc) {
                if($exc->getCode() == 403)
                    $this->view->error = "Sorry, you do not have sufficent rights to perform this action. Please contact your group admin.";
                else
                    $this->view->error = "Error. Cannot retrieve pending requests. Please try again later.";
                return;
            }

            $this->view->requestors = $requestors;
            $this->view->groupID = $groupID;
        }
    }

    public function joinAction() {
        $groupID = $this->_getParam('groupID');
        $groupType = $this->_getParam('type');
        $this->sessionHandle = new Application_Model_Session();

        $this->sessionHandle->startSession();
        $userID = $this->sessionHandle->getUserId();

        try {
            $personHandle = new Application_Model_Mapper_Person_GroupMembership();

            if ($personHandle->create($userID, $groupID) == true) {
                if($groupType == 'open')
                    $this->view->msg = "You have successfully joined this group";
                else
                    $this->view->msg = "A membership request has been sent. You will be a member once your membership request has been accepted";
            }
        } catch (Exception $exc) {
            $this->view->error =$exc->getCode()==409?'Operation not allowed. You are already a member of this group':$exc->getMessage();
        }
    }

    public function inviteAction() {
        $userID = $this->_getParam('userID');
        $groupID = $this->_getParam('groupID');

        try {
            $personHandle = new Application_Model_Mapper_Person_GroupMembership();
            $sessionHandle = new Application_Model_Session();

            if ($personHandle->create($userID, $groupID)) {
                $sessionHandle->setSessionParameter('msg', "A membership invitation has been sent to <b>{$this->_getParam('username')}</b>");
                
                $this->_helper->redirector('index','index','forums');
            }
            else
                $this->view->error = "An error occured while sending the invitation. Please try again later";
        } catch (Exception $exc) {
            if($exc->getCode() == 403)
                $this->view->response = "Sorry. Only a group admin is permitted to invite members to join the group. Please contact your group administrator";
            else if($exc->getCode() == 409)
                $this->view->response = "Sorry. User <b>{$this->_getParam('username')}</b> is already a member of this group or has a pending membership invitation";
            else
                $this->view->error = "An error occured while sending the invitation. Please try again later";
        }
    }

    public function ignoreinviteAction() {
        $ignoredInvites = new Forums_Model_DbTable_IgnoredInvites();
        $sessionHandle = new Application_Model_Session();

        $groupID = $this->_getParam('groupID','');
        $userID = $this->_getParam('userID','');
        
        try {
            $ignoredInvites->setIgnored($userID, $groupID);
            $sessionHandle->setSessionParameter('msg', 'Group invite has been ignored successfully');
            $this->_helper->redirector('index','index','forums',array(), null, true);
        } catch (Exception $exc) {
            $this->view->error = "There was an error while ignoring group invite. Please try again later.";
        }
    }

    public function confirminviteAction() {
        $groupID = $this->_getParam('groupID');
        $userID = $this->_getParam('userID');

        try {
            $personHandle = new Application_Model_Mapper_Person_GroupMembership();

            if($personHandle->create($userID, $groupID)) {
                $this->view->response = "Confirmation successfull";
                $sessionHandle->setSessionParameter('msg', 'Group invite has been confirmed successfully');
                $this->_helper->redirector('index','index','forums',array(), null, true);
            }
        } catch (Exception $exc) {
            $this->view->error = "Error confirming group invitation. Please try again later.";
        }
    }

    public function viewGroupsAction() {
        $params = $this->_getAllParams();

        try {
                $this->groupHandle = new Application_Model_Mapper_Publicgroups_Mapper();
                $groups = $this->groupHandle->fetch($params['query'], null, null, $params['sort_by'], $params['sort_order']);
            } catch (Exception $exc) {
                $groups = array();
                $this->view->error = "Error fetching group(s). Please try again later.";
            }
            
            $paginator = Zend_Paginator::factory($groups);
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
            $paginator->setItemCountPerPage($params['per_page']);
            $paginator->setCurrentPageNumber($this->_getParam('page'));

            $this->view->paginator = $paginator;
    }

    public function updateAction() {
        $sessionHandle = new Application_Model_Session();
        $role = $sessionHandle->getSessionParameter('role');

        $this->view->role = $role;
        
        $group = $this->_getAllParams();
        $this->view->groupID = $group['groupID'];
        $form = new Forums_Form_GroupUpdate();

        $type = $group['type'];

        if ($type == "open") {
            $type = 0;
        }
        else if ($type == "closed") {
            $type = 1;
        }
        else if ($type == "hidden") {
            $type = 2;
        }
        else {
            $type = 0;
        }

        $formData = array('title' => $group['title'], 'type' => $type, 'description' => $group['description']);
        $form->populate($formData);

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $title = $form->getValue('title');

                $description = $form->getValue('description');
                $type = $form->getValue('type');

                if ($type == 0)
                    $type = "open";
                else if ($type == 1)
                    $type = "closed";
                else if ($type == 2)
                    $type = "hidden";
                else
                    $type = "open";

                try {
                    $this->groupHandle = new Application_Model_Mapper_Group();
                    $groupInfo = new Forums_Model_DbTable_GroupInfo();

                    $data = array('title' => $title, 'group_type' => $type, 'description' => $description);
                    $response = $this->groupHandle->update($this->_getParam('groupID'), $data);

                    if($response)
                        $this->view->response = "Group updated successfully.";
                } catch (Exception $exc) {
                    if($exc->getCode() == 403)
                        $this->view->error = "Sorry, you do not have sufficient permissions to update group information";
                    else
                        $this->view->error = "Error occured while updating group information. Please try again later";

                    $form->populate($formData);
                }
            }
        }
    }

    public function friendsAction() {
        $sessionHandle = new Application_Model_Session();
        $form = new Forums_Form_SearchPerson();
        
        $role = $sessionHandle->getSessionParameter('role');

        $this->view->role = $role;
        $this->view->groupID = $this->_getParam('groupID');
        $this->view->form = $form;
        static $people = array();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $name = $form->getValue('criteria');
         
                try {
                    $peopleHandle = new Application_Model_Mapper_People();
                    $peopleHandle->setSearchTerm($name);

                    $people = $peopleHandle->fetch();
                } catch (Exception $exc) {
                    $people = array();
                    $this->view->perror = "Sorry. An error was encountered while searching for user.";
                }
            }
        }

        $pag = Zend_Paginator::factory($people);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
        $pag->setItemCountPerPage(5);
        $pag->setCurrentPageNumber($this->_getParam('page'));

        $this->view->people = $pag;
            
        try {
            $peopleHandle = new Application_Model_Mapper_Person_Friends();
            $session = new Application_Model_Session();
            $session->startSession();

            $userID = $session->getUserId();

            $friends = $peopleHandle->fetch($userID);
        } catch (Exception $exc) {
            $this->view->ferror = "Sorry. An error was encountered while fetching friendlist.";
        }

        $paginator = Zend_Paginator::factory($friends);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
        $paginator->setItemCountPerPage(5);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
    }

    public function deleteAction() {
        $sessionHandle = new Application_Model_Session();
        $role = $sessionHandle->getSessionParameter('role');

        $this->view->role = $role;

        if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Yes') {
                $groupID = $this->getRequest()->getPost('groupID');
                $userID = $this->getRequest()->getPost('userID');

                try {
                    $personHandle = new Application_Model_Mapper_Person_GroupMembership();
                    if ($personHandle->delete($userID, $groupID)) {
                        $msg = "User has been deleted successfully";
                        $this->view->msg = $msg;
                        $this->view->done = true;
                        $this->_helper->redirector('members','index','forums',array('id'=>$groupID,'msg'=>$msg),null,true);
                    }
                    else {
                        $this->view->msg = "Could not delete the user. Please try again later";
                    }
                } catch (Exception $exc) {
                    if($exc->getCode() == 403)
                        $this->view->error = "You do not have sufficient permissions to perform this operation. Please contact the group admin.";
                    else
                        $this->view->error = "An error occured while trying to delete member. Please try again later.";
                }
            } else {
                $this->_helper->redirector('members','index','forums',array('id'=>$this->_getParam('groupID',null)));
            }
        } else {
            $this->view->groupID = $this->_getParam('groupID',null);
            $this->view->userID = $this->_getParam('userID',null);
            $this->view->userName = $this->_getParam('userName',null);
        }
    }

    public function deletegroupAction() {
        $sessionHandle = new Application_Model_Session();
        $role = $sessionHandle->getSessionParameter('role');

        $this->view->role = $role;

        if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Yes') {
                $groupID = $this->getRequest()->getPost('groupID');

                $this->groupHandle = new Application_Model_Mapper_Group();
                $groupInfo = new Forums_Model_DbTable_GroupInfo();
                $sessionHandle = new Application_Model_Session();
                
                try {
                    $members = $this->groupHandle->getMembers($groupID);
                } catch (Exception $exc) {
                    $this->view->error = "Could not fetch group information. Please try again later or contact your group admin to perform this action.";
                    return;
                }

                $this->groupHandle = new Application_Model_Mapper_Person_GroupMembership();
                foreach ($members as $member) {
                    try {
                        $deleted = $this->groupHandle->delete($member->getId(), $groupID);
                    } catch (Exception $exc) {
                        $this->view->error = "An error occured while deleting this group. Please try again later.";
                        return;
                    }
                }

                try {
                    $groupInfo->deleteRecord($groupID);
                } catch (Exception $exc) {
                    $this->view->error = $exc->getMessage();
                }

                $sessionHandle->setSessionParameter('msg', "{$this->_getParam('title','')} Group deleted successfully");

                $this->_helper->redirector('index','index','forums');
            } else
                $this->_helper->redirector('profile', 'index', 'forums',array('id'=>$this->_getParam('groupID',null)));
        } else {
            $this->view->groupID = $this->_getParam('groupID','');
            $this->view->title = $this->_getParam('title','');
        }
    }

    public function notifyAction()
    {
        $form = new Forums_Form_Message();
        $sessionHandle = new Application_Model_Session();
        $role = $sessionHandle->getSessionParameter('role');

        $this->view->role = $role;
        $this->view->form = $form;
        $this->view->done = false;
        $this->view->groupID = $this->_getParam('groupID',null);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $groupID = $this->_getParam('groupID');
                $message = $form->getValue('message');

                try {
                    $this->groupHandle = new Application_Model_Mapper_Group();
                    $members = $this->groupHandle->getMembers($groupID);
                } catch (Exception $exc) {
                    $this->view->error = "Sorry, could not retrieve group members. Please try again later.";
                    return;
                }

                try {
                    $contacts = array();
                    foreach ($members as $member) {
                        $contacts[] = $member->getPhoneNumber();
                    }

                    $smsHandle = new Application_Model_Sms_Mapper();
                    if($smsHandle->create($contacts, $message)) {
                        $this->view->done = true;
                        $this->view->response = "Message has been sent successfully to group member(s)";
                    }
                    else {
                        $this->view->done = false;
                        $this->view->response = "An error occured while sending group SMS";
                    }
                } catch (Exception $exc) {
                    $this->view->done = false;
                    $this->view->error = "Sorry, could not send group SMS.";
                }
            } else {
                $this->view->done = false;
                $form->populate($formData);
            }
        }
    }

    public function adminAction()
    {
        $groupID = $this->_getParam('groupID');
        $userID = $this->_getParam('userID');

        try {
            $personHandle = new Application_Model_Mapper_Person_GroupMembership();

            if($personHandle->update(null, 'true', $userID, $groupID))
                $this->view->response = "The user has now been elevated to group administrator";
            else
                $this->view->response = "Could not change role to group administrator";
        } catch (Exception $exc) {
            if($exc->getCode() == 403) {
                $this->view->error = "Sorry, you do not have sufficient permissions to perform this action. Please contact your group admin.";
            } else {
                $this->view->response = $exc->getMessage();
            }
        }
    }

    public function userAction()
    {
        $groupID = $this->_getParam('groupID');
        $userID = $this->_getParam('userID');

        try {
            $personHandle = new Application_Model_Mapper_Person_GroupMembership();

            if($personHandle->update(null, 'false', $userID, $groupID))
                $this->view->response = "Role changed successfully";
            else
                $this->view->response = "Could not change role to user";
        } catch (Exception $exc) {
            if($exc->getCode() == 403) {
                $this->view->error = "Sorry, you do not have sufficient permissions to perform this action. Please contact your group admin.";
            } else {
                $this->view->response = $exc->getMessage();
            }
        }
    }

    public function acceptRequestAction()
    {
        $userID = $this->_getParam('userID');
        $groupID = $this->_getParam('groupID');

        $this->view->groupID = $groupID;
        $this->view->userID = $userID;

        $groupHandle = new Application_Model_Mapper_Person_GroupMembership();
        $sessionHandle = new Application_Model_Session();

        try {
            if ($groupHandle->accept($userID, $groupID)) {
                $this->view->response = "Membership request has been accepted.";
                $sessionHandle->setSessionParameter('rqstMsg', 'Membership request has been accepted.');
                $this->_helper->redirector('requests','index','forums',array('id'=>$groupID),null,true);
            } else {
                $this->view->response = "An error occured while accepting the membership request. Please try again later.";
                $sessionHandle->setSessionParameter('rqstMsg', 'An error occured while accepting the membership request. Please try again later.');
                $this->_helper->redirector('requests','index','forums',array('id'=>$groupID),null,true);
            }
        } catch (Exception $exc) {
            $this->view->error =  "Could not accept the membership request";
            $sessionHandle->setSessionParameter('rqstErr', 'Could not accept the membership request');
            $this->_helper->redirector('requests','index','forums',array('id'=>$groupID),null,true);
        }
    }

    public function ignoreRequestAction()
    {
        
        $userID = $this->_getParam('userID');
        $groupID = $this->_getParam('groupID');

        $this->view->groupID = $groupID;
        $this->view->userID = $userID;
        
        $groupHandle = new Application_Model_Mapper_Person_GroupMembership();
        $sessionHandle = new Application_Model_Session();
        
        try {
            if ($groupHandle->decline($userID, $groupID)) {
                $this->view->response = "Membership request has been ignored.";
                $sessionHandle->setSessionParameter('rqstMsg', 'Membership request has been ignored.');
                $this->_helper->redirector('requests','index','forums',array('id'=>$groupID),null,true);
            } else {
                $this->view->response = "An error occured while attempting to decline the membership request. Please try again later.";
                 $sessionHandle->setSessionParameter('rqstMsg', 'An error occured while attempting to decline the membership request. Please try again later.');
                 $this->_helper->redirector('requests','index','forums',array('id'=>$groupID),null,true);
            }
        } catch (Exception $exc) {
            $this->view->error =  "Could not ignore the membership request";
            $sessionHandle->setSessionParameter('rqstErr', 'Could not ignore the membership request');
            $this->_helper->redirector('requests','index','forums',array('id'=>$groupID),null,true);
        }
    }

    public function availableGroupsAction()
    {
        try {
            $this->groupHandle = new Application_Model_Mapper_Publicgroups_Mapper();

            $groups = $this->groupHandle->fetch(null, null, null, "updated_at", "ascending");

            $paginator = Zend_Paginator::factory($groups);
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
            $paginator->setItemCountPerPage(5);
            $paginator->setCurrentPageNumber($this->_getParam('page'));
            $this->sessionHandle->setSessionParameter('public',1);
            $this->view->paginator = $paginator;
        } catch (Exception $exc) {
            $this->view->error = $exc->getMessage();
        }
    }

    public function myinvitesAction() {
        $sessionHandle = new Application_Model_Session();
        $sessionHandle->startSession();
        $invites = $sessionHandle->getSessionParameter('invites'); 
        
        $this->view->invites = $invites;
        $this->view->userID = $sessionHandle->getUserId();
    }
}