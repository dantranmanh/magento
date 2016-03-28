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
	
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Backtotopaction{

	const BACK_PAGE = 0;
	const BACK_PRODUCTS = 1;
	
    public function toOptionArray(){
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
            array('value'=>self::BACK_PAGE, 'label' => $helper->__('Back to top of page')),
        	array('value'=>self::BACK_PRODUCTS, 'label' => $helper->__('Back to top of products'))
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
            self::BACK_PAGE => $helper->__('Back to top of page'),
            self::BACK_PRODUCTS => $helper->__('Back to top of products')
        );
    }

}