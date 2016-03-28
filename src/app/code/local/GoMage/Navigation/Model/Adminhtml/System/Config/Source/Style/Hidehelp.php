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
	
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Style_Hidehelp{
    
	const MOUSE_OUT = 0;
	const CLOSE_BUTTON = 1;
		
    public function toOptionArray(){
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
            array('value'=>self::MOUSE_OUT, 'label' => $helper->__('Mouse out')),
        	array('value'=>self::CLOSE_BUTTON, 'label' => $helper->__('Close Button'))
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
            self::MOUSE_OUT => $helper->__('Mouse out'),
            self::CLOSE_BUTTON => $helper->__('Close Button')                 	
        );
    }

}