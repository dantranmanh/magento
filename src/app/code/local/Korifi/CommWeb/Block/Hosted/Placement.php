<?php
class Korifi_CommWeb_Block_Hosted_Placement extends Mage_Core_Block_Template
{
    public function __construct()
    {
    }

    /**
     * Return checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return payment API model
     *
     * @return Appmerce_Migs_Model_Api
     */
    protected function getApi()
    {
        return Mage::getSingleton('commWeb/api_hosted');
    }

    /**
     * Return order instance by lastRealOrderId
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            $order = $this->getOrder();
        }
        elseif ($this->getCheckout()->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        }

        return $order;
    }

    /**
     * Return placement form fields
     *
     * @return array
     */
    public function getFormData()
    {
        return $this->getApi()->getFormFields($this->_getOrder());
    }

    /**
     * Return gateway path from admin settings
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getApi()->getConfigData('vpc_url', $this->_getOrder()->getStoreId());
    }

}
