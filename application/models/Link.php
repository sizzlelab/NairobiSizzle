<?php
/**
 * Stores data pertaining to a link.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Link extends Application_Model_Abstract {
    /**
     * The link's href attribute.
     * 
     * @var string
     */
    protected $href = '';

    /**
     * The link's rel attribute.
     *
     * @var string
     */
    protected $rel  = '';

    /**
     * Set a link's href.
     * 
     * @param string $href
     *
     * @return Application_Model_Link
     */
    public function setHref($href) {
        $this->href = (string) $href;
        return $this;
    }

    /**
     * Get a link's href.
     *
     * @see Application_Model_Link::setHref()
     *
     * @return string
     */
    public function getHref() {
        return $this->href;
    }

    /**
     * Set a link's rel.
     * 
     * @param string $rel
     * 
     * @return Application_Model_Link
     */
    public function setRel($rel) {
        $this->rel = (string) $rel;
        return $this;
    }

    /**
     * Get a link's rel.
     *
     * @see Application_Model_Link::setRel()
     * 
     * @return string
     */
    public function getRel() {
        return $this->rel;
    }
}
