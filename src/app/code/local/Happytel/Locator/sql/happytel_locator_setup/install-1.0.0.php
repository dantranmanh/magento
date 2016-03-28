<?php

/**
 * Location extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://andrewkett.github.io/Ak_Locator/
 */

/* @var $installer Ak_locator_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'company_name');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'branch_no');
$setup->removeAttribute(Ak_Locator_Model_Location::ENTITY,'google_map_iframes');


$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'branch_no', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Branch No',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 330,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'company_name', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Company Name',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 335,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'google_map_iframes', array(
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Google Map Iframes',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 340,
    'wysiwyg_enabled' => false,
));

$formAttributes = array(
    'branch_no', 'company_name', 'google_map_iframes'
);

$eavConfig = Mage::getSingleton('eav/config');

foreach ($formAttributes as $code) {
    $attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, $code);
    $attribute->setData('used_in_forms', array('location_edit','location_create'));
    $attribute->save();
}

$installer->endSetup();
