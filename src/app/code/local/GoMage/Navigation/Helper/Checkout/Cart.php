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
 * @since        Class available since Release 3.1
 */
 
class GoMage_Navigation_Helper_Checkout_Cart extends Mage_Checkout_Helper_Cart
{
    /**
     * Retrieve current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {          	  
        $url = parent::getCurrentUrl();
		
        if (Mage::helper('gomage_navigation')->isGomageNavigationAjax()){
        	$url = Mage::helper('gomage_navigation/url')->removeRequestParam($url, 'ajax');
        }
		
        return $url;
    }
}
