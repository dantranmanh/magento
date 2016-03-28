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
class GoMage_Navigation_Block_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    /**
     * Set model name
     */
    public function __construct()
    {
        parent::__construct();

        $this->_filterModelName = 'gomage_navigation/catalog_layer_filter_attribute';
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
                if (Mage::helper('gomage_navigation/configurableswatches')->isSwatchAttribute($this->getAttributeModel()->getId())) {
                    $this->_template = 'gomage/navigation/layer/filter/swatches.phtml';
                } else {
                    $this->_template = 'gomage/navigation/layer/filter/default.phtml';
                }
                break;
        }
    }

    public function getFilter()
    {
        return $this->_filter;
    }

    public function getPopupId()
    {
        return $this->getAttributeModel()->getAttributeCode();
    }

    public function ajaxEnabled()
    {
        return (int)$this->getAttributeModel()->getIsAjax();
    }

    public function canShowMinimized($side)
    {
        $helper = Mage::helper('gomage_navigation');

        if ('true' === $helper->getRequest()->getParam($this->_filter->getRequestVar() . '-' . $side . '_is_open')) {
            return false;
        } elseif ('false' === $helper->getRequest()->getParam($this->_filter->getRequestVar() . '-' . $side . '_is_open')) {
            return true;
        }

        return (bool)($this->getAttributeModel()->getShowMinimized() > 0);

    }

    public function getShowAllOptions()
    {
        $helper = Mage::helper('gomage_navigation');

        if ('true' === $helper->getRequest()->getParam($this->_filter->getRequestVar() . '_show_all')) {
            return true;
        } elseif ('false' === $helper->getRequest()->getParam($this->_filter->getRequestVar() . '_show_all')) {
            return false;
        }

        return false;
    }

    public function getVisibleOptions()
    {
        return (int)$this->getAttributeModel()->getVisibleOptions();
    }

    public function canShowPopup()
    {
        return (bool)($this->getAttributeModel()->getShowHelp() > 0);
    }

    public function getPopupText()
    {
        return trim($this->getAttributeModel()->getPopupText());
    }

    public function getCategoryIdsFilter()
    {
        return trim($this->getAttributeModel()->getCategoryIdsFilter());
    }

    public function getAttributeLocation()
    {
        return trim($this->getAttributeModel()->getAttributeLocation());
    }

    public function getPopupWidth()
    {
        return (int)$this->getAttributeModel()->getPopupWidth();
    }

    public function getPopupHeight()
    {
        return (int)$this->getAttributeModel()->getPopupHeight();
    }

    public function canShowCheckbox()
    {
        return $this->getAttributeModel()->getShowCheckbox();
    }

    public function canShowLabels()
    {
        return (bool)$this->getAttributeModel()->getShowImageName();
    }

    public function getImageWidth()
    {
        return (int)$this->getAttributeModel()->getImageWidth();
    }

    public function getImageHeight()
    {
        return (int)$this->getAttributeModel()->getImageHeight();
    }

    public function getImageAlign()
    {
        switch ($this->getAttributeModel()->getImageAlign()) {
            case (1) :
                $image_align = 'horizontally';
                break;

            case (2):
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
        return (bool)($this->getAttributeModel()->getFilterReset() > 0);
    }

    public function getFilterType()
    {
        return $this->getAttributeModel()->getFilterType();
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

    public function isActiveFilter($label)
    {
        return false;
    }
}
