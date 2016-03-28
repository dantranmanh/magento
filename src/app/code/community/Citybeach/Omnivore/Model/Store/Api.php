<?php
/**
 * User: radu
 * Date: 4/09/15
 */

class Citybeach_Omnivore_Model_Store_Api extends Mage_Core_Model_Store_Api
{

    public function shipping($storeId)
    {
        Mage::log("shipping: storeId = {$storeId}");

        /** @var Mage_Core_Model_Store $store */
        $store = Mage::getModel('core/store')->load($storeId);

        if (!$store->getId())
        {
            throw new Exception("Cannot load store id {$storeId}");
        }

        $result = array();

        $carriers = Mage::getSingleton('shipping/config')->getAllCarriers();
        // $carriers is a map where the key is the carrier "code" and the value is an object that extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
        foreach ($carriers as $key1 => $value1)
        {
            //Mage::log("key = {$key1}, value class is = " . get_class($value1) . ", methods = " . print_r($value1->getAllowedMethods(), true));
            //Mage::log("carrier as array = " . print_r($value1, true));

            $carrier = array();

            $carrier['code'] = $value1->getCarrierCode();
            $carrier['title'] = $value1->getConfigData('title');
            $carrier['methods'] = $value1->getAllowedMethods();


            $result[] = $carrier;
        }

        // Mage::log("shipping: result = " . print_r($result, true));


        return $result;
    }

}
?>
