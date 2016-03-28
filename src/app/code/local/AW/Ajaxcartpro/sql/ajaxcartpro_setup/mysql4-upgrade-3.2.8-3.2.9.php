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

try {
    $this->startSetup();
    $this->run("
        ALTER TABLE {$this->getTable('ajaxcartpro/promo')} 
		ADD INDEX `type` (`type`),
		ADD INDEX `is_active` (`is_active`),
		ADD INDEX `to_date` (`to_date`),
		ADD INDEX `from_date` (`from_date`),
		ADD INDEX `store_ids` (`store_ids`),
		ADD INDEX `customer_groups` (`customer_groups`),
		ADD INDEX `priority` (`priority`)		
		;
    ");
    $this->endSetup();
} catch (Exception $e) {
    echo $e->getMessage();
    Mage::logException($e);
}