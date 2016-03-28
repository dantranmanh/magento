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
 * @since        Class available since Release 3.2
 */
class GoMage_Navigation_Block_Enterprise_Search_Catalog_Layer_Filter_Stock extends Mage_Catalog_Block_Layer_Filter_Abstract
{	
	/**
     * Set model name
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->_filterModelName = 'gomage_navigation/enterprise_search_catalog_layer_filter_stock';  
    }
	
	protected function _prepareFilter()
    {
        parent::_prepareFilter();
		
		switch (Mage::getStoreConfig('gomage_navigation/stock/filter_type')) {
			case GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_IMAGE :
				$this->_template = 'gomage/navigation/layer/filter/image.phtml';
			break;

			case GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DROPDOWN :
				$this->_template = 'gomage/navigation/layer/filter/dropdown.phtml';
			break;
			
			default :
				$this->_template = 'gomage/navigation/layer/filter/default.phtml';
			break;
		}	
    }
	
	/*****/
	
	public function getFilter()
    {
        return $this->_filter;
    }

    public function getItems()
    {
        if (Mage::getStoreConfigFlag('gomage_navigation/stock/active')) {
            if (!$this->ajaxEnabled()) {
                $items = parent::getItems();;

                foreach ($items as $key => $item) {
                    if ($category = Mage::getModel('catalog/category')->load($item->getValue())) {
                        $items[$key]->setUrl($category->getUrl());
                    }
                }

                return $items;
            }
        }

        return parent::getItems();
    }

    public function getPopupId()
    {
        return 'stock_status';
    }

    public function ajaxEnabled()
    {
        return (int) Mage::getStoreConfigFlag('gomage_navigation/stock/ajax_enabled');
    }

    public function canShowMinimized($side)
    {
        $helper = Mage::helper('gomage_navigation');

        if ($helper->getRequest()->getParam('stock_status' . '-' . $side . '_is_open')) {
            if ('true' === $helper->getRequest()->getParam('stock_status' . '-' . $side . '_is_open')) {
                return false;
            } elseif ('false' === $helper->getRequest()->getParam('stock_status' . '-' . $side . '_is_open')) {
                return true;
            }
        }

        return (bool) Mage::getStoreConfigFlag('gomage_navigation/stock/show_minimized');
    }

    public function getAttributeLocation()
    {
        return Mage::getStoreConfig('gomage_navigation/stock/attribute_location');
    }

    public function canShowPopup()
    {
        return (bool) Mage::getStoreConfigFlag('gomage_navigation/stock/show_help');
    }

    public function getPopupText()
    {
        return trim(Mage::getStoreConfig('gomage_navigation/stock/popup_text'));
    }

    public function getPopupWidth()
    {
        return (int) Mage::getStoreConfig('gomage_navigation/stock/popup_width');
    }

    public function getPopupHeight()
    {
        return (int) Mage::getStoreConfig('gomage_navigation/stock/popup_height');
    }

    public function canShowCheckbox()
    {
        return (bool) Mage::getStoreConfigFlag('gomage_navigation/stock/show_checkbox');
    }

    public function canShowLabels()
    {
        return (bool) Mage::getStoreConfigFlag('gomage_navigation/stock/show_image_name');
    }

    public function getImageWidth()
    {
        return (int) Mage::getStoreConfig('gomage_navigation/stock/image_width');
    }

    public function getImageHeight()
    {
        return (int) Mage::getStoreConfig('gomage_navigation/stock/image_height');
    }

    public function getImageAlign()
    {
        switch (Mage::getStoreConfig('gomage_navigation/stock/image_align')) {
            case (1) :
                $image_align = 'horizontally';
            break;

            case (2) :
                $image_align = '2-columns';

            break;
				 
			default :
                $image_align = 'default';
            break;
		}

        return $image_align;
    }

    public function canShowResetFirler()
    {
        return false;
    }

    public function getFilterType()
    {
        return Mage::getStoreConfig('gomage_navigation/stock/filter_type');
    }

    public function getInBlockHeight()
    {
        return Mage::getStoreConfig('gomage_navigation/stock/inblock_height');
    }

    public function getInblockType()
    {
        return Mage::getStoreConfig('gomage_navigation/stock/inblock_type');
    }

    public function getMaxInBlockHeight()
    {
        return Mage::getStoreConfig('gomage_navigation/stock/max_inblock_height');
    }

    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        
		return $this;
    }
	
	public function isActiveFilter($label)
    {
        return false;
    }
}
