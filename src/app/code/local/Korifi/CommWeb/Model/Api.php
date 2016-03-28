<?php
class Korifi_CommWeb_Model_Api extends Mage_Payment_Model_Method_Abstract
{
    // Magento features
    protected $_isGateway = false;
    protected $_canOrder = false;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
    protected $_isInitializeNeeded = true;
    protected $_canFetchTransactionInfo = false;
    protected $_canReviewPayment = false;
    protected $_canCreateBillingAgreement = false;
    protected $_canManageRecurringProfiles = false;

    // Special feature: we do NOT store Cc data in Magento
    protected $_canSaveCc = false;

    // Restrictions
    protected $_allowCurrencyCode = array();

    // Local constants
    const COMMAND_PAY = 'pay';
    const COMMAND_CAPTURE = 'capture';

    const TRANSACTION_SOURCE_INTERNET = 'INTERNET';
    const TRANSACTION_SOURCE_MAILORDER = 'MAILORDER';
    const TRANSACTION_SOURCE_TELORDER = 'TELORDER';

    const SOURCE_SUBTYPE_SINGLE = 'SINGLE';
    const SOURCE_SUBTYPE_INSTALLMENT = 'INSTALLMENT';
    const SOURCE_SUBTYPE_RECURRING = 'RECURRING';

    const MAESTRO_CHQ = 'CHQ';
    const MAESTRO_SAV = 'SAV';

    const VPC_VERSION = '1';

    public function __construct()
    {
        $this->_config = Mage::getSingleton('commWeb/config');
        return $this;
    }

    /**
     * Return configuration instance
     *
     * @return Appmerce_Migs_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Validate if payment is possible
     *  - check allowed currency codes
     *
     * @return bool
     */
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getCurrencyCode();
        if (!empty($this->_allowCurrencyCode) && !in_array($currency_code, $this->_allowCurrencyCode)) {
            $errorMessage = Mage::helper('commWeb')->__('Selected currency (%s) is not compatible with this payment method.', $currency_code);
            Mage::throwException($errorMessage);
        }
        return $this;
    }

    /**
     * Get redirect URL after placing order
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getApiUrl('placement');
    }

    /**
     * Decide currency code type
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return Mage::app()->getStore()->getBaseCurrencyCode();
    }

    /**
     * Decide grand total
     *
     * @param $order Mage_Sales_Model_Order
     */
    public function getGrandTotal($order)
    {
        // For Migs: always use BaseGrandTotal
        // Currency is set at Merchant account level
        $amount = $order->getBaseGrandTotal();
        return round($amount * 100);
    }

    /**
     * Cut query ?___SID from URL
     *
     * Ogone can't handle any ?query= after an URL
     * It breaks the SHA Signature
     *
     * @param $url String
     * @return $url without ?query
     */
    public function _cutQueryFromUrl($url)
    {
        $explode = explode('?', $url);
        return $explode[0];
    }

    /**
     * Post with CURL and return response
     *
     * @param $postUrl The URL with ?key=value
     * @param $postData string Message
     * @return reponse XML Object
     */
    public function curlPost($postUrl, $postData = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($postData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$postData");
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setCcType($data->getCcType())->setCcOwner($data->getCcOwner())->setCcLast4(substr($data->getCcNumber(), -4))->setCcNumber($data->getCcNumber())->setCcCid($data->getCcCid())->setCcExpMonth($data->getCcExpMonth())->setCcExpYear($data->getCcExpYear())->setCcSsIssue($data->getCcSsIssue())->setCcSsStartMonth($data->getCcSsStartMonth())->setCcSsStartYear($data->getCcSsStartYear());
        return $this;
    }

    /**
     * Prepare info instance for save
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function prepareSave()
    {
        $info = $this->getInfoInstance();
        Mage::getSingleton('core/session')->setCcCidEnc($info->encrypt($info->getCcCid()));
        Mage::getSingleton('core/session')->setCcNumberEnc($info->encrypt($info->getCcNumber()));
        $info->setCcNumber(null)->setCcCid(null);
        return $this;
    }

}
