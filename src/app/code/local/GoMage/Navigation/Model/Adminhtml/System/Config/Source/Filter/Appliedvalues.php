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
	
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Appliedvalues{

	const YES = 1;
	const NO = 0;
	const REMOVE = 2;
	
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
            array('value' => self::YES, 'label'=>$helper->__('Yes')),
            array('value' => self::NO, 'label'=>$helper->__('No')),
            array('value' => self::REMOVE, 'label'=>$helper->__('Yes, remove value from list'))
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
            self::YES => $helper->__('Yes'),
            self::REMOVE => $helper->__('Yes, remove value from list'),        	
        );
    }

}