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
class GoMage_Navigation_Block_Catalog_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{
    protected $_block_side = null;
	
	/**
     * Set model name
     */
    public function __construct()
    {		
        parent::__construct();
		
        $this->_filterModelName = 'gomage_navigation/catalog_layer_filter_category';
    }
	
	protected function _prepareFilter()
    {
        parent::_prepareFilter();
		
        switch ($this->getFilterType()) {
            case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_IMAGE) :
                $this->_template = 'gomage/navigation/layer/filter/image.phtml';
            break;
			
            case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DROPDOWN) :
                $this->_template = 'gomage/navigation/layer/filter/dropdown.phtml';
            break;
			
			default :
                $this->_template = 'gomage/navigation/layer/filter/category/default.phtml';
            break;
        }
    }
	
	/*****/
	
	public function getFilter()
    {
        return $this->_filter;
    }
	
    /**
     * Initialize filter template
     *
     */
    public function setBlockSide($block_side)
    {
        $this->_block_side = $block_side;
    }

    public function getBlockSide()
    {
        if ($this->_block_side) {
            return $this->_block_side;
        }

        return GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::LEFT_BLOCK;
    }

    public function getConfigTab()
    {
        switch ($this->getBlockSide()) {
            case (GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::LEFT_BLOCK) :
                $tab = 'category';
            break;
			
            case (GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::RIGHT_BLOCK) :
                $tab = 'rightcolumnsettings';
                break;
            case (GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::CONTENT) :
                $tab = 'contentcolumnsettings';
            break;
				
			default :
                $tab = 'category';
            break;
        }

        return $tab;
    }
	
    public function getItems()
    {
        if (Mage::getStoreConfigFlag('gomage_navigation/category/active')) {
            if (!$this->ajaxEnabled()) {
                $items = parent::getItems();
				
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
        return 'category';
    }

    public function ajaxEnabled()
    {
        return (int) Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/ajax_enabled');
    }

    public function canShowMinimized($side)
    {
        $helper = Mage::helper('gomage_navigation');

        if ('true' === $helper->getRequest()->getParam('cat' . '-' . $side . '_is_open')) {
            return false;
        } elseif ('false' === $helper->getRequest()->getParam('cat' . '-' . $side . '_is_open')) {
            return true;
        }

        return (bool) Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/show_minimized');
    }

    public function canShowPopup()
    {
        return (bool )Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/show_help');
    }

    public function getPopupText()
    {
        return trim(Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/popup_text'));
    }

    public function getPopupWidth()
    {
        return (int) Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/popup_width');
    }

    public function getPopupHeight()
    {
        return (int) Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/popup_height');
    }

    public function canShowCheckbox()
    {
        if (Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/active')) {
            return (bool) Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/show_checkbox');
        }
    }

    public function canShowLabels()
    {
        return (bool) Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/show_image_name');
    }

    public function getImageWidth()
    {
        return (int) Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/image_width');
    }

    public function getImageHeight()
    {
        return (int) Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/image_height');
    }

    public function getImageAlign()
    {
        switch (Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/image_align')) { 
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
        return (bool) Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/filter_reset');
    }

    public function getFilterType()
    {
        return Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/filter_type');
    }

    public function getInBlockHeight()
    {
        return Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/inblock_height');
    }

    public function getInblockType()
    {
        return Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/inblock_type');
    }

    public function getMaxInBlockHeight()
    {
        return Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/max_inblock_height');
    }
	
	public function isActiveFilter($label)
    {
        return false;
    }
}
