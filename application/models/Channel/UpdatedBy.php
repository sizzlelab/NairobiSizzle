<?php

/**
 * This is a model of asi/channel/updated by
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */

class Application_Model_Channel_UpdatedBy extends Application_Model_Abstract {

    protected $name = '';
    protected $link = null;

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getname() {
        return $this->name;
    }

    public function setLink(array $link=null) {
        $this->link = new Application_Model_Channel_UpdatedBy_Link($link);
    }

    public function getLink() {
        return $this->link;
    }

}

?>
