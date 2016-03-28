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
class GoMage_Navigation_Model_Enterprise_Search_Catalog_Layer_Filter_Category extends Enterprise_Search_Model_Catalog_Layer_Filter_Category
{
	protected $_resource;
	
    /**
     * @var array
     */
    protected $category_list = array();
	
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
                isset($itemData['image']) ? $itemData['image'] : '',
                isset($itemData['level']) ? $itemData['level'] : 0,
                isset($itemData['haschild']) ? $itemData['haschild'] : ''
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
    protected function _createItem($label, $value, $count = 0, $status = false, $image = '', $level = 0, $haschild = '')
    {
        return Mage::getModel('gomage_navigation/catalog_layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setActive($status)
            ->setImage($image)
            ->setLevel($level)
            ->setHasChild($haschild);
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
     * Get selected category object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        return $this->getLayer()->getCurrentCategory();
    }
	
    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {		
		$filter		= $request->getParam($this->getRequestVar());   
		$filters	= explode(',', $filter);
		
		if (empty($filters)) {
            return $this;
        }
        
		$is_multi = (count($filters) > 1) ? true : false;
		
		foreach ($filters as $filter) {
			$category = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($filter);

            if ($this->_isValidCategory($category)) {
				if (!$is_multi) {
					$this->getLayer()->getProductCollection()
						->addCategoryFilter($category);
				}
				
                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($category->getName(), $filter)
                );
            }
        }
		
		if ($is_multi) {
			$this->getLayer()->getProductCollection()->addCategoriesFilter($filters);
		}
		
        return $this;
    }
	
	/**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
		$key    = $this->getLayer()->getStateKey() . '_SUBCATEGORIES';
        $data   = $this->getLayer()->getCacheData($key);

        if ($data === null) {
			$selected = array();

			if ($value = Mage::helper('gomage_navigation')->getRequest()->getParam($this->_requestVar)) {
				$selected = array_merge($selected, explode(',', $value));
			}
			
			$categories = $this->getChildrenCategories($selected);
			
            $productCollection	= $this->getLayer()->getProductCollection();
            $facets				= $productCollection->getFacetedData('category_ids');
						
            $data = array();
			
            foreach ($categories as $category) {
                $categoryId = $category->getId();
				
                if (isset($facets[$categoryId])) {
                    $category->setProductCount($facets[$categoryId]);
                } else {
                    $category->setProductCount(0);
                }
				
				if (in_array($categoryId, $selected)) {
					$active = true;
					$value  = $categoryId;
				} else {
					$active = false;
					
					if (!empty($selected)) {
						$value = implode(',', array_merge($selected, (array) $categoryId));
					} else {
						$value = $categoryId;
					}
				}	
				
                if ($category->getIsActive()) {
					if (Mage::getStoreConfig('gomage_navigation/' . $this->getData('config_tab') . '/hide_empty') && !$category->getProductCount()) {
						continue;
					}
					
                    $data[] = array(
                        'label'		=> Mage::helper('core')->escapeHtml($category->getName()),
                        'value'		=> $value,
                        'count'		=> $category->getProductCount(),
						'active'	=> $active,
						'image'		=> $category->getFilterImage(),
                        'level'		=> $category->getLevel(),
                        'haschild'	=> $category->getChildren(),
                    );
                }
            }

            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }

        return $data;	
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
	
	/**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Category
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getModel('gomage_navigation/resource_eav_mysql4_layer_filter_category');
        }
		
        return $this->_resource;
    }
	
	private function getChildrenCategories(array $selected = array())
	{		
		$base_children_categories_ids = array();
		
		if (!Mage::getStoreConfigFlag('gomage_navigation/' . $this->getData('config_tab') . '/show_allsubcats')) {
			$base_children_categories = $this->getCategory()->getChildrenCategories();
			
			if (empty($selected)) {
				return $base_children_categories;
			} else {
				$base_children_categories_ids = $selected;
				
				foreach ($base_children_categories as $base_children_category) {
					$base_children_categories_ids[] = $base_children_category->getId();
				}
			}
		} else {
			$base_children_categories_ids = array_diff(
				$this->getCategory()->getAllChildren(true), 
				array($this->getCategory()->getId())
			);
		}
		
		$base_children_categories_ids = array_unique($base_children_categories_ids);
		
		$collection = Mage::getResourceModel('catalog/category_collection');
		
		$collection->setStoreId(Mage::app()->getStore()->getId())
			->addAttributeToSelect('url_key')
			->addAttributeToSelect('name')
			->addAttributeToSelect('all_children')
			->addAttributeToSelect('level')
			->addAttributeToSelect('is_anchor')
			->addAttributeToFilter('is_active', 1)
			->addAttributeToSelect('filter_image')
			->addIdFilter(implode(',', $base_children_categories_ids))
			->joinUrlRewrite()
			->getSelect()		
			->order('path');
		
		if (!empty($selected)) {
			$collection->getSelect()
				->orWhere('e.parent_id IN (' . implode(',', $selected) . ')');
		}
		
		return $collection->load();
	}
}
