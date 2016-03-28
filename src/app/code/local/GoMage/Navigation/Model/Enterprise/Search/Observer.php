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
 * @since        Class available since Release 4.1
 */
class GoMage_Navigation_Model_Enterprise_Search_Observer extends Enterprise_Search_Model_Observer
{
	protected $GMN	= null;
	
	public function isGMN() 
	{
		if ($this->GMN === null) {
			$this->GMN = Mage::helper('gomage_navigation')->isGomageNavigation();
		} 
        
		return $this->GMN;
    }
	
	 /**
     * Reset search engine if it is enabled for catalog navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentCatalogLayer(Varien_Event_Observer $observer)
    {
		if (!$this->isGMN()) { return parent::resetCurrentCatalogLayer($observer); }
		
        if (Mage::helper('enterprise_search')->getIsEngineAvailableForNavigation()) {
            Mage::register('current_layer', Mage::getSingleton('gomage_navigation/enterprise_search_catalog_layer'));
        } else {
			Mage::register('current_layer', Mage::getSingleton('gomage_navigation/catalog_layer'));
		}
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentSearchLayer(Varien_Event_Observer $observer)
    {
		if (!$this->isGMN()) { return parent::resetCurrentSearchLayer($observer); }
		
        if (Mage::helper('enterprise_search')->getIsEngineAvailableForNavigation(false)) {
            Mage::register('current_layer', Mage::getSingleton('gomage_navigation/enterprise_search_search_layer'));
        } else {
			Mage::register('current_layer', Mage::getSingleton('gomage_navigation/catalogsearch_layer'));
		}
    }
}
