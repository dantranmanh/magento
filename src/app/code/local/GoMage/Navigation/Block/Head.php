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
 * @since        Class available since Release 2.3
 */
class GoMage_Navigation_Block_Head extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $helper = Mage::helper('gomage_navigation');

        if ($helper->isGomageNavigation() || $helper->isGomageNavigationMenu()) {
            if ($head_block = $this->getLayout()->getBlock('head')) {
                $styles_block = $this->getLayout()->createBlock('gomage_navigation/styles', 'advancednavigation_styles')->setTemplate('gomage/navigation/header/styles.phtml');
                $head_block->addjs('varien/menu.js');
                $head_block->addjs('gomage/navigation/effects.js');
                $head_block->addjs('gomage/advanced-navigation.js');
                $head_block->setChild('advancednavigation_styles', $styles_block);
                $head_block->addCss('css/gomage/advanced-navigation.css');
                if (Mage::getStoreConfig('configswatches/general/product_list_attribute')) {
                    $head_block->addItem('skin_js', 'js/configurableswatches/product-media.js');
                    $head_block->addItem('skin_js', 'js/configurableswatches/swatches-list.js');
                }
            }
        }
    }
}