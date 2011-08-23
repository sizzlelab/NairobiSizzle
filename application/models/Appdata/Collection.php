<?php
/**
 * This is a model of asi/appdata/collection
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */


class Application_Model_Appdata_Collection extends Application_Model_Abstract {

    protected $updatedBy = '';
    protected $title = '';
    protected $link = null;
    protected $indestructible = '';
    protected $metadata = '';
    protected $updatedByName = '';
    protected $updatedAt = '';
    protected $totalResults = '';
    protected $tags = '';
    protected $owner = '';
    protected $readOnly = '';
    protected $priv = '';
    protected $id = '';
    protected $entry = null;
    protected $startIndex = '';
    protected $itemsPerPage = '';

    public function setItemsPerPage($itemsPerPage) {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }

    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }

    public function setEntry(array $entry) {
        $this->entry = array();
        foreach ($entry as $val) {
            $this->entry[] = new Application_Model_Appdata_Collection_Entry($val);
        }
        return $this;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setStartIndex($startIndex) {
        $this->startIndex = $startIndex;
        return $this;
    }

    public function getStartIndex() {
        return $this->startIndex;
    }

    public function setUpdatedBy($updatedBy) {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function getUpdatedBy() {
        return $this->updatedBy;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setLink(array $link) {
        $this->link = new Application_Model_Appdata_Collection_Link($link);
        return $this;
    }

    public function getLink() {
        return $this->link;
    }

    public function setIndestructible($indestructible) {
        $this->indestructible = $indestructible;
        return $this;
    }

    public function getIndestructible() {
        return $this->indestructible;
    }

    public function setMetadata($metadata) {
        $this->metadata = $metadata;
        return $this;
    }

    public function getMetadata() {
        return $this->metadata;
    }

    public function setUpdatedByName($updatedByName) {
        $this->updatedByName = $updatedByName;
        return $this;
    }

    public function getUpdatedByName() {
        return $this->updatedByName;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setTotalResults($totalResults) {
        $this->totalResults = $totalResults;
        return $this;
    }

    public function getTotalResults() {
        return $this->totalResults;
    }

    public function setTags($tags) {
        $this->tags = $tags;
        return $this;
    }

    public function getTags() {
        $this->tags;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
        return $this;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setReadOnly($readOnly) {
        $this->readOnly = $readOnly;
        return $this;
    }

    public function getReadOnly() {
        return $this->readOnly;
    }

    public function setPriv($priv) {
        $this->priv = $priv;
        return $this;
    }

    public function getPriv() {
        return $this->priv;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

}

?>
