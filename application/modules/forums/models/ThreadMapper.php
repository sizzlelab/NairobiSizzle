<?php
/**
 * This class maps all values obtained from a form to the format that is expected by the ASI
 * platform. It also does a higher level of validation to ensure correctness of data.
 * to map a user input to a thread object.
 * @author swift kelvin
 * @copyright 2010, Kelvin Nkinyili
 * @category NairobiSizzle
 * @package Forums
 * @subpackage models
 */
class Forums_Model_ThreadMapper
{
    /*stores the thread title that will be viewed by other people
     * to enable them to comment on this post
     * @var string
     */
    protected $_title = null;
    /*stores the thread message that people can look at and make comments
     * Its the main part of a thread
     * @var string title
     */
    protected $_thread = null;
    /* stores the value of the users chosen privacy settings
     * @var array thread
     */
    protected $_privacy= null;
    /*stores the array passed by a user
     * @var array privacy
     */
     protected $_tags= null;
    /*stores the array passed by a user
     * @var string tag
     */
     protected $_owner_id= 'assOvWMver34QkcaaNYdUc';
    /*stores the array passed by a user
     * @var string owner
     */
    private $_usercontent= null;
    /*
     * stores an answer that a user has updated to an existing post
     * @var string answer
     */
    //protected  $_answer = null;
    public function __construct($values=null)
    {
        $this->_usercontent = $values;
       if(is_array($this->_usercontent)){
			$this->setOptions($this->_usercontent = $values);
		}

    }
    public function validate()
    {
        //$val = null;
        //This method shall come in handy
       //  var_dump($this->_usercontent);
         return true;

    }
    public function checkPublicity()
    {
        //This returns a boolean true if the user has indicated that the message be public
        $set = false;
        if(isset ($this->_privacy))
            {
            $set=true;
            }
            return $set;
    }
    public function setThread($thread)
    {
        $this->_thread=$thread;
    }
    public function setPrivacy($privacy)
    {
        $this->_privacy=$privacy[0];
    }
    public function setTitle($title)
    {
        $this->_title=$title;
    }
    public function getTitle()
    {
        return $this->_title;
    }
    public function getThread()
    {
        return $this->_thread;
    }
    public function getPrivacy()
    {

            return true;
    }
    public function getTags()
    {
        return $this->_tags;
    }
    public function getOwner()
    {
        return $this->_owner_id;
    }
    public function setOptions(array $options)

    {

        $methods = get_class_methods($this);

        foreach ($options as $key => $value) {

            $method = 'set' . ucfirst($key);

            if (in_array($method, $methods)) {
                $this->$method($value);

            }

        }

        return $this;
      }
      
}

