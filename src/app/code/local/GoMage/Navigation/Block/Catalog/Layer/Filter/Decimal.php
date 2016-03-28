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
class GoMage_Navigation_Block_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Block_Layer_Filter_Decimal
{
    /**
     * Initialize Decimal Filter Model
     */
    public function __construct()
    {		
        parent::__construct();
		
        $this->_filterModelName = 'gomage_navigation/catalog_layer_filter_decimal';
    }

	protected function _prepareFilter()
    {
        parent::_prepareFilter();

		switch ($this->getFilterType()) {
			case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT) :
				$this->_template = 'gomage/navigation/layer/filter/input.phtml';
			break;

			case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER) :
				if (Mage::helper('gomage_navigation')->isMobileDevice()) {
					$this->_template = 'gomage/navigation/layer/filter/default.phtml';
				} else {
					$this->_template = 'gomage/navigation/layer/filter/slider.phtml';
				}
			break;

			case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT) :
				if (Mage::helper('gomage_navigation')->isMobileDevice()) {
					$this->_template = 'gomage/navigation/layer/filter/default.phtml';
				} else {
					$this->_template = 'gomage/navigation/layer/filter/slider-input.phtml';
				}
			break;

			case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER) :
				if (Mage::helper('gomage_navigation')->isMobileDevice()) {
					$this->_template = 'gomage/navigation/layer/filter/default.phtml';
				} else {
					$this->_template = 'gomage/navigation/layer/filter/input-slider.phtml';
				}
			break;

			case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DROPDOWN) :
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
	
	public function getFilterType()
    {
        return $this->getAttributeModel()->getFilterType();
    }
	
	public function ajaxEnabled()
    {
        return (int) $this->getAttributeModel()->getIsAjax();
    }
	
	public function canShowMinimized($side)
    {
        $helper = Mage::helper('gomage_navigation');

        if ('true' === $helper->getRequest()->getParam($this->_filter->getRequestVar() . '-' . $side . '_is_open')) {
            return false;
        } elseif ('false' === $helper->getRequest()->getParam($this->_filter->getRequestVar() . '-' . $side . '_is_open')) {
            return true;
        }

        return (bool) $this->getAttributeModel()->getShowMinimized();
    }
	
	public function canShowCheckbox()
    {
        return (bool) $this->getAttributeModel()->getShowCheckbox();
    }
	
	public function getPopupId()
    {
        return $this->getAttributeModel()->getAttributeCode();
    }
	
    public function canShowPopup()
    {
        return (bool) $this->getAttributeModel()->getShowHelp();
    } 
	
	public function getPopupWidth()
    {
        return (int) $this->getAttributeModel()->getPopupWidth();
    }

    public function getPopupHeight()
    {
        return (int) $this->getAttributeModel()->getPopupHeight();
    }
	
    public function getPopupText()
    {
        return trim($this->getAttributeModel()->getPopupText());
    }

	public function canShowResetFirler()
    {
        return (bool) $this->getAttributeModel()->getFilterReset();
    }

    public function getCategoryIdsFilter()
    {
        return trim($this->getAttributeModel()->getCategoryIdsFilter());
    }

    public function getAttributeLocation()
    {
        return trim($this->getAttributeModel()->getAttributeLocation());
    }

    public function canShowLabels()
    {
        return (bool) $this->getAttributeModel()->getShowImageName();
    }
	
    public function getInBlockHeight()
    {
        return $this->getAttributeModel()->getInblockHeight();
    }

    public function getInblockType()
    {
        return $this->getAttributeModel()->getInblockType();
    }

    public function getMaxInBlockHeight()
    {
        return $this->getAttributeModel()->getMaxInblockHeight();
    }

    public function canShowFilterButton()
    {
        return (bool) $this->getAttributeModel()->getFilterButton();
    }
	
	public function isActiveFilter($label)
    {
        return false;
    }
}
