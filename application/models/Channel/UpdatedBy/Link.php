<?php
/**
 * This is a model of asi/channel/updated_by/link
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */

class Application_Model_Channel_UpdatedBy_Link extends Application_Model_Abstract {

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
