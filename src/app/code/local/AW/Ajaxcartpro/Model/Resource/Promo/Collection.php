<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @version    3.2.11
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Ajaxcartpro_Model_Resource_Promo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ajaxcartpro/promo');
    }

    public function addStoreFilter($store)
    {
        $this->addFieldToFilter(
            'store_ids',
            array(
                array('finset' => '0'),
                array('finset' => $store)
            )
        );
        return $this;
    }

    public function addTypeFilter($ruleType)
    {
        return $this->addFieldToFilter('type', $ruleType);
    }

    public function addActiveFilter()
    {
        return $this->addFieldToFilter('is_active', 1);
    }

    public function addDateFilter($now = null)
    {
        if (is_null($now)) {
            $now = Mage::getModel('core/date')->date('Y-m-d');
        }
        $this->addFieldToFilter(
            'from_date',
            array(
                array('null' => 'from_date'),
                array('lteq' => $now)
            )
        );
        $this->addFieldToFilter(
            'to_date',
            array(
                array('null' => 'to_date'),
                array('gteq' => $now)
            )
        );
        return $this;
    }

    public function addCustomerGroupFilter($groupId)
    {
        $this->addFieldToFilter(
            'customer_groups', array('finset' => $groupId)
        );
        return $this;
    }

    public function addSortOrderByPriority()
    {
        return $this->setOrder('priority');
    }
}