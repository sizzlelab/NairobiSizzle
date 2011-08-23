<?php
/**
 * This class maps all values obtained from a form to the format that is expected by the ASI
 * platform. It also does a higher level of validation to ensure correctness of data.
 * to create an answer to a thread.
 * @author swift kelvin
 * @copyright 2010, Kelvin Nkinyili
 * @category NairobiSizzle
 * @package Forums
 * @subpackage models
 */
class Forums_Model_Answer
{
    
    protected $_thread = null;
     /*This value is the actual answer provided by the user
      *
      * It is necessary for this value to be long enough to be long enough
      *
      * to allow for long responses
      * 
     * @var array $privacy
     */
    
    public function setThread($thread)
    {
        $this->_thread=$thread;
    }
    

}

