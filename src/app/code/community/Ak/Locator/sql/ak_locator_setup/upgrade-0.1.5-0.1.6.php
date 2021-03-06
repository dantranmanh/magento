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

$installer->removeAttribute(Ak_Locator_Model_Location::ENTITY, 'description');

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'description', array(
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Description',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 340,
));

$formAttributes = array(
	'description'
);


$eavConfig = Mage::getSingleton('eav/config');

foreach ($formAttributes as $code) {
    $attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, $code);
    $attribute->setData('used_in_forms', array('location_edit','location_create'));
    $attribute->save();
}

$installer->endSetup();

