<?php

class Campuswisdom_Model_Exps {
    protected $_Category = Null;
    protected $_Name = Null;
    protected $_ExpId = Null;
    protected $_Views = null;
    protected $_Id = null;
    protected $_Comment = null;
    public function __construct(array $options = null) {
        if (is_array($options)) {
            $this->setOptions($options);        }
    }
    public function __set($name, $value) {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid Experiences property');
        }
        $this->$method($value);
    }
    public function __get($name) {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid experiences property');
        }
        return $this->$method();
    }
    public function setOptions(array $options) {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    public function setCategory($category) {
        $this->_Category = $category;
        return $this;    }

    public function getCategory() {
        return $this->_Category;
    }

    public function setName($name) {
        $this->_Name = $name;
        return $this;
    }

    public function getName() {
        $this->_Name;
    }

    public function setViews($views) {
        $this->_Views = $views;
        return $this;
    }

    public function getViews() {
        $this->_Views;
    }

    public function setId($id) {
        $this->_ExpId = $id;
        return $this;
    }

    public function getId() {
        $this->_ExpId;
    }

    public function setIdc($id) {
        $this->_Id = $id;
        return $this;
    }

    public function getIdc() {
        $this->_Id;
    }

    public function setComment($comment) {
        $this->_Comment = $comment;
        return $this;
    }

    public function getComment() {
        $this->_Comment;
    }

}

