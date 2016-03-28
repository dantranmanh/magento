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
class GoMage_Navigation_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{

    protected $first_render = true;

    protected function _toHtml()
    {
        if (Mage::helper('gomage_navigation')->isGomageNavigationAjax()) {
            $this->setTemplate('gomage/navigation/catalog/product/list/toolbar.phtml');
        }

        $currentCategory = Mage::registry('current_category');

        if (($currentCategory && $currentCategory->getData('navigation_pw_gn_shopby') == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::USE_GLOBAL)
            ||
            !$currentCategory
        ) {
            $position = Mage::getStoreConfig('gomage_navigation/general/show_shopby');
        } else {
            if ($currentCategory) {
                $position = $currentCategory->getData('navigation_pw_gn_shopby');
            }
        }

        $html = '';
        if (Mage::helper('gomage_navigation')->isGomageNavigation()
            &&
            ($position == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::CONTENT
                ||
                $position == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::RIGHT_COLUMN_CONTENT
                ||
                $position == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::LEFT_COLUMN_CONTENT)
        ) {
            if ($this->first_render) {
                $shop_by = false;
                if ($this->getLayout()->getBlock('catalogsearch.leftnav')) {
                    $shop_by = $this->getLayout()->getBlock('catalogsearch.leftnav');
                } elseif ($this->getLayout()->getBlock('catalog.leftnav')) {
                    $shop_by = $this->getLayout()->getBlock('catalog.leftnav');
                } elseif ($this->getLayout()->getBlock('gomage.enterprise.catalogsearch.leftnav')) {
                    $shop_by = $this->getLayout()->getBlock('gomage.enterprise.catalogsearch.leftnav');
                } elseif ($this->getLayout()->getBlock('gomage.enterprise.catalog.leftnav')) {
                    $shop_by = $this->getLayout()->getBlock('gomage.enterprise.catalog.leftnav');
                }
                if ($shop_by) {
                    $shop_by->setShopByInContent(true);
                    $html .= $shop_by->toHtml();
                    $shop_by->setShopByInContent(false);
                }
                $this->first_render = false;
            }
        }
        if (!$this->getTemplate()) {
            return $html;
        }
        $html .= $this->renderView();
        return $html;
    }

    public function getPagerUrl($params = array())
    {
        $urlParams                 = array();
        $urlParams['_nosid']       = true;
        $urlParams['_current']     = true;
        $urlParams['_escape']      = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query']       = $params;

        return Mage::helper('gomage_navigation')->getFilterUrl('*/*/*', $urlParams);
    }


    public function getPagerHtml()
    {
        $pagerBlock = $this->getChild('gomage_navigation_product_list_toolbar_pager');

        if (!$pagerBlock) {
            $pagerBlock = $this->getLayout()->createBlock('gomage_navigation/product_list_toolbar_pager', 'gomage_navigation_product_list_toolbar_pager');
            $this->insert($pagerBlock);
        }

        if ($pagerBlock instanceof Varien_Object) {

            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimitVarName($this->getLimitVarName())
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getLimit())
                ->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($this->getCollection());

            return $pagerBlock->toHtml();
        }

        return '';

    }

    public function getFirstNum()
    {
        if (Mage::getStoreConfigFlag('gomage_navigation/general/autoscrolling')) {
            return 1;
        }

        return parent::getFirstNum();
    }

    public function getOrderUrl($order, $direction, $clean = false)
    {
        $url = parent::getOrderUrl($order, $direction);

        if ($clean == true) {
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
        }

        return $url;
    }

    public function getOrderUrlParams($order, $direction)
    {
        $url       = parent::getOrderUrl($order, $direction);
        $clean_url = $url;

        if (strpos($url, "?") !== false) {
            $clean_url = substr($url, 0, strpos($url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }

    public function getModeUrl($mode, $clean = false)
    {
        $url = parent::getModeUrl($mode);

        if ($clean == true) {
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
        }

        return $url;
    }

    public function getModeUrlParams($mode)
    {
        $url       = parent::getModeUrl($mode);
        $clean_url = $url;

        if (strpos($url, "?") !== false) {
            $clean_url = substr($url, 0, strpos($url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }
}