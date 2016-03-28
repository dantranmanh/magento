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
class GoMage_Navigation_Helper_Config
{

    protected $store_root_category_id = null;
    protected $current_category = null;

    public function storeRootCategoryId()
    {
        if ($this->store_root_category_id === null) {
            return $this->store_root_category_id = (int)Mage::app()->getStore()->getRootCategoryId();
        }

        return $this->store_root_category_id;
    }

    public function curentCategory()
    {
        if ($this->current_category === null) {
            if (Mage::registry('current_category')) {
                $this->current_category = Mage::registry('current_category');
            } else {
                if ($this->isCMSPage()) {
                    $category_id = (int)$this->getCMSPage()->getData('navigation_category_id');
                    if (!$category_id) {
                        $category_id = $this->storeRootCategoryId();
                    }
                } else {
                    $category_id = $this->storeRootCategoryId();
                }

                $category_model         = Mage::getModel('catalog/category');
                $this->current_category = $category_model->load($category_id);
            }
        }

        return $this->current_category;
    }

    public function isCMSPage()
    {
        return (bool)$this->getCMSPage()->getData('page_id');
    }

    public function getCMSPage()
    {
        if ($gan_page_id = Mage::helper('gomage_navigation')->getRequest()->getParam('gan_page_id', 0)) {
            Mage::getSingleton('cms/page')->load($gan_page_id);
        }

        return Mage::getSingleton('cms/page');
    }

}