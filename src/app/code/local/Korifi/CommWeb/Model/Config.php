<?php
class Korifi_CommWeb_Model_Config extends Mage_Payment_Model_Config
{
    // Default order statuses
    const DEFAULT_STATUS_NEW = 'pending';
    const DEFAULT_STATUS_PENDING_PAYMENT = 'pending_payment';
    const DEFAULT_STATUS_PROCESSING = 'processing';

    /**
     * Get store configuration
     */
    public function getPaymentConfigData($method, $key, $storeId = null)
    {
        return Mage::getStoreConfig('payment/' . $method . '/' . $key, $storeId);
    }

    /**
     * Get response
     */
    function getResponseDescription($responseCode)
    {
        switch ($responseCode) {
            case '0' :
                $result = 'Transaction Successful';
                break;
            case '?' :
                $result = 'Transaction status is unknown';
                break;
            case '1' :
                $result = 'Unknown Error';
                break;
            case '2' :
                $result = 'Bank Declined Transaction';
                break;
            case '3' :
                $result = 'No Reply from Bank';
                break;
            case '4' :
                $result = 'Expired Card';
                break;
            case '5' :
                $result = 'Insufficient funds';
                break;
            case '6' :
                $result = 'Error Communicating with Bank';
                break;
            case '7' :
                $result = 'Payment Server System Error';
                break;
            case '8' :
                $result = 'Transaction Type Not Supported';
                break;
            case '9' :
                $result = 'Bank declined transaction (Do not contact Bank)';
                break;
            case 'A' :
                $result = 'Transaction Aborted';
                break;
            case 'C' :
                $result = 'Transaction Cancelled';
                break;
            case 'D' :
                $result = 'Deferred transaction has been received and is awaiting processing';
                break;
            case 'F' :
                $result = '3D Secure Authentication failed';
                break;
            case 'I' :
                $result = 'Card Security Code verification failed';
                break;
            case 'L' :
                $result = 'Shopping Transaction Locked (Please try the transaction again later)';
                break;
            case 'N' :
                $result = 'Cardholder is not enrolled in Authentication scheme';
                break;
            case 'P' :
                $result = 'Transaction has been received by the Payment Adaptor and is being processed';
                break;
            case 'R' :
                $result = 'Transaction was not processed - Reached limit of retry attempts allowed';
                break;
            case 'S' :
                $result = 'Duplicate SessionID (OrderInfo)';
                break;
            case 'T' :
                $result = 'Address Verification Failed';
                break;
            case 'U' :
                $result = 'Card Security Code Failed';
                break;
            case 'V' :
                $result = 'Address Verification and Card Security Code Failed';
                break;
            default :
                $result = 'Unable to be determined';
        }
        return Mage::helper('commWeb')->__($result);
    }

    /**
     * Return order description
     *
     * @param Mage_Sales_Model_Order
     * @return string
     */
    public function getOrderDescription($order)
    {
        return Mage::helper('commWeb')->__('Order %s', $order->getIncrementId());
    }

    /**
     * Functions for default new/pending/processing statuses
     */
    public function getOrderStatus($code)
    {
        $status = $this->getPaymentConfigData($code, 'order_status');
        if (empty($status)) {
            $status = self::DEFAULT_STATUS_PENDING;
        }
        return $status;
    }

    public function getPendingStatus($code)
    {
        $status = $this->getPaymentConfigData($code, 'pending_status');
        if (empty($status)) {
            $status = self::DEFAULT_STATUS_PENDING_PAYMENT;
        }
        return $status;
    }

    public function getProcessingStatus($code)
    {
        $status = $this->getPaymentConfigData($code, 'processing_status');
        if (empty($status)) {
            $status = self::DEFAULT_STATUS_PROCESSING;
        }
        return $status;
    }

}
