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
	
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Style_Showhelp{
    
	const MOUSE_OVER = 0;
	const CLICK = 1;
		
    public function toOptionArray(){
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
            array('value'=>self::MOUSE_OVER, 'label' => $helper->__('Mouse over')),
        	array('value'=>self::CLICK, 'label' => $helper->__('Click'))
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
            self::MOUSE_OVER => $helper->__('Mouse over'),
            self::CLICK => $helper->__('Click')                 	
        );
    }

}