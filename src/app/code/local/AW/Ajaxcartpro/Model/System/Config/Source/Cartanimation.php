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


class AW_Ajaxcartpro_Model_System_Config_Source_Cartanimation
{
    public function toOptionArray()
    {
        $helper = Mage::helper('ajaxcartpro');
        return array(
            array('value' => 'none',    'label' => $helper->__('None')),
            array('value' => 'opacity', 'label' => $helper->__('Opacity')),
            array('value' => 'grow',    'label' => $helper->__('Grow')),
            array('value' => 'blink',   'label' => $helper->__('Blink'))
        );
    }
}