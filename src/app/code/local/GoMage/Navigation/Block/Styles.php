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
 * @since        Class available since Release 3.0
 */
class GoMage_Navigation_Block_Styles extends Mage_Core_Block_Template
{

    public function getStoreCategories()
    {
        $root_category = Mage::app()->getStore()->getRootCategoryId();
        $tree          = Mage::getResourceModel('catalog/category_tree');
        $nodes         = $tree->loadNode($root_category)
            ->loadChildren(1)
            ->getChildren();

        $collection = Mage::getResourceModel('catalog/category_collection');
        $collection->addAttributeToSelect('*');
        $tree->addCollectionData($collection, Mage::app()->getStore()->getId(), $root_category, true, true);

        return $nodes;
    }

    public function getRootCategory()
    {
        $root_category_id = Mage::app()->getStore()->getRootCategoryId();

        return Mage::getModel('catalog/category')->load($root_category_id);
    }

    public function getNavigationCatigoryUrl()
    {
        if (Mage::helper('gomage_navigation/config')->isCMSPage() &&
            ((int)Mage::helper('gomage_navigation/config')->getCMSPage()->getData('navigation_category_id') !== 0)
        ) {
            return Mage::helper('gomage_navigation/url')->categoryUrl(
                Mage::helper('gomage_navigation/config')->curentCategory(),
                array('_query' => array('ajax' => 1))
            );
        }

        return false;
    }

    public function getGanPageId()
    {
        return intval(Mage::helper('gomage_navigation/config')->getCMSPage()->getData('page_id'));
    }

}