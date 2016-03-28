<?php

class Magestore_Giftvoucher_Block_Adminhtml_Field_Comment extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $comment = Mage::helper('giftvoucher')->__('Only available when enabling');
        $comment .= ' <a href="' . $this->getUrl('adminhtml/system_config/edit', array('section' => 'customer'))
            . '" target="_blank">' . Mage::helper('giftvoucher')->__('customer store credit functionality') . '</a>';
        $element->setComment($comment);
        return parent::render($element);
    }
}
