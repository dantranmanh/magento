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
	
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation{

	const USE_GLOBAL = 0;
	const LEFT_BLOCK = 1;
	const CONTENT = 2;
	const RIGHT_BLOCK = 3;
	
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
        	array('value' => self::USE_GLOBAL, 'label'=>$helper->__('Use Global')),
            array('value' => self::LEFT_BLOCK, 'label'=>$helper->__('Left Block')),            
            array('value' => self::CONTENT, 'label'=>$helper->__('Content')),
            array('value' => self::RIGHT_BLOCK, 'label'=>$helper->__('Right Block'))
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
            self::USE_GLOBAL => $helper->__('Use Global'),
            self::LEFT_BLOCK => $helper->__('Left Block'),
            self::CONTENT => $helper->__('Content'),
            self::RIGHT_BLOCK => $helper->__('Right Block'),        	
        );
    }

}