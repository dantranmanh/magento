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
class GoMage_Navigation_Block_Navigation_CMS_Left extends GoMage_Navigation_Block_Navigation_Left
{

    public function canDisplay()
    {
        if ($this->can_display === null) {
            $navigation = intval(Mage::helper('gomage_navigation/config')->getCMSPage()->getData('navigation'));

            $this->can_display = in_array($navigation,
                    array(GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::LEFT_COLUMN_CONTENT,
                        GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::LEFT_COLUMN)
                ) && (!$this->showInShopBy());
        }
        return $this->can_display;
    }

    protected function _prePrepareLayout()
    {
        if (
            $this->isGMN() &&
            $this->canDisplay() &&
            $this->isCMSPage()
        ) {
            $this->setTemplate('gomage/navigation/catalog/navigation/left.phtml')
                ->unsetData('cache_lifetime')
                ->unsetData('cache_tags');
        } else {
            if ($content = $this->getLayout()->getBlock('content')) {
                $content->unsetChild('gomage.navigation.cms.left');
            }
        }
    }
}