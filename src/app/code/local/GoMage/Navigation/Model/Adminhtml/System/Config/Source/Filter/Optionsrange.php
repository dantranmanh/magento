<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 4.9
 * @since        Class available since Release 4.0
 */
	
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange{

	const NO = 0;
	const AUTO = 1;
	const MANUALLY = 2;
	
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
        	array('value' => self::NO, 'label'=>$helper->__('No')),
            array('value' => self::AUTO, 'label'=>$helper->__('Auto')),            
            array('value' => self::MANUALLY, 'label'=>$helper->__('Manually'))
        );
    }
    
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionHash(){
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
            self::NO => $helper->__('No'),
            self::AUTO => $helper->__('Auto'),
            self::MANUALLY => $helper->__('Manually'),        	
        );
    }

}