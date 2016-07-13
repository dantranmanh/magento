<?php
class Tromvia_Nhapsanpham_Adminhtml_ThuchiController
	extends Mage_Adminhtml_Controller_Action
{
 	protected function _initAction()
    {
    	//set active menu, title
        $this->_title($this->__('Thu chi'))
             ->loadLayout()
             ->_setActiveMenu('tromvia/nhapsanpham_thuchi');

        return $this;
    }
	
	public function indexAction()
	{ 
		$this->_initAction()
             ->_title($this->__('Thu chi'))
             ->_addBreadcrumb($this->__('Thu chi'), $this->__('Thu chi'));
		$this->renderLayout();
	}
}