<?php


/**
 * This is a model of asi/channel/subscription
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */

class Application_Model_Channel_Subscription extends Application_Model_Abstract {

    protected $groupSubscribers = array();
    protected $userSubscribers = array();

    public function setGroupSubscribers(array $groupSubscribers) {
        $count = 0;
        foreach ($groupSubscribers as $group) {
            $this->groupSubscribers[$count] = new Application_Model_Group($group);
            $count++;
        }
        return $this;
    }

    public function getGroupSubscribers() {
        return $this->groupSubscribers;
    }

    public function setUserSubscribers(array $userSubscribers) {
        $count = 0;
        foreach ($userSubscribers as $user) {
            $this->userSubscribers[$count] = new Application_Model_Person($user);
            $count++;
        }
        return $this;
    }

    public function getUserSubscribers() {
        return $this->userSubscribers;
    }

}

?>
