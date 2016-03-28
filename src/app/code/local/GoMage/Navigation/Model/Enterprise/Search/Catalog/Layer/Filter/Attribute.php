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
class GoMage_Navigation_Model_Enterprise_Search_Catalog_Layer_Filter_Attribute extends Enterprise_Search_Model_Catalog_Layer_Filter_Attribute
{	
	protected $_resource;
	
	/**
     * Initialize filter items
     *
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        
		foreach ($data as $itemData) {
            $items[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['active'],
                isset($itemData['image']) ? $itemData['image'] : ''
            );
        }
       
	    $this->_items = $items;
        
		return $this;
    }

    /**
     * Create filter item object
     *
     * @param string $label
     * @param mixed $value
     * @param int $count
     * @param bool $status
     * @param string $image
     * @param int $level
     * @param string $haschild
     * @param string $from_to
     * @return Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count = 0, $status = false, $image = '')
    {
        return Mage::getModel('gomage_navigation/catalog_layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setActive($status)
            ->setImage($image);
    }
	
	/**
     * Get filter value for reset current filter state
     *
     * @return mixed
     */
    public function getResetValue($value_to_remove = null)
    {
        if ($value_to_remove && ($current_value = Mage::helper('gomage_navigation')->getRequest()->getParam($this->_requestVar))) {
            $current_value = explode(',', $current_value);

            if (false !== ($position = array_search($value_to_remove, $current_value))) {
                unset($current_value[$position]);
                
				if (!empty($current_value)) {
                    return implode(',', $current_value);
                }
            }
        }

        return null;
    }
	
    /**
     * Apply attribute filter to layer
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param object $filterBlock
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->_requestVar);
       
	    if (is_array($filter)) {
            return $this;
        }

        if ($filter) {
            $filters = explode(',', $filter);
			$this->applyFilterToCollection($this, $filters);
            
			foreach ($filters as $filter) {
				$text = $this->_getOptionText($filter);
				$this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            }
        }
		
        return $this;
    }
	
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
		$attribute			= $this->getAttributeModel();	
        $this->_requestVar	= $attribute->getAttributeCode();	
		$option_images		= $attribute->getOptionImages();
		
		$selected			= array();

        if ($value = Mage::helper('gomage_navigation')->getRequest()->getParam($this->_requestVar)) {
            $selected = array_merge($selected, explode(',', $value));
        }
		    
		$engine				= Mage::getResourceSingleton('enterprise_search/engine');
        $fieldName			= $engine->getSearchEngineFieldName($attribute, 'nav');
        $productCollection	= $this->getLayer()->getProductCollection();
        $optionsFacetedData	= $productCollection->getFacetedData($fieldName);
        $options			= $attribute->getSource()->getAllOptions(false);
        $data				= array();
		
		foreach ($options as $option) {
			$image	= '';
			$active	= false;

			if (Mage::helper('core/string')->strlen($option['value'])) {
				if ($option_images && isset($option_images[$option['value']])) {
					$image = $option_images[$option['value']];
				}

				if (in_array($option['value'], $selected)) {
					$active = true;
					$value  = $option['value'];
				} else {
					$active = false;
					
					if (!empty($selected) && $attribute->getFilterType() != GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DROPDOWN) {
						$value = implode(',', array_merge($selected, (array) $option['value']));
					} else {
						$value = $option['value'];
					}
				}
			}
			
            $optionId = $option['value'];
            
            if ($this->_getIsFilterableAttribute($attribute) != self::OPTIONS_ONLY_WITH_RESULTS
                || isset($optionsFacetedData[$optionId])
            ) {
                $data[] = array(
                    'label'		=> $option['label'],
                    'value'		=> $value,
                    'count'		=> ($optionsFacetedData[$optionId]) ? $optionsFacetedData[$optionId] : 0,
					'active'	=> $active,
                    'image'		=> $image,
                );
            }
        }
		
        return $data;
    }
	
	/**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getModel('gomage_navigation/resource_eav_mysql4_layer_filter_attribute');
        }
		
        return $this->_resource;
    }
	
	/*****/
	
	public function hasAttributeModel()
    {   
		return $this->hasData('attribute_model');
    }	
	
	public function getRequestVarValue()
    {
        return $this->_requestVar;
    }
}
