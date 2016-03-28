<?php
class Korifi_CommWeb_Controller_Common extends Mage_Core_Controller_Front_Action
{
    /**
     * Return checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return order process instance
     *
     * @return Appmerce_Migs_Model_Process
     */
    public function getProcess()
    {
        return Mage::getSingleton('commWeb/process');
    }

    /**
     * Return order instance by LastOrderId
     *
     * @return  Mage_Sales_Model_Order object
     */
    protected function getLastRealOrder()
    {
        $order = Mage::getModel('sales/order');
        $order->load($this->getCheckout()->getLastRealOrderId(), 'increment_id');
        return $order;
    }

    /**
     * Debug IN
     */
    public function saveDebugIn($in)
    {
        if ($this->getApi()->getConfigData('debug_flag')) {
            $url = $this->getRequest()->getPathInfo();
            $data = print_r($in, true);
            Mage::getModel('migs/api_debug')->setDir('in')->setUrl($url)->setData('data', $data)->save();
        }
    }

    /**
     * Save checkout session
     */
    public function saveCheckoutSession()
    {
        $this->getCheckout()->setMigsQuoteId($this->getCheckout()->getLastSuccessQuoteId());
        $this->getCheckout()->setMigsOrderId($this->getCheckout()->getLastOrderId(true));
    }

}
