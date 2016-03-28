<?php

/**
 * Location extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://andrewkett.github.io/Ak_Locator/
 */
class Ak_Locator_Helper_Data extends Ak_Locator_Helper_Abstract
{
    protected $_origins;

    const BROWSER_CACHE_CONFIG_PATH = 'locator_settings/search/leverage_browser_caching';
    const DISTANCE_API_KEY_CONFIG_PATH = 'locator_settings/google_maps/distance_api_key';
    const DS = '|';

    /**
     * Return available location attribute form as select options
     *
     * @throws Mage_Core_Exception
     * @return mixed
     */
    public function getAttributeFormOptions()
    {
        Mage::throwException(Mage::helper('ak_locator')->__('Use helper with defined EAV entity'));
    }


    /**
     * Default attribute entity type code
     *
     * @throws Mage_Core_Exception
     * @return mixed
     */
    protected function _getEntityTypeCode()
    {
        Mage::throwException(Mage::helper('ak_locator')->__('Use helper with defined EAV entity'));
    }

    /**
     * Return available location attribute form as select options
     *
     * @return array
     */
    public function getLocationAttributeFormOptions()
    {
        return Mage::helper('ak_locator/location')->getAttributeFormOptions();
    }

    /**
     * Returns array of user defined attribute codes for location entity type
     *
     * @return array
     */
    public function getLocationUserDefinedAttributeCodes()
    {
        return Mage::helper('ak_locator/location')->getUserDefinedAttributeCodes();
    }


    /**
     * Is browser caching enabled for searches
     *
     * @return bool
     */
    public function browserCacheEnabled()
    {
        return (bool)Mage::getStoreConfig(self::BROWSER_CACHE_CONFIG_PATH);
    }

    /**
     * Get Google Distance Matrix API key
     *
     * @return string
     */
    public function getGoogleDistanceApiKey()
    {
        return Mage::getStoreConfig(self::DISTANCE_API_KEY_CONFIG_PATH);
    }

    /**
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getBreadcrumbPath()
    {
        $path = array();

        if ($id = Mage::app()->getRequest()->getParam('id')) {
            $locations = Mage::getModel('ak_locator/location')->getCollection()
                ->addAttributeToSelect('title')
                ->addAttributeToFilter('entity_id', $id)
                ->load();
            $items = $locations->getItems();
            $location = reset($items);
            $path['location_detail'] = array(
                'label' => $location->getTitle(),
                'link'  => ''
            );
        }
        return $path;
    }

    /**
     * Add distance to collection
     *
     * @param Ak_Locator_Model_Location $collection Location collection
     * @return mixed
     */
    public function addDistance($collection)
    {
        foreach ($collection as $_location) {
            $destinations = $this->_toGoogleApiFormat($_location->getLatitude(), $_location->getLongitude());
            $_distance = $this->_calculateDistance($this->getOrigins(), $destinations);
            // @TODO: Handle multiple request with cURL for best performance pratices
            // $_distance = Mage_Locator_DistanceMatrix::getDistance($this->getOrigins(), $destinations);
            $_location->setData('distance', $_distance);
        }
    }

    /**
     * Get origins
     *
     * @return mixed
     */
    public function getOrigins()
    {
        return Mage::getSingleton('core/session')->getOrigins();
    }

    /**
     * Set origins
     *
     * @param string $latitude  Latitude
     * @param string $longitude Longitude
     *
     * @return mixed
     */
    public function setOrigins($latitude, $longitude)
    {
        Mage::getSingleton('core/session')->setOrigins($this->_toGoogleApiFormat($latitude, $longitude));
    }

    /**
     * Check if current coordinates exists
     *
     * @return bool
     */
    public function findCurrentCoordinates()
    {
        $result = false;

        if (Mage::getSingleton('core/session')->getOrigins()) {
            $result = true;
        }

        return $result;
    }

    /**
     * Combine latitude and longitude to Google API parameter format
     *
     * @param string $latitude  Latitude
     * @param string $longitude Longitude
     *
     * @return string
     */
    protected function _toGoogleApiFormat($latitude, $longitude)
    {
        return $latitude . ',' . $longitude;
    }

    /**
     * Using Haversine formular to calculate Great Distance
     *
     * @param string $origins      Origin lat/lng
     * @param string $destinations Destination lat/lng
     *
     * @return float
     */
    protected function _calculateDistance($origins, $destinations)
    {
        $origins = explode(',', $origins);
        $destinations = explode(',', $destinations);
        $earthRadius = 6371000;
        $alpha = deg2rad($origins[0]);
        $beta = deg2rad($destinations[0]);
        $gamma = deg2rad($destinations[0] - $origins[0]);
        $delta = deg2rad($destinations[1] - $origins[1]);

        $epsilon = sin($gamma / 2) * sin($gamma / 2) + cos($alpha) * cos($beta) * sin($delta / 2) * sin($delta / 2);
        $zeta = 2 * atan2(sqrt($epsilon), sqrt(1 - $epsilon));
        $distance = $earthRadius * $zeta;

        return round(($distance/1000), 1);
    }
}
