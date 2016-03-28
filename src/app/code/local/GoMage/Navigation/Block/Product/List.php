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
class GoMage_Navigation_Block_Product_List extends Mage_Catalog_Block_Product_List
{

    protected $_procartproductlist = null;

    public function getAddToCompareUrl($product)
    {
        return $this->helper('gomage_navigation/compare')->getAddUrl($product);
    }

    public function getAddToCartUrl($product, $additional = array())
    {
        $_modules      = Mage::getConfig()->getNode('modules')->children();
        $_modulesArray = (array)$_modules;
        if (isset($_modulesArray['GoMage_Procart']) && $_modulesArray['GoMage_Procart']->is('active')) {
            if (Mage::helper('gomage_procart')->isProCartEnable() && Mage::getStoreConfig('gomage_procart/qty_settings/category_page')) {
                $additional['_query']['gpc_prod_id'] = $product->getId();
            }
        }
        return parent::getAddToCartUrl($product, $additional);
    }

    public function getProcartProductList()
    {
        $_modules      = Mage::getConfig()->getNode('modules')->children();
        $_modulesArray = (array)$_modules;
        if (isset($_modulesArray['GoMage_Procart']) && $_modulesArray['GoMage_Procart']->is('active')) {
            if (!$this->_procartproductlist) {
                $this->_procartproductlist = array();
                $helper                    = Mage::helper('gomage_procart');

                foreach ($this->getLoadedProductCollection() as $product) {
                    if (!isset($this->_procartproductlist[$product->getId()])) {
                        $this->_procartproductlist[$product->getId()] = $helper->getProcartProductData($product, false, false);
                    }
                    if ($product->isComposite()) {
                        $ti = $product->getTypeInstance(true);
                        foreach ($ti->getChildrenIds($product->getId()) as $groupIds) {
                            foreach ($groupIds as $id) {
                                $childProduct = Mage::getModel('catalog/product')->load($id);
                                if (!isset($this->_procartproductlist[$childProduct->getId()])) {
                                    $this->_procartproductlist[$childProduct->getId()] = $helper->getProcartProductData($childProduct, false, $product->getId());
                                } else {
                                    $this->_procartproductlist[$childProduct->getId()]['parent_id'] = $product->getId();
                                }
                            }
                        }
                    }
                }
            }

            return Mage::helper('core')->jsonEncode($this->_procartproductlist);
        }
    }

    public function getToolbarHtml()
    {
        $toolbar = $this->getChild('toolbar');
        return $toolbar->toHtml();
    }

}