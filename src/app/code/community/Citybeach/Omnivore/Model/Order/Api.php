<?php
class Citybeach_Omnivore_Model_Order_Api extends Mage_Checkout_Model_Cart_Api
{

    public function json2order($jsonOrder, $storeId, $guestCheckout)
    {
        //Mage::log(__FILE__);
        //Mage::log("createQuoteAndOrder: storeId = {$storeId}, json = {$jsonOrder}");


        /** @var  $order Citybeach_Omnivore_Model_Order */
        $order = Mage::getModel('citybeach_omnivore/order');

        $order->json = json_decode($jsonOrder);
        $order->storeId = $storeId;
        $order->guestCheckout = $guestCheckout;

        $order->validate();

        $order->persist();

        return $order->getOrderId();
    }

}
?>
