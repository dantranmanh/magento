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
 * @since        Class available since Release 4.7
 */
class GoMage_Navigation_Helper_Url extends Mage_Core_Helper_Url
{
    public function wrapp($url)
    {
        //can't decode url gomage-8956
        return $url;
    }

    public function prepareCategory(Varien_Object $category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            return $category;
        }

        return Mage::getModel('catalog/category')->load($category->getId());
    }

    public function categoryUrl(Varien_Object $category, $params = array())
    {
        $urlPath =
            (is_array($params) && !isset($params['_direct'])) ?
                $this->prepareCategory($category)->getUrlPath() :
                $params['_direct'];

        $params = array_merge(
            array(
                '_use_rewrite' => true,
                '_query'       => array(),
                '_secure'      => true,
                '_direct'      => ltrim($urlPath, '/')
            ),
            $params
        );

        $url = Mage::getUrl('*/*/*', $params);
        $url = Mage::getModel('core/url')->sessionUrlVar($url);

        return $this->wrapp($url);
    }

    public function categoryFilterIsActive(Varien_Object $category)
    {
        $helper      = Mage::helper('gomage_navigation');
        $active_cats = $helper->getRequest()->getParam('cat');
        $active_cats = explode(',', $active_cats);

        return in_array($category->getId(), $active_cats);
    }

    public function categoryFilterUrl(Varien_Object $category, $params = array())
    {
        if (empty($params['_direct']) && !Mage::helper('gomage_navigation/config')->isCMSPage()) {
            $params['_direct'] = null;
        }

        $params = array_merge(
            array(
                '_current'     => true,
                '_use_rewrite' => true,
                '_query'       => array(),
                '_secure'      => true,
            ),
            $params
        );

        $helper = Mage::helper('gomage_navigation');
        $cat    = explode(',', $helper->getRequest()->getParam('cat', ''));

        if (in_array($category->getId(), $cat)) {
            $cat = array_diff($cat, array($category->getId()));
            if (empty($cat)) {
                $cat = null;
            } else {
                $cat = implode(',', $cat);
            }
        } else {
            $cat[] = $category->getId();
            $cat   = implode(',', $cat);
        }

        $params['_query']['cat'] = $cat;

        $params['_query'] = $this->prepareUrlQuery($params['_query']);

        return $this->categoryUrl(Mage::helper('gomage_navigation/config')->curentCategory(), $params);
    }

    public function prepareUrlQuery($query = array())
    {
        $helper = Mage::helper('gomage_navigation');

        if (!$helper->isFrendlyUrl()) {
            return $query;
        }

        $_query = array();

        $filter_attributes = Mage::registry('gan_filter_attributes');
        $request_query     = is_array($helper->getRequest()->getQuery()) ? $helper->getRequest()->getQuery() : array();
        $query             = array_merge($request_query, $query);

        foreach ($query as $param => $value) {
            if (is_null($value)) {
                $query[$param] = null;

                continue;
            }

            if ($param == 'cat') {
                $values         = explode(',', $value);
                $prepare_values = array();

                foreach ($values as $_value) {
                    $category = Mage::getModel('catalog/category')->load($_value);

                    if ($category && $category->getId()) {
                        if (Mage::getStoreConfigFlag('gomage_navigation/filter_settings/expend_frendlyurl')) {
                            $parent_ids       = $category->getParentIds();
                            $parent_category  = Mage::getModel('catalog/category')->load(end($parent_ids));
                            $prepare_values[] = $parent_category->getData('url_key') . '|' . $category->getData('url_key');
                        } else {
                            $prepare_values[] = $category->getData('url_key');
                        }
                    }
                }

                if (!empty($prepare_values)) {
                    $_query[$param] = implode(',', $prepare_values);
                } else {
                    $_query[$param] = null;
                }
            } elseif (isset($filter_attributes[$param]) && !in_array($filter_attributes[$param]['type'], array('price', 'decimal'))) {
                $values         = explode(',', $value);
                $prepare_values = array();

                foreach ($values as $_value) {
                    foreach ($filter_attributes[$param]['options'] as $_k => $_v) {
                        if ($_v == $_value) {
                            $prepare_values[] = $_k;
                            break;
                        }
                    }
                }

                if (!empty($prepare_values)) {
                    $_query[$param] = implode(',', $prepare_values);
                } else {
                    $_query[$param] = null;
                }
            } elseif (isset($filter_attributes[$param]) && in_array($filter_attributes[$param]['type'], array('price', 'decimal'))) {
                if (is_array($value)) {
                    if (isset($value['from'])) {
                        $_query[$param . '_from'] = $value['from'];
                    }

                    if (isset($value['to'])) {
                        $_query[$param . '_to'] = $value['to'];
                    }
                } elseif (
                    ($filter_attributesibute = $helper->getProductAttribute($param)) &&
                    in_array(
                        $filter_attributesibute->getRangeOptions(),
                        array(
                            GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::MANUALLY,
                            GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO
                        )
                    ) &&
                    $filter_attributesibute->getFilterType() == GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT
                ) {
                    $values                   = explode(',', $value);
                    $_query[$param . '_from'] = $values[0];
                    $_query[$param . '_to']   = $values[1];
                    $_query[$param]           = null;
                } else {
                    $_query[$param] = $value;
                }
            } else {
                $_query[$param] = $value;
            }
        }

        return $_query;
    }
}