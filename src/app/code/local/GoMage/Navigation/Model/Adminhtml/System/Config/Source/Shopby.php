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
 * @since        Class available since Release 3.0
 */
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    const USE_GLOBAL = 0;
    const CONTENT = 1;
    const RIGHT_COLUMN = 2;
    const LEFT_COLUMN_CONTENT = 3;
    const RIGHT_COLUMN_CONTENT = 4;
    const LEFT_COLUMN = 5;

    public function toOptionArray()
    {
        $helper = Mage::helper('gomage_navigation');

        return array(
            array('value' => self::CONTENT, 'label' => $helper->__('Content')),
            array('value' => self::LEFT_COLUMN, 'label' => $helper->__('Left Column')),
            array('value' => self::RIGHT_COLUMN, 'label' => $helper->__('Right Column')),
            array('value' => self::LEFT_COLUMN_CONTENT, 'label' => $helper->__('Left Column and Content')),
            array('value' => self::RIGHT_COLUMN_CONTENT, 'label' => $helper->__('Right Column and Content')),
        );

    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionHash()
    {
        $helper = Mage::helper('gomage_navigation');

        return array(
            self::CONTENT              => $helper->__('Content'),
            self::LEFT_COLUMN          => $helper->__('Left Column'),
            self::RIGHT_COLUMN         => $helper->__('Right Column'),
            self::LEFT_COLUMN_CONTENT  => $helper->__('Left Column and Content'),
            self::RIGHT_COLUMN_CONTENT => $helper->__('Right Column and Content'),
        );
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {
        $helper = Mage::helper('gomage_navigation');

        if (!$this->_options) {
            $this->_options = array(
                array('value' => self::USE_GLOBAL, 'label' => $helper->__('Use Global')),
                array('value' => self::CONTENT, 'label' => $helper->__('Content')),
                array('value' => self::LEFT_COLUMN, 'label' => $helper->__('Left Column')),
                array('value' => self::RIGHT_COLUMN, 'label' => $helper->__('Right Column')),
                array('value' => self::LEFT_COLUMN_CONTENT, 'label' => $helper->__('Left Column and Content')),
                array('value' => self::RIGHT_COLUMN_CONTENT, 'label' => $helper->__('Right Column and Content')),
            );
        }

        return $this->_options;
    }

    public function getAllOptionHash()
    {
        $helper = Mage::helper('gomage_navigation');

        return array(
            self::USE_GLOBAL           => $helper->__('Use Global'),
            self::CONTENT              => $helper->__('Content'),
            self::LEFT_COLUMN          => $helper->__('Left Column'),
            self::RIGHT_COLUMN         => $helper->__('Right Column'),
            self::LEFT_COLUMN_CONTENT  => $helper->__('Left Column and Content'),
            self::RIGHT_COLUMN_CONTENT => $helper->__('Right Column and Content'),
        );
    }
}