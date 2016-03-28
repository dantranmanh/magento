<?php

/**
 * AutomaticInvoice Extensions created by Balance Internet
 *
 * NOTICE OF LICENSE
 *
 * This source file is licensed of Balance Internet.
 * All this code is used for Balance Internet properties
 *
 * @copyright   Copyright (c) 2016 Balance Internet
 * @license     http://www.balanceinternet.com.au/
 * @developer    Toan Nguyen (toan.nguyen@balanceinternet.com.au)
 */
class Happytel_Comparerefresh_Model_Observer
{
    public function comparerefreshfullpagecache(Varien_Event_Observer $observer)
    {
        try {
            Enterprise_PageCache_Model_Cache::getCacheInstance()->cleanType('full_page');
        } catch (Mage_Core_Exception $e) {
            Mage::log("Error: " . $e->getMessage());
        }

        return $this;
    }
}