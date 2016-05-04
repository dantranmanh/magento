<?php

class Mau_Thunghiem_Block_Adminhtml_Thunghiem_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'thunghiem';
        $this->_controller = 'adminhtml_thunghiem';
        
        $this->_updateButton('save', 'label', Mage::helper('thunghiem')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('thunghiem')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('thunghiem_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'thunghiem_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'thunghiem_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('thunghiem_data') && Mage::registry('thunghiem_data')->getId() ) {
            return Mage::helper('thunghiem')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('thunghiem_data')->getTitle()));
        } else {
            return Mage::helper('thunghiem')->__('Add Item');
        }
    }
}