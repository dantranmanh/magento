<?php

class Mau_Thunghiem_Block_Adminhtml_Thunghiem_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('thunghiem_form', array('legend'=>Mage::helper('thunghiem')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('thunghiem')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('thunghiem')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('thunghiem')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('thunghiem')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('thunghiem')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('thunghiem')->__('Content'),
          'title'     => Mage::helper('thunghiem')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getThunghiemData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getThunghiemData());
          Mage::getSingleton('adminhtml/session')->setThunghiemData(null);
      } elseif ( Mage::registry('thunghiem_data') ) {
          $form->setValues(Mage::registry('thunghiem_data')->getData());
      }
      return parent::_prepareForm();
  }
}