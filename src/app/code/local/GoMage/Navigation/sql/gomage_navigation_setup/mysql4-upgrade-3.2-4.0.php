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
 * @since        Release available since Release 4.0
 */

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('gomage_navigation_attribute'), 'max_inblock_height',
    "int(3) NOT NULL");

$installer->getConnection()->addColumn($this->getTable('gomage_navigation_attribute'), 'inblock_type',
    "smallint(2) NOT NULL default '1'");

$installer->getConnection()->addColumn($this->getTable('gomage_navigation_attribute'), 'round_to',
    "smallint(5) NOT NULL default '1'");

$installer->getConnection()->addColumn($this->getTable('gomage_navigation_attribute'), 'range_options',
    "smallint(5) NOT NULL default '0'");

$installer->getConnection()->addColumn($this->getTable('gomage_navigation_attribute'), 'range_manual',
    "text NOT NULL");

$installer->getConnection()->addColumn($this->getTable('gomage_navigation_attribute'), 'range_auto',
    "text NOT NULL");

$installer->getConnection()->addColumn($this->getTable('gomage_navigation_attribute'), 'attribute_location',
    "smallint(5) NOT NULL default '0'");

$installer->getConnection()->addColumn($this->getTable('gomage_navigation_attribute'), 'show_currency',
    "smallint(1) NOT NULL default 0");

$attribute_data = array(
        'group'             => 'Advanced Navigation',
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Show Shop By in',
        'input'             => 'select',
        'class'             => '',
        'source'            => 'gomage_navigation/adminhtml_system_config_source_shopby',
        'global'            => true,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => 0,
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
		'sort_order'		=> 10,		
    );
$installer->addAttribute('catalog_category', 'navigation_pw_gn_shopby', $attribute_data);

   
$installer->endSetup(); 