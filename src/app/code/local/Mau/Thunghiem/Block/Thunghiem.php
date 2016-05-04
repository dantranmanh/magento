<?php
class Mau_Thunghiem_Block_Thunghiem extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getThunghiem()     
     { 
        if (!$this->hasData('thunghiem')) {
            $this->setData('thunghiem', Mage::registry('thunghiem'));
        }
        return $this->getData('thunghiem');
        
    }
}