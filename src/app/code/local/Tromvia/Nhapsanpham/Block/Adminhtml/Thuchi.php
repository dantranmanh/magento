<?php
class Tromvia_Nhapsanpham_Block_Adminhtml_Thuchi extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_thuchi';
    $this->_blockGroup = 'thuchi';
    $this->_headerText = Mage::helper('nhapsanpham')->__('Quản lý thu chi');
    $this->_addButtonLabel = Mage::helper('nhapsanpham')->__('Thêm khoản thu chi');
    parent::__construct();
  }
}