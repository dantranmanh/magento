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

/* @var $installer ak_locator_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'phone', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Phone',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 330,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'description', array(
    'input'         => 'textarea',
    'type'          => 'varchar',
    'label'         => 'Description',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 340,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'image', array(
    'input'         => 'image',
    'type'          => 'varchar',
    'label'         => 'Image',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 350,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'monday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Monday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 361,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'tuesday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Tuesday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 362,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'wednesday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Wednesday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 363,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'thursday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Thursday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 364,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'friday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Friday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 365,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'saturday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Saturday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 366,
));
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'sunday', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Sunday',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 367,
));

$formAttributes = array(
	'phone',
	'description',
	'image',
    'monday',
    'tuesday',
    'wednesday',
    'thursday',
    'friday',
    'saturday',
    'sunday'
);


$eavConfig = Mage::getSingleton('eav/config');

foreach ($formAttributes as $code) {
    $attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, $code);
    $attribute->setData('used_in_forms', array('location_edit','location_create'));
    $attribute->save();
}

$installer->endSetup();

