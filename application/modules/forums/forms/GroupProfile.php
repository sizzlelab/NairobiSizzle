<?php

class Forums_Form_GroupProfile extends Zend_Form
{
    private $groupHandle = null;
    private $sessionHandle = null;
    private $groupsID;
    private $groupsTitle;

    public function init()
    {
        $this->sessionHandle = new Application_Model_Session();
        $this->groupHandle = new Application_Model_Mapper_Publicgroups_Mapper();
        $this->sessionHandle->startSession();
        $this->groupsID = array();
        $this->groupsTitle = array();

        $this->display();
    }

    public function getGroupID($id) {
        return $this->groupsID[$id];
    }

    private function getGroups() {
        try {
            $allGroups = $this->groupHandle->fetch();

            foreach ($allGroups as $group) {
                $this->groupsTitle[] = $group->getTitle();
                $this->groupsID[] = $group->getID();
            }

            return $this->groupsTitle;
        } catch (Application_Model_Exception $exc) {
            echo $exc->getMessage();
        }
    }

    private function display() {
        $this->setName('Group Profile');

        $group = new Zend_Form_Element_Select('group');
        $group->addMultiOptions($this->getGroups());
        $group->setLabel('Groups: ')
                ->setRequired(true)
                ->addFilter('StripTags');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel("View Profile");
        $submit->setAttrib('id', 'submitbutton');
        
        $this->addElements(array($group, $submit));
    }
}

