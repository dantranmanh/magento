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
class Ak_Locator_SearchController extends Mage_Core_Controller_Front_Action
{
    protected $_currentUserLocation;


    /**
     * Handles search requests
     *
     * @return mixed
     * @throws Exception
     */
    public function indexAction()
    {

        try {
            $this->loadLayout();

            /** @var Ak_Locator_Block_Search $search */
            $search = $this->getLayout()->getBlock('search');

            //if search block doesn't know how to handle the parameters, forward onto the search form page
            if (!$search->hasValidParams()) {
                $this->_redirect('*/search/index');
                return;
            }

            //if there are no locations returned go to the noresults action now
            if (!count($search->getLocations()->getItems())) {
                $this->_forward('noresults');
                return;

            }

            if ($this->getRequest()->isXmlHttpRequest() || $this->getRequest()->getParam('xhr') == 1) {
                if (Mage::helper('ak_locator')->browserCacheEnabled()) {
                    $this->_setCacheHeaders();
                }
                $this->getResponse()->setBody($search->asJson());
                return;
            } else {
                $this->renderLayout();
            }

        } catch (Exception $e) {

            if ($e instanceof Ak_Locator_Model_Exception_Geocode
                || $e instanceof Ak_Locator_Model_Exception_NoResults
            ) {
                $this->_forward('noresults');
                return;
            }

            throw $e;
        }
    }

    /**
     * Output info window html for a given location marker
     *
     * @return mixed
     */
    public function infowindowAction()
    {
        $this->loadLayout();
        if (Mage::helper('ak_locator')->browserCacheEnabled()) {
            $this->_setCacheHeaders();
        }
        $this->renderLayout();
    }

    /**
     * Output json object containing info window html for a group of markers
     *
     * @return mixed
     */
    public function infowindowsAction()
    {
        $this->loadLayout();
        if (Mage::helper('ak_locator')->browserCacheEnabled()) {
            $this->_setCacheHeaders();
        }
        $this->renderLayout();
    }

    /**
     * Handle empty result
     *
     * @return mixed
     */
    public function noresultsAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() || $this->getRequest()->getParam('xhr') == 1) {
            $obj = new Varien_Object();
            $obj->setError(true);
            $obj->setErrorType('noresults');
            $obj->setMessage('No Results Found');
            $this->getResponse()->setBody($obj->toJson());
            return;
        } else {
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    /**
     * Handle json data
     *
     * @return mixed
     */
    public function detectAction()
    {
        $this->loadLayout();

        /** @var Ak_Locator_Block_Search $search */
        $search = $this->getLayout()->getBlock('search_detect');

        if ($this->getRequest()->isPost() && $postData = $this->getRequest()->getPost()) {
            if (empty($postData['latitude']) || empty($postData['longitude'])) {
                $this->_redirect('*/*/');
                return;
            }

            Mage::helper('ak_locator')->setOrigins($postData['latitude'], $postData['longitude']);

            if (Mage::helper('ak_locator')->browserCacheEnabled()) {
                $this->_setCacheHeaders();
            }

            $this->getResponse()->setHeader('content-type', 'application/json');
            $this->getResponse()->setBody($search->asDetectJson());

            return;
        }
    }

    /**
     * Set cache headers
     *
     * @return mixed
     */
    protected function _setCacheHeaders()
    {
        $expire = 'Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 days')) . ' GMT';
        header($expire);
        header_remove('Pragma');
        header_remove('Cache-Control');
    }
}
