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
class GoMage_Navigation_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View
{
	protected $GMN	= null;
	
	protected $_stockFilterBlockName	= null;
	
	protected $shopby_position			= null;
	protected $can_show_category_filter	= null; 
	
	/**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
		if (!$this->isGMN()) { return parent::_initBlocks(); }
		
        parent::_initBlocks();
		    
		$this->_categoryBlockName			= 'gomage_navigation/catalog_layer_filter_category';
		$this->_attributeFilterBlockName	= 'gomage_navigation/catalog_layer_filter_attribute';
		$this->_priceFilterBlockName		= 'gomage_navigation/catalog_layer_filter_price';
		$this->_decimalFilterBlockName		= 'gomage_navigation/catalog_layer_filter_decimal';
		$this->_stockFilterBlockName		= 'gomage_navigation/catalog_layer_filter_stock';
    }
	
	/**
    * Prepare child blocks
    *
    * @return Mage_Catalog_Block_Layer_View
    */
    protected function _prepareLayout()
    {
		if (!$this->isGMN()) { 
			$this->setTemplate('catalog/layer/view.phtml');
			
			return parent::_prepareLayout(); 
		}
		
		$filterableAttributes	= $this->_getFilterableAttributes();
        $collection				= $this->getLayer()->getProductCollection();
        $base_select			= array();
        $request				= Mage::helper('gomage_navigation')->getRequest();

        if ($request->getParam('price') || $request->getParam('price_from') || $request->getParam('price_to')) {
            $base_select['price'] = clone $collection->getSelect();
        }

        if ($request->getParam('cat')) {
            $base_select['cat'] = clone $collection->getSelect();
        }

        foreach ($filterableAttributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if ($request->getParam($code, false)) {
                $base_select[$code] = clone $collection->getSelect();
            }
        }

        $this->getLayer()->setBaseSelect($base_select);
		
		if (Mage::getStoreConfigFlag('gomage_navigation/stock/active')) {
			$stockBlock = $this->getLayout()->createBlock($this->_stockFilterBlockName)
				->setLayer($this->getLayer())
				->init();
	
			$this->setChild('stock_status_filter', $stockBlock);
		}
		
        parent::_prepareLayout();
        
		$this->setTemplate('gomage/navigation/layer/view.phtml');
		$this->setData('position', GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::LEFT_BLOCK);
		
		$shopby_position = $this->getShopbyPosition();	
		$blocks = array();	
		
		switch ($shopby_position) {
			case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::CONTENT :
				$blocks['gomage.catalog.content']	= $this->getLayout()->getBlock('content');
			break;
			
			case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::LEFT_COLUMN :
				
			break;
			
			case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::RIGHT_COLUMN :
				$blocks['gomage.catalog.right']	= $this->getLayout()->getBlock('right');
			break;
			
			case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::LEFT_COLUMN_CONTENT :
				$blocks['gomage.catalog.content']	= $this->getLayout()->getBlock('content');
			break;			
			
			case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::RIGHT_COLUMN_CONTENT :
				$blocks['gomage.catalog.right']		= $this->getLayout()->getBlock('right');	
				$blocks['gomage.catalog.content']	= $this->getLayout()->getBlock('content');
			break;
		}
		
		foreach ($blocks as $block_name => $block) {
			if ($block) {
				$nav_block = clone $this;
				$nav_block->setParentBlock($block);
				$nav_block->setNameInLayout($block_name);			
				
				switch ($block_name) {
					case 'gomage.catalog.right' :
						$nav_block->setData('position', GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::RIGHT_BLOCK);
					break;
					
					case 'gomage.catalog.content' :
						$nav_block->setData('position', GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::CONTENT);
					break;
				}
				
				$block->insert($nav_block, $block_name, false, $block_name);			
			}
		}
		
		if (
			$shopby_position !== GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::LEFT_COLUMN && 
			$shopby_position !== GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::LEFT_COLUMN_CONTENT 
		) {			
			$this->setTemplate(null);
		}
		
		return $this;
    }
	
	/**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
		if (!$this->isGMN()) { return parent::getLayer(); }
		   
        return Mage::getSingleton('gomage_navigation/catalog_layer');
    }
	
	/**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters()
    {
		if (!$this->isGMN()) { return parent::getFilters(); }
		
        $filters = parent::getFilters();

        if ($this->isGMN() && Mage::getStoreConfigFlag('gomage_navigation/stock/active')) {
            $filters[] = $this->getChild('stock_status_filter');
        }

        return $filters;
    }
	
	/**
     * Get category filter block
     *
     * @return Mage_Catalog_Block_Layer_Filter_Category
     */
    protected function _getCategoryFilter()
    {
		if (!$this->isGMN()) { return parent::_getCategoryFilter(); }
		
        $categoryFilter = parent::_getCategoryFilter();
		$position = $this->getData('position');
		
        $categoryFilter->setBlockSide($position);
        $categoryFilter->setCustomTemplate();
		$config_tab = $categoryFilter->getConfigTab();
		$categoryFilter
			->getFilter()
			->setData('config_tab', $config_tab);		
		
		if (null === $this->can_show_category_filter) {
			if (Mage::getStoreConfigFlag('gomage_navigation/' . $config_tab . '/active') && Mage::getStoreConfig('gomage_navigation/' . $config_tab . '/show_shopby')) {						
				$this->can_show_category_filter = true;
			}
		}
		
		return ($this->can_show_category_filter) ? $categoryFilter : false;
    }
	
	/**
     * Get url for 'Clear All' link
     *
     * @return string
     */
    public function getClearUrl($ajax = false)
    {
		if (!$this->isGMN()) { return parent::getClearUrl(); }
		
        $filterState = array();

        foreach ($this->getActiveFilters() as $item) {
            try {
                switch ($item->getFilter()->getAttributeModel()->getFilterType()) {
                    case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT) :
                        $filterState[$item->getFilter()->getRequestVarValue() . '_from'] = null;
                        $filterState[$item->getFilter()->getRequestVarValue() . '_to']   = null;
                    break;
					
                    case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER) :
                    case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT) :
                    case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER) :
                        if (Mage::helper('gomage_navigation')->isMobileDevice()) {
                            $filterState[$item->getFilter()->getRequestVarValue()] = $item->getFilter()->getResetValue();
                        } else {
                            $filterState[$item->getFilter()->getRequestVarValue() . '_from'] = null;
                            $filterState[$item->getFilter()->getRequestVarValue() . '_to']   = null;
                        }	
                    break;
					
                    case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT) :
                        if ($item->getFilter()->getAttributeModel()->getRangeOptions() != GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::NO) {
                            $filterState[$item->getFilter()->getRequestVarValue() . '_from'] = null;
                            $filterState[$item->getFilter()->getRequestVarValue() . '_to']   = null;
                        } else {
                            $filterState[$item->getFilter()->getRequestVarValue()] = $item->getFilter()->getResetValue();
                        }
                    break;
					
                    default :
                        $filterState[$item->getFilter()->getRequestVarValue()] = $item->getFilter()->getResetValue();
                        break;
                }
            } catch (Exception $e) {
                $filterState[$item->getFilter()->getRequestVarValue()] = $item->getFilter()->getResetValue();
            }
        }

        $params['_nosid']       = true;
        $params['_current']     = true;
		$params['_secure']      = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $filterState;
        $params['_escape']      = true;

        $params['_query']['ajax'] = null;

        if ($ajax) {
            $params['_query']['ajax'] = true;
        }

        return Mage::getUrl('*/*/*', $params);
    }
	
	/*****/
	
	public function isGMN() 
	{
		if ($this->GMN === null) {
			$this->GMN = Mage::helper('gomage_navigation')->isGomageNavigation();
		} 
        
		return $this->GMN;
    }
	
	public function getShopbyPosition()
	{
		if (null === $this->shopby_position) {
			$currentCategory	= Mage::registry('current_category');
			
			if ($currentCategory && ((int) $currentCategory->getData('navigation_pw_gn_shopby') !== GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::USE_GLOBAL)) {
				$this->shopby_position = (int) $currentCategory->getData('navigation_pw_gn_shopby');
			} else {
				$this->shopby_position = (int) Mage::getStoreConfig('gomage_navigation/general/show_shopby');
			}
		}		
		
		return $this->shopby_position;
	}
	
    public function canShowLayeredBlock($check)
    {
        $_filters = $this->getFilters();
        $canShow  = false;
		
        foreach ($_filters as $_filter) {
            $category = Mage::registry("current_category");
            
			if ($category && in_array($category->getId(), explode(",", $_filter->getCategoryIdsFilter()))) {
                continue;
            }

            if ($_filter->getItemsCount() 
				&& ($_filter->getAttributeLocation() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::USE_GLOBAL 
					|| $_filter->getAttributeLocation() == $check)
            ) {
                if (Mage::helper('gomage_navigation')->getFilterItemCount($_filter)) {
                    $canShow = true;
                }
            }

        }

        return $canShow;
    }
	
	public function getPopupStyle()
    {
        return (string) Mage::getStoreConfig('gomage_navigation/filter/popup_style');
    }

    public function getSliderType()
    {
        return (string) Mage::getStoreConfig('gomage_navigation/filter/slider_type');
    } 

    public function getSliderStyle()
    {
        return (string) Mage::getStoreConfig('gomage_navigation/filter/slider_style');
    }

    public function getIconStyle()
    {
        return (string) Mage::getStoreConfig('gomage_navigation/filter/icon_style');
    }

    public function getButtonStyle()
    {
        return (string) Mage::getStoreConfig('gomage_navigation/filter/button_style');
    }

    public function getFilterStyle()
    {
        return (string) Mage::getStoreConfig('gomage_navigation/filter/style');
    }
	
	public function getFiltersWidth($check)
    {
        $filters = $this->getFilters();
        $i       = 0;

        foreach ($filters as $_filter) {
            if (($_filter->getPopupId() == 'category' && $check == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::CONTENT)) {
                if (!Mage::getStoreConfigFlag('gomage_navigation/contentcolumnsettings/active') 
					|| !Mage::getStoreConfigFlag('gomage_navigation/contentcolumnsettings/show_shopby')
                ) {
                    continue;
                }
            }

            $category = Mage::registry("current_category");
			
            if ($category && in_array($category->getId(), explode(",", $_filter->getCategoryIdsFilter()))) {
                continue;
            }

            if (!Mage::helper('gomage_navigation')->getFilterItemCount($_filter)) {
                continue;
            }

            if ($_filter->getItemsCount() 
				&& ($_filter->getAttributeLocation() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::USE_GLOBAL 
					|| $_filter->getAttributeLocation() == $check)
            ) {
                $i++;
            }
        }

        return ($i) ? floor(100 / $i) . '%' : 0;
    }
	
	/**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
		
        if (!is_array($filters)) {
            $filters = array();
        }

        $allFilters   = $this->getFilters();
        $filterEnable = array();
		
        foreach ($allFilters as $_filter) {
            $filterEnable[$_filter->getName()] = $_filter->ajaxEnabled();
        }

        $activeFilters = array();
		
        foreach ($filters as $filter) {
            if (isset($filterEnable[$filter->getName()])) {
                $filter->setData('ajax_enabled', $filterEnable[$filter->getName()]);
            }

            $activeFilters[] = $filter;
        }

        return $activeFilters;
    }
	
	/**
     * Get url for "clear" link
     * 
     * @return false|string
     */	
    public function getClearLinkUrl($block)
    {
		$filter_model = $block->getFilter();
				
		$filter_request_var	= $filter_model->getRequestVarValue();
		$active_filters		= array();
			
		foreach ($this->getActiveFilters() as $item) {
			$active_filters[] = $item->getFilter()->getRequestVarValue();
		}
		
		if (!in_array($filter_request_var, $active_filters)) {
			return false;
		}
		
		$filter_reset_val	= array(
			$filter_request_var	=> null,
		);
		
		if ($filter_model->hasAttributeModel()) {
			$filter_type = $filter_model->getAttributeModel()->getFilterType();
			
			if (
				in_array(
					$filter_type,
					array(
						GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT,
						GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER,
						GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT,
						GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER
					)
				) &&
				!Mage::helper('gomage_navigation')->isMobileDevice()
			) {
				$filter_reset_val = array(
					$filter_request_var . '_from'	=> null, 
					$filter_request_var . '_to'		=> null
				);
			}
		}
		
		$params = array(
			'_nosid'		=> true,
			'_current'		=> true,
			'_secure'		=> true,
			'_use_rewrite'	=> true,
			'_query'		=> array(
				'ajax'	=> ($block->ajaxEnabled()) ? $block->ajaxEnabled() : null,
			),
			'_escape'		=> false,
			
		);
		
		$params['_query'] = array_merge($params['_query'], $filter_reset_val);
		
        return Mage::helper('gomage_navigation')->getFilterUrl('*/*/*', $params);
    }
}