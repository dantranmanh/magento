<?php

$installer = $this;

$installer->startSetup();


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'noinhap', array(
    'group'         => 'General',
    'type'          => 'int',
    'input'         => 'select',
    'label'         => 'Nơi Nhập Hàng',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'default'       => '0',
    'user_defined'  => 1,
    'searchable'    => 1,
    'filterable'    => 0,
    'comparable'    => 0,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$entityType = Mage::getModel('catalog/product')->getResource()->getEntityType();

$collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
    ->setEntityTypeFilter($entityType->getId());

foreach ($collection as $attributeSet) {
    $attributeGroupId = $installer->getDefaultAttributeGroupId('catalog_product',$attributeSet->getId());
    $installer->addAttributeToSet('catalog_product', $attributeSet->getId(), $attributeGroupId, 'noinhaphang');
}


$installer->endSetup(); 