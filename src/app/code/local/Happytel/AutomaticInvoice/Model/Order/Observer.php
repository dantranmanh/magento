<?php
/**
 * AutomaticInvoice Extensions created by Balance Internet
 *
 * NOTICE OF LICENSE
 *
 * This source file is licensed of Balance Internet.
 * All this code is used for Balance Internet properties
 *
 * @category     Happytel
 * @package      Happytel_AutomaticInvoice
 * @copyright    Copyright (c) 2016 Balance Internet
 * @license      http://www.balanceinternet.com.au/
 * @developer    Toan Nguyen (toan.nguyen@balanceinternet.com.au)
 */

/**
 * Class Happytel_AutomaticInvoice_Model_Observer
 *
 * @category     Happytel
 * @package      Happytel_AutomaticInvoice
 * @copyright    Copyright (c) 2016 Balance Internet
 * @license      http://www.balanceinternet.com.au/
 * @developer    Toan Nguyen (toan.nguyen@balanceinternet.com.au)
 */
class Happytel_AutomaticInvoice_Model_Order_Observer
{
    /**
     * Send out emails when payment code is 'directdeposit_au'
     *
     * @param Varien_Event_Observer $observer Order
     *
     * @return void
     */
    public function sendOrderConfirmationEmail(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment();
        $paymentCode = $payment->getMethodInstance()->getCode();

        if ($paymentCode === 'directdeposit_au') {
            $this->_sendNewOrderEmail($order);
        }
    }

    /**
     * @param Mage_Sales_Model_Order $orderObj Order object
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _sendNewOrderEmail($orderObj, $notifyCustomer = true)
    {
        $storeId = $orderObj->getStore()->getId();

        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(Mage_Sales_Model_Order::XML_PATH_EMAIL_COPY_TO, $storeId);
        $copyMethod = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $orderObj;
        }

        // Start store emulation process
        /** @var $appEmulation Mage_Core_Model_App_Emulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($orderObj->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($orderObj->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $orderObj->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $orderObj->getCustomerName();
        }

        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            /** @var $emailInfo Mage_Core_Model_Email_Info */
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($orderObj->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                /** @var $emailInfo Mage_Core_Model_Email_Info */
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(
            array(
                'order'        => $orderObj,
                'billing'      => $orderObj->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        $mailer->send();
        $orderObj->setEmailSent(true);

        return $orderObj;
    }

    protected function _getEmails($configPath, $storeId)
    {
        $data = Mage::getStoreConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }
}
