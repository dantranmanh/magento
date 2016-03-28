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
 * @since        Class available since Release 1.0
 */
class GoMage_Navigation_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{
    protected $attribute_options_images = null;

    const FILTER_TYPE_DEFAULT         = 0;
    const FILTER_TYPE_IMAGE           = 1;
    const FILTER_TYPE_DROPDOWN        = 2;
    const FILTER_TYPE_INPUT           = 3;
    const FILTER_TYPE_SLIDER          = 4;
    const FILTER_TYPE_SLIDER_INPUT    = 5;
    const FILTER_TYPE_PLAIN           = 6;
    const FILTER_TYPE_FOLDING         = 7;
    const FILTER_TYPE_DEFAULT_PRO     = 8;
    const FILTER_TYPE_DEFAULT_INBLOCK = 9;
    const FILTER_TYPE_INPUT_SLIDER    = 10;
    const FILTER_TYPE_ACCORDION       = 11;
	
    public function prepareProductCollection($collection)
    {
        parent::prepareProductCollection($collection);
        
		$collection->getSelect()->group('e.entity_id');
        
		return $this;
    }

    protected function _prepareAttributeCollection($collection)
    {
        $collection = parent::_prepareAttributeCollection($collection);

        $collection->addIsFilterableFilter();

        $tableAlias = 'gomage_nav_attr';

        $collection->getSelect()->joinLeft(
            array($tableAlias => Mage::getSingleton('core/resource')->getTableName('gomage_navigation_attribute')),
            "`main_table`.`attribute_id` = `{$tableAlias}`.`attribute_id`",
            array('filter_type',
                'inblock_type',
                'round_to',
                'show_currency',
                'image_align',
                'image_width',
                'image_height',
                'show_minimized',
                'show_image_name',
                'visible_options',
                'show_help',
                'show_checkbox',
                'popup_width',
                'popup_height',
                'filter_reset',
                'is_ajax',
                'inblock_height',
                'max_inblock_height',
                'filter_button',
                'category_ids_filter',
                'range_options',
                'range_manual',
                'range_auto',
                'attribute_location')
        );

        $tableAliasStore = 'gomage_nav_attr_store';

        $collection->getSelect()->joinLeft(
            array($tableAliasStore => Mage::getSingleton('core/resource')->getTableName('gomage_navigation_attribute_store')),
            "`main_table`.`attribute_id` = `{$tableAliasStore}`.`attribute_id` and `{$tableAliasStore}`.store_id = " . Mage::app()->getStore()->getStoreId(),
            array('popup_text')
        );

        foreach ($collection as $attribute) {
            $attribute->setOptionImages($this->getAttributeOptionsImages($attribute->getId()));
        }

        return $collection;
    }
	
    public function getAttributeOptionsImages($attribute_id)
    {
        if (is_null($this->attribute_options_images)) {
            $this->attribute_options_images = array();

            $options = Mage::getModel('gomage_navigation/attribute_option')
                ->getCollection();

            foreach ($options as $option) {
                if (!isset($this->attribute_options_images[$option->getData('attribute_id')])) {
                    $this->attribute_options_images[$option->getData('attribute_id')] = array();
                }
                
				$this->attribute_options_images[$option->getData('attribute_id')][$option->getData('option_id')] =
                    $option->getData('filename');

            }

        }
		
        return isset($this->attribute_options_images[$attribute_id]) ? $this->attribute_options_images[$attribute_id] : array();
    }
}