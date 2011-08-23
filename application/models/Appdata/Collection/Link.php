<?php
/**
 * This is a model of asi/appdata/collection/link
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */

class Application_Model_Appdata_Collection_Link extends Application_Model_Abstract {

    protected $href = '';
    protected $rel = '';

    public function setHref($href) {
        $this->href = $href;
        return $this;
    }

    public function getHref() {
        return $this->href;
    }

    public function setRel($rel) {
        $this->rel = $rel;
        return $this;
    }

    public function getRel() {
        return $this->rel;
    }

}

?>
