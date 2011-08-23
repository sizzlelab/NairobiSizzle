<?php
/**
 * Implements the Zend pagination as opposed to using ASI's pagination
 * @author Eric Mutunga <rcngei@gmail.com>
 * @copyright NairobiSizzle, 2010
 *
 */
class Application_Model_ZendPagination
{
	protected $paginator = null;
	protected $itemCount = null;
		
	/**
	 * Paginate a result array using Zend_Paginator
	 * NB: Copy the file ../application/views/scripts/paginator to your_module/views/scripts
	 * @todo find a way use only one copy of paginator.phmtl throughout the site
	 * An example call to this function in your controller would be
	 * <code>
	 * 		// note $array -- array to paginate
	 * 		$paginator = new Application_Model_ZendPagination();
	 * 		$paginator->setItemCount(10);
	 * 		//use this to set the number of items to display per page
	 * 		//otherwise items per page defaults to 5			
	 * 		$this->view->results = $paginator->paginate($array, $this->_getParam('page'));
	 * </code>
	 * to display page numbers add this after looping through the array in your view for example
	 * <code>
	 * 		echo $this->results;
	 * </code>
	 * @param array $array
	 * @param int $page : use $this->_getParam('page')
	 * @return array 
	 */
	public function paginate($array, $page){
		$this->paginator = Zend_Paginator::factory($array);
		$this->paginator->setItemCountPerPage($this->getItemCount());
		$this->paginator->setCurrentPageNumber($page);
		
		//$this->view->paginator used to loop thru in the view
		Zend_Paginator::setDefaultScrollingStyle('Elastic');
		//Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
		return $this->paginator;
	}
	
	/**
	 * Returns the itemcount if set otherwise it sets this to 5
	 */
	protected function getItemCount(){
		if(!empty($this->itemCount)){
			return $this->itemCount;
		}else{
			return 5;
		}
	}
	
	/**
	 * Sets the item count to {$itemCount}, use this to change item count from the default 5
	 * @param int $itemCount
	 * @return boolean
	 */
	public function setItemCount($itemCount){
		if(!empty($itemCount)){
			$this->itemCount = $itemCount;
			$this->paginator->setItemCountPerPage = $this->itemCount;
			return true;
		}else{
			return false;
		}
	}

}

