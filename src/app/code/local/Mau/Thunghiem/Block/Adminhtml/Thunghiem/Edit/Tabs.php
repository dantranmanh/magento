<?php

class Mau_Thunghiem_Block_Adminhtml_Thunghiem_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('thunghiem_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('thunghiem')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('thunghiem')->__('Item Information'),
          'title'     => Mage::helper('thunghiem')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('thunghiem/adminhtml_thunghiem_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}