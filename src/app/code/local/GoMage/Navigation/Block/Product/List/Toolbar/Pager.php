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
class GoMage_Navigation_Block_Product_List_Toolbar_Pager extends Mage_Page_Block_Html_Pager
{

    protected function _construct()
    {
        parent::_construct();
        if ($this->isAjaxPager()) {
            $this->setTemplate('gomage/navigation/html/pager.phtml');
        } else {
            $this->setTemplate('page/html/pager.phtml');
        }
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

    public function isAjaxPager()
    {
        return Mage::helper('gomage_navigation')->isGomageNavigationAjax() &&
        Mage::getStoreConfigFlag('gomage_navigation/general/pager');
    }

    public function getPreviousPageUrl($clean = false)
    {
        $url = parent::getPreviousPageUrl();

        if ($clean == true) {
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
        }

        return $url;
    }

    public function getPreviousPageUrlParams()
    {
        $url       = parent::getPreviousPageUrl();
        $clean_url = $url;

        if (strpos($url, "?") !== false) {
            $clean_url = substr($url, 0, strpos($url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }

    public function getNextPageUrl($clean = false)
    {
        $url = parent::getNextPageUrl();

        if ($clean == true) {
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
        }

        return $url;
    }

    public function getNextPageUrlParams()
    {
        $url       = parent::getNextPageUrl();
        $clean_url = $url;

        if (strpos($url, "?") !== false) {
            $clean_url = substr($url, 0, strpos($url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }

    public function getFirstPageUrl($clean = false)
    {
        $url = parent::getFirstPageUrl();

        if ($clean == true) {
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
        }

        return $url;
    }

    public function getFirstPageUrlParams()
    {
        $url       = parent::getFirstPageUrl();
        $clean_url = $url;

        if (strpos($url, "?") !== false) {
            $clean_url = substr($url, 0, strpos($url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }

    public function getLastPageUrl($clean = false)
    {
        $url = parent::getLastPageUrl();

        if ($clean == true) {
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
        }

        return $url;
    }

    public function getLastPageUrlParams()
    {
        $url       = parent::getLastPageUrl();
        $clean_url = $url;

        if (strpos($url, "?") !== false) {
            $clean_url = substr($url, 0, strpos($url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }

    public function getPreviousJumpUrl($clean = false)
    {
        $url = parent::getPreviousJumpUrl();

        if ($clean == true) {
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
        }

        return $url;
    }

    public function getPreviousJumpUrlParams()
    {
        $url       = parent::getPreviousJumpUrl();
        $clean_url = $url;

        if (strpos($url, "?") !== false) {
            $clean_url = substr($url, 0, strpos($url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }

    public function getNextJumpUrl($clean = false)
    {
        $url = parent::getNextJumpUrl();

        if ($clean == true) {
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
        }

        return $url;
    }

    public function getNextJumpUrlParams()
    {
        $url       = parent::getNextJumpUrl();
        $clean_url = $url;

        if (strpos($url, "?") !== false) {
            $clean_url = substr($url, 0, strpos($url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }

    public function getPageUrl($page, $clean = false)
    {
        $url = parent::getPageUrl($page);

        if ($clean == true) {
            if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
        }

        return $url;
    }

    public function getPageUrlParams($page)
    {
        $url       = parent::getPageUrl($page);
        $clean_url = $url;

        if (strpos($url, "?") !== false) {
            $clean_url = substr($url, 0, strpos($url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }
}
