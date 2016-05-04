<?php
class Mau_Thunghiem_Block_Adminhtml_Thunghiem extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_thunghiem';
    $this->_blockGroup = 'thunghiem';
    $this->_headerText = Mage::helper('thunghiem')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('thunghiem')->__('Add Item');
    parent::__construct();
  }
}