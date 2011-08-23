<?php
class Yearbook_Model_Pagination extends Application_Model_Abstract {
    protected $page    = 0;
    protected $perPage = 0;
    protected $size    = 0;

    public function setPage($page) {
        $this->page = (int) $page;
        return $this;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPerPage($perPage) {
        $this->perPage = (int) $perPage;
        return $this;
    }

    public function getPerPage() {
        return $this->perPage;
    }

    public function setSize($size) {
        $this->size = (int) $size;
        return $this;
    }

    public function getSize() {
        return $this->size;
    }
}