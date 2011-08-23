<?php
/**
 * This is a model of asi/appdata/self
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */


class Application_Model_Appdata_Self extends Application_Model_Abstract {

    protected $anyKey = '';
    protected $anyOtherKey = '';

    public function setAnyKey($anyKey) {
        $this->anyKey = $anyKey;
        return $this;
    }

    public function getAnyKey() {
        return $this->anyKey;
    }

    public function setAnyOtherKey($anyOtherKey) {
        $this->anyOtherKey = $anyOtherKey;
        return $this;
    }

    public function getAnyOtherKey() {
        return $this->anyOtherKey;
    }

}

?>
