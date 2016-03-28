<?php
/**
 * Happytel
 *
 * @category    Happytel
 * @package     Happytel_SortByNewest
 * @create by   Dev Balance Internet
 *
 */

$installer = $this;

$installer->startSetup();
$code='featured_product';
$name="Is Feature Product";
$attb = Mage::getModel('catalog/resource_eav_attribute')
    ->loadByCode('catalog_product',$code);
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
if(null===$attb->getId()) {
    $setup->addAttribute('catalog_product', $code, array(
        'type'              => 'int',
        'backend_type'      => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Is Featured',
        'input'             => 'boolean',
        'frontend_class'    => '',
        'source'            => 'eav/entity_attribute_source_boolean',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => false,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => '',
        'is_configurable'   => false,
    ));

    $setup->addAttributeToSet('catalog_product', 'Default', 'General', $code);
}

$installer->endSetup();
