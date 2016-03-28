<?php

class Citybeach_Omnivore_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'omnivorepayment';

    protected $_canUseCheckout = false;
    protected $_canUseInternal = false;
    protected $_canUseForMultishipping = false;

    //protected $_infoBlockType = 'citybeach_omnivore/adminhtml_payment';
    protected $_infoBlockType = 'Citybeach_Omnivore_Block_Adminhtml_Payment';

//
//    public function assignData($data)
//    {
//        if ($data instanceof Varien_Object) {
//            $data = $data->getData();
//        }
//
//        $details = array(
//            'component_mode'    => $data['component_mode'],
//            'payment_method'    => $data['payment_method'],
//            'channel_order_id'  => $data['channel_order_id'],
//            'channel_final_fee' => $data['channel_final_fee'],
//            'transactions'      => $data['transactions'],
//            'tax_id'            => isset($data['tax_id']) ? $data['tax_id'] : null,
//        );
//
//        $this->getInfoInstance()->setAdditionalData(serialize($details));
//
//        return $this;
//    }

}