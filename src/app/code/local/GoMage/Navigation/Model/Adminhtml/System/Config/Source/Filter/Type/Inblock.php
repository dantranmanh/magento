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
 * @since        Class available since Release 1.0
 */
	
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Type_Inblock{

	const TYPE_FIXED = 1;
	const TYPE_AUTO = 2;
	
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
            array('value'=>self::TYPE_FIXED, 'label' => $helper->__('Fixed')),
            array('value'=>self::TYPE_AUTO, 'label' => $helper->__('Auto'))
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
            self::TYPE_FIXED	=> $helper->__('Fixed'),
            self::TYPE_AUTO	=> $helper->__('Auto'),        	
        );
    }

}