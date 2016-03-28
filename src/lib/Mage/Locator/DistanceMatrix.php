<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Locator
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://www.magento.com/license/enterprise-edition
 */

/**
 * Distance Matrix
 * Get shortest distance (in km) between two points using Google Maps API
 *
 * @category    Mage
 * @package     Mage_Locator
 * @author      Toan Nguyen <toan.nguyen@balanceinternet.com.au>
 * @license     http://www.magento.com/license/enterprise-edition
 */
class Mage_Locator_DistanceMatrix
{
    const API_URL = 'https://maps.googleapis.com/maps/api/distancematrix/json';
    const API_KEY = 'AIzaSyBYRWUOgxPLuHL2VaUQfFkLR1Hm2jncOrA';
    const STATUS_OK = 'OK';
    const STATUS_ZERO_RESULTS = 'ZERO_RESULTS';
    const STATUS_OVER_QUERY_LIMIT = 'OVER_QUERY_LIMIT';

    /**
     * Call to single API url and return response JSON
     *
     * @param array $parameters cURL parameters
     *
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected static function _doCall($parameters = array())
    {
        // check if curl is available
        if (!function_exists('curl_init')) {
            throw new Mage_Core_Exception('This method requires cURL (http://php.net/curl), it seems like the extension isn\'t installed.');
        }
        // define url
        $url = self::API_URL . '?';
        $api = '&key=' . self::API_KEY;
        // add every parameter to the url
        foreach ($parameters as $key => $value) {
            $url .= $key . '=' . urlencode($value) . '&';
        }
        // trim last &
        $url = trim($url, '&');
        // init curl
        $curl = curl_init();
        // set options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        }
        // execute
        $response = curl_exec($curl);
        // fetch errors
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);
        // close curl
        curl_close($curl);
        // we have errors
        if ($errorNumber != '') {
            throw new Mage_Core_Exception($errorMessage);
        }
        // redefine response as json decoded
        $response = json_decode($response);
        // return the content
        return $response;
    }

    /**
     * Call to multiple API urls and return response JSON
     *
     * @param array $parameters cURL parameters
     *
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _doMultiCall($parameters = array())
    {
        // define url
        $url = self::API_URL . '?';
        $destinations = array();
        $curls = array();
        // add every parameter to the url
        foreach ($parameters as $key => $value) {
            if ($key === 'origins') {
                $url .= $key . '=' . urlencode($value) . '&';
            } elseif ($key === 'destinations') {
                $destinations = $this->_getDestinations($value);
            }
        }
        // trim last &
        $url = trim($url, '&');

        $curls = $this->_buildMultiRequest($url, $destinations);

        // execute
        $response = $this->_multiRequest($curls);
        Zend_Debug::dump($response);

        return $response;
    }

    /**
     * Multi-Call to API url and return response
     *
     * @param array $data    Request URL
     * @param array $options cURL options
     *
     * @return mixed
     */
    protected function _multiRequest($data, $options = array())
    {
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();

        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($data as $id => $d) {

            $curly[$id] = curl_init();

            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, 0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

            // post?
            if (is_array($d)) {
                if (!empty($d['post'])) {
                    curl_setopt($curly[$id], CURLOPT_POST, 1);
                    curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
                }
            }

            // extra options?
            if (!empty($options)) {
                curl_setopt_array($curly[$id], $options);
            }

            curl_multi_add_handle($mh, $curly[$id]);
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);


        // get content and remove handles
        foreach ($curly as $id => $c) {
            $result[$id] = curl_multi_getcontent($c);
            curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);

        return $result;
    }

    /**
     * @param       $postUrl
     * @param array $destinations
     *
     * @return array
     */
    protected function _buildMultiRequest($postUrl, $destinations = array())
    {
        $result = array();
        $url = $postUrl;

        for ($i = 0, $j = count($destinations); $i < $j; $i++) {
            $result[$i] = trim($url . '&destinations' . '=' . urlencode($destinations[$i]), '&');
        }

        return $result;
    }

    /**
     * Build destinations string array meet Google API limits (25 destinations)
     *
     * @param string $data Destination
     *
     * @return array
     */
    protected function _getDestinations($data)
    {
        $destinations = array_chunk(explode('|', $data), 25);
        $result = array();

        for ($i = 0, $j = count($destinations); $i < $j; $i++) {
            $result[$i] = implode('|', $destinations[$i]);
        }

        return $result;
    }

    /**
     * Return distance value (in km)
     *
     * @param string $origins      Origins lat/lng
     * @param string $destinations Destination lat/lng
     *
     * @return object
     * @throws Mage_Core_Exception
     */
    public static function getDistance($origins, $destinations)
    {
        // define results
        $results = self::_doCall(array(
            'origins'      => $origins,
            'destinations' => $destinations
        ));

        $rows = $results->rows;
        $elements = array_key_exists(0, $results) ? $rows[0]->elements : null;
        $distance = null;

        if ($results->status !== self::STATUS_OVER_QUERY_LIMIT) {
            if ($elements[0]->status === self::STATUS_OK) {
                $distance = array_key_exists(0, $elements) ? round(($elements[0]->distance->value / 1000), 1) : null;
            } elseif ($elements[0]->status === self::STATUS_ZERO_RESULTS) {
                $distance = 9999999999;
            }
        } else {
            $distance = 0;
        }

        return $distance;
    }
}