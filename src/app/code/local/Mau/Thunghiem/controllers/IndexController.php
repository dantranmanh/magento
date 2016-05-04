<?php
class Mau_Thunghiem_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/thunghiem?id=15 
    	 *  or
    	 * http://site.com/thunghiem/id/15 	
    	 */
    	/* 
		$thunghiem_id = $this->getRequest()->getParam('id');

  		if($thunghiem_id != null && $thunghiem_id != '')	{
			$thunghiem = Mage::getModel('thunghiem/thunghiem')->load($thunghiem_id)->getData();
		} else {
			$thunghiem = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($thunghiem == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$thunghiemTable = $resource->getTableName('thunghiem');
			
			$select = $read->select()
			   ->from($thunghiemTable,array('thunghiem_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$thunghiem = $read->fetchRow($select);
		}
		Mage::register('thunghiem', $thunghiem);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}