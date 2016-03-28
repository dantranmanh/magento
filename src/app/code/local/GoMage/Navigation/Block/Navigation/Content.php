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

class GoMage_Navigation_Block_Navigation_Content extends GoMage_Navigation_Block_Navigation_Abstract {
	
	const NAVIGATION_PLACE	= self::CONTENT_COLUMN;//replace to admin html const
	const CONFIG_KEY		= 'gomage_navigation/contentcolumnsettings';
	
	public function canDisplay() {
		if ($this->can_display === null) {
			$shop_by = Mage::getStoreConfig('gomage_navigation/general/show_shopby');
			
			$this->can_display = (bool) (
				$this->isActive() &&
				(!$this->showInShopBy()) &&
				(
					$shop_by == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::CONTENT				|| 
					$shop_by == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::LEFT_COLUMN_CONTENT	|| 
					$shop_by == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::RIGHT_COLUMN_CONTENT
				)
			);
		}
		
        return $this->can_display;
    }
	
	protected function _prePrepareLayout() {
		if ($this->isGMN() && $this->canDisplay()) {		
			if (in_array(Mage::app()->getFrontController()->getRequest()->getControllerName(), array('category', 'result'))) {		
				$this->setTemplate('gomage/navigation/catalog/navigation/content.phtml')
					->unsetData('cache_lifetime')
					->unsetData('cache_tags');
			}     
        } else if ($content = $this->getLayout()->getBlock('content')) {
			$content->unsetChild('gomage.navigation.content');
		}
	}
}