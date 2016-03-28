<?php

/**
 * User: radu
 * Date: 19/08/15
 */
class Citybeach_Omnivore_Model_Order
{

    public $storeId;
    public $guestCheckout;

    // the data structure parsed from the JSON string;
    public $json;

    // some strings we need for shipping, but we compute in validate()
    private $carrierCode, $carrierTitle, $methodCode, $methodTitle;

    /** @var $quote Mage_Sales_Model_Quote */
    private $quote = NULL;

    /** @var $order Mage_Sales_Model_Order */
    private $order = NULL;

    // TODO: custom options
    private $additionalData = array();


    public function __construct()
    {
    }

    public function __toString()
    {
        //return print_r($this, true);
        return "orderNumber = {$this->json->marketplaceOrderNumber}";
    }

    public function validate()
    {
        /** @var Mage_Core_Model_Store $store */
        $store = Mage::getModel('core/store')->load($this->storeId);

        if (!$store->getId())
        {
            throw new Exception("Cannot load store id {$this->storeId}");
        }

        if ($this->json->currency != $store->getBaseCurrency()->getCurrencyCode())
        {
            throw new Exception("Order currency {$this->json->currency} is different than store base currency {$store->getBaseCurrency()->getCurrencyCode()}");
        }

        // shippingMethod is carrierCode_methodCode, eg: flatrate_flatrate or ups_GND
        // NOTE: some methods have an underscore or more in the code, eg: fedex_STANDARD_OVERNIGHT, usps_INT_10
        $pos = strpos($this->json->shippingMethod, "_");

        if ($pos === false)
        {
            throw new Exception("Cannot find an underscore character in the shipping method");
        }

        $carrierCode = substr($this->json->shippingMethod, 0, $pos);
        $methodCode = substr($this->json->shippingMethod, $pos + 1, strlen($this->json->shippingMethod));

        //Mage::log("carrierCode = {$carrierCode}, methodCode = {$methodCode}");

        $found = false;

        $carriers = Mage::getSingleton('shipping/config')->getAllCarriers();
        // $carriers is a map where the key is the carrier "code" and the value is an object that extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
        foreach ($carriers as $key1 => $value1)
        {
            //Mage::log("key = {$key1}, value class is = " . get_class($value1) . ", methods = " . print_r($value1->getAllowedMethods(), true));
            //Mage::log("carrier as array = " . print_r($value1, true));

            if ($carrierCode == $value1->getCarrierCode())
            {
                // found a matching carrier, let's check the method
                $methods = $value1->getAllowedMethods();

                // $methods is a map where the key is the method "code" and the value is a display label, apparently
                foreach ($methods as $key2 => $value2)
                {
                    if ($methodCode == $key2)
                    {
                        $found = true;

                        $this->carrierCode = $carrierCode;
                        $this->carrierTitle = $value1->getConfigData('title');
                        $this->methodCode = $methodCode;
                        $this->methodTitle = $value2;
                    }
                }
            }
        }

        if (!$found)
        {
            throw new Exception("Cannot find a carrier {$carrierCode} with method {$methodCode}");
        }

        //throw new Exception("Validation failed for marketplace {$this->json->marketplaceCode} order {$this->json->marketplaceOrderNumber}");
    }

    public function persist()
    {
        try
        {
            $this->createMagentoQuote();
            $this->createMagentoOrder();
        }
        catch (Exception $ex)
        {
            //Mage::log("exception = {$ex}");
            error_log("exception = {$ex}");

            $this->quote->setIsActive(false)->save();

            throw $ex;
        }
    }

    public function getOrderId()
    {
        if ($this->order == null)
        {
            //Mage::log("returning *null*");
            return null;
        }
        else
        {
            //Mage::log("getOrderId: returning increment id = {$this->order->getIncrementId()} for order id = {$this->order->getId()}");
            return $this->order->getIncrementId();
        }
    }

    public function createMagentoQuote()
    {
        try
        {
            $this->setupQuote();
            $this->setupCustomer();
            $this->setupAddresses();
            $this->setupTaxCalculation();
            $this->setupCurrency();
            $this->setupQuoteItems();
            $this->setupPaymentData();

            $this->quote->collectTotals();

            $this->quote->save();

            //Mage::log("quote id = " . $this->quote->getId());
        }
        catch (Exception $ex)
        {
            error_log("exception = {$ex}");

            //Mage::log("exception = {$ex}");
            //Mage::log("quote = " . print_r($this->quote, true));

            $this->quote->setIsActive(false)->save();
            throw $ex;
        }
    }

    private function setupQuote()
    {
        /** @var Mage_Core_Model_Store $store */
        $store = Mage::getModel('core/store')->load($this->storeId);

        $this->quote = Mage::getModel('sales/quote');

        $this->quote->setCheckoutMethod('todo');
        $this->quote->setStore($store);
        $this->quote->setQuoteCurrencyCode($this->json->currency);

        //$this->quote->getStore()->setData('current_currency', $this->quote->getStore()->getBaseCurrency());

        $this->quote->save();

        Mage::getSingleton('checkout/session')->replaceQuote($this->quote);
    }

    private function setupCustomer()
    {

        if ($this->guestCheckout)
        {
            $this->quote
                ->setCustomerId(null)
                ->setCustomerEmail($this->json->customer->email)
                ->setCustomerFirstname($this->json->customer->firstName)
                ->setCustomerLastname($this->json->customer->lastName)
                ->setCustomerIsGuest(true)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }
        else
        {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = null;

            $customers = Mage::getModel('customer/customer')->getCollection();
            $customers->addFieldToFilter('email', $this->json->customer->email);

            if ($customers->count() > 0)
            {
                $customer = $customers->getFirstItem();
                //Mage::log("Found existing customer id = {$customer->getId()}");
                //$this->quote->assignCustomer($customer);
            }
            else
            {
                /** @var Mage_Core_Model_Store $store */
                $store = Mage::getModel('core/store')->load($this->storeId);

                $password = Mage::helper('core')->getRandomString(12);

                $customer = Mage::getModel('customer/customer')
                    ->setData('firstname', $this->json->billingAddress->firstName)
                    ->setData('lastname', $this->json->billingAddress->lastName)
                    ->setData('website_id', $store->getWebsiteId())
                    ->setData('email', $this->json->customer->email)
                    ->setData('confirmation', $password);

                $customer->setPassword($password);
                $customer->save();

                //Mage::log("Created new customer id = {$customer->getId()}");

                $street = '';
                if ($this->json->shippingAddress->line2 != '')
                {
                    $street = $this->json->shippingAddress->line1 . ', ' . $this->json->shippingAddress->line2;
                }
                else
                {
                    $street = $this->json->shippingAddress->line1;
                }

                /** @var Mage_Customer_Model_Address $customerAddress */
                $customerAddress = Mage::getModel('customer/address')
                    ->setData('firstname', $this->json->shippingAddress->firstName)
                    ->setData('lastname', $this->json->shippingAddress->lastName)
                    ->setData('street', $street)
                    ->setData('city', $this->json->shippingAddress->city)
                    ->setData('country_id', $this->json->shippingAddress->countryCode)
                    ->setData('postcode', $this->json->shippingAddress->postcode)
                    ->setData('telephone', $this->json->shippingAddress->phone)
                    ->setData('email', $this->json->customer->email)
                    ->setData('region', $this->json->shippingAddress->state)
                    //->setData('region_id', $this->json->shippingAddress->state)
                    ->setCustomerId($customer->getId())
                    ->setIsDefaultBilling(true)
                    ->setIsDefaultShipping(true);

                $customerAddress->implodeStreetAddress();
                $customerAddress->save();
            }

            if (is_null($customer))
            {
                throw new Exception("Cannot find or create the customer");
            }
            else
            {
                //Mage:log("Setting customer id = {$customer->getId()}");
                $this->quote
                    ->setCustomerId($customer->getId())
                    ->setCustomerEmail($this->json->customer->email)
                    ->setCustomerFirstname($this->json->customer->firstName)
                    ->setCustomerLastname($this->json->customer->lastName)
                    ->setCustomerIsGuest(false)
                    ->setCustomerGroupId(Mage_Customer_Model_Group::CUST_GROUP_ALL); // TODO???
            }
        }

    }

    private function setupAddresses()
    {
        // Billing:

        $billingAddressData = array();
        $billingAddressData['firstname'] = $this->json->billingAddress->firstName;
        $billingAddressData['lastname'] = $this->json->billingAddress->lastName;
        if ($this->json->billingAddress->line2 != '')
        {
            $billingAddressData['street'] = $this->json->billingAddress->line1 . ', ' . $this->json->billingAddress->line2;
        }
        else
        {
            $billingAddressData['street'] = $this->json->billingAddress->line1;
        }

        $billingAddressData['city'] = $this->json->billingAddress->city;
        $billingAddressData['region'] = $this->json->billingAddress->state;
        //$billingAddressData['region_id'] = $this->json->billingAddress->state;
        $billingAddressData['country_id'] = $this->json->billingAddress->countryCode;
        $billingAddressData['postcode'] = $this->json->billingAddress->postcode;
        $billingAddressData['telephone'] = $this->json->billingAddress->phone;
        $billingAddressData['email'] = $this->json->customer->email;

        $billingAddress = $this->quote->getBillingAddress();
        $billingAddress->addData($billingAddressData);
        $billingAddress->implodeStreetAddress();

        // looks like billing address doesn't need a shipping method
        $billingAddress->setShippingMethod(null);
        $billingAddress->setCollectShippingRates(false);
        $billingAddress->setShouldIgnoreValidation(true);

        // Shipping:

        $shippingAddressData = array();
        $shippingAddressData['firstname'] = $this->json->shippingAddress->firstName;
        $shippingAddressData['lastname'] = $this->json->shippingAddress->lastName;

        if ($this->json->shippingAddress->line2 != '')
        {
            $shippingAddressData['street'] = $this->json->shippingAddress->line1 . ', ' . $this->json->shippingAddress->line2;
        }
        else
        {
            $shippingAddressData['street'] = $this->json->shippingAddress->line1;
        }

        $shippingAddressData['city'] = $this->json->shippingAddress->city;
        $shippingAddressData['region'] = $this->json->shippingAddress->state;
        //$shippingAddressData['region_id'] = $this->json->shippingAddress->state;
        $shippingAddressData['country_id'] = $this->json->shippingAddress->countryCode;
        $shippingAddressData['postcode'] = $this->json->shippingAddress->postcode;
        $shippingAddressData['telephone'] = $this->json->shippingAddress->phone;
        $shippingAddressData['email'] = $this->json->customer->email;

        $shippingAddress = $this->quote->getShippingAddress();
        $shippingAddress->setSameAsBilling(0);
        $shippingAddress->addData($shippingAddressData);
        $shippingAddress->implodeStreetAddress();

        // database values seem to be of the form carrierCode underscore methodCode, eg: flatrate_flatrate or ups_GND
        $shippingAddress->setShippingMethod($this->json->shippingMethod);
        $shippingAddress->setCollectShippingRates(false);

        // Mage::log("shippingAddress id = " . $shippingAddress->getId());

        /** @var Mage_Sales_Model_Quote_Address_Rate $rate */
        $rate = Mage::getModel('sales/quote_address_rate');

        $rate->setCode($this->json->shippingMethod);
        $rate->setCarrier($this->carrierCode);
        $rate->setCarrierTitle($this->carrierTitle);
        $rate->setMethod($this->methodCode);
        $rate->setMethodTitle($this->methodTitle);
        $rate->setPrice($this->json->shippingPrice);

        $shippingAddress->addShippingRate($rate);

        //Mage::log("rate = {$rate}");
    }

    private function setupCurrency()
    {

        $currentCurrency = Mage::getModel('directory/currency')->load($this->json->currency);
        $this->quote->getStore()->setData('current_currency', $currentCurrency);

        //Mage::log("initializeCurrency: store base currency = " . $this->quote->getStore()->getBaseCurrency());
        //Mage::log("initializeCurrency: store current currency = " . $this->quote->getStore()->getCurrentCurrency());

        if ($this->json->currency != $this->quote->getStore()->getBaseCurrency()->getCurrencyCode())
        {
            throw new Exception("Order currency {$this->json->currency} is different than store currency {$this->quote->getStore()->getBaseCurrency()->getCurrencyCode()}");
        }

    }


    private function setupTaxCalculation()
    {
        // see Mage_Tax_Model_Calculation::setCustomer()
        Mage::getSingleton('tax/calculation')->setCustomer($this->quote->getCustomer());
    }


    private function setupQuoteItems()
    {
        //$prods = $this->json->products;
        //Mage::log("initializeQuoteItems: prods = " . $prods);

        foreach ($this->json->products as $lineItem)
        {
            // a lineItem has these properties: sku, variantCode, variantName, externalId, quantity, unitPrice

            //Mage::log("initializeQuoteItems: item = " . print_r($item, true));
            $productId = $lineItem->externalId;

            $this->clearQuoteItemsCache();

            /** @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('catalog/product')->load($productId);

            //Mage::log("setupQuoteItems: Magento product id = {$product->getId()}, sku = {$product->getSku()}");

            // TODO: add product options to $request

            $request = new Varien_Object();
            $request->setQty($lineItem->quantity);

            $productOriginalPrice = (float)$product->getPrice();

            $price = $lineItem->unitPrice;
            $product->setPrice($price);
            $product->setSpecialPrice($price);

            // see Mage_Sales_Model_Observer::substractQtyFromQuotes
            $this->quote->setItemsCount($this->quote->getItemsCount() + 1);
            $this->quote->setItemsQty((float)$this->quote->getItemsQty() + $request->getQty());

            $result = $this->quote->addProduct($product, $request);
            if (is_string($result))
            {
                throw new Exception($result);
            }

            $quoteItem = $this->quote->getItemByProduct($product);
            //Mage::log(" ***** quoteItem = " . $quoteItem);

            if ($quoteItem !== false)
            {
                $weight = $product->getTypeInstance()->getWeight();
                if ($product->isConfigurable())
                {
                    $simpleProductId = $product->getCustomOption('simple_product')->getProductId();
                    $weight = Mage::getResourceModel('catalog/product')->getAttributeRawValue(
                        $simpleProductId, 'weight', 0
                    );
                }

                $quoteItem->setStoreId($this->quote->getStoreId());
                $quoteItem->setOriginalCustomPrice($lineItem->unitPrice);
                $quoteItem->setOriginalPrice($productOriginalPrice);
                $quoteItem->setBaseOriginalPrice($productOriginalPrice);
                $quoteItem->setWeight($weight);
                $quoteItem->setNoDiscount(1);

            }
        }
    }

    private function clearQuoteItemsCache()
    {
        foreach ($this->quote->getAllAddresses() as $address)
        {
            /** @var $address Mage_Sales_Model_Quote_Address */

            $address->unsetData('cached_items_all');
            $address->unsetData('cached_items_nominal');
            $address->unsetData('cached_items_nonominal');
        }
    }

    private function setupPaymentData()
    {
        $quotePayment = $this->quote->getPayment();
        $paymentData = array();

        $paymentData['method'] = 'omnivorepayment';

        $payment = $this->json->payment;

        if ($payment->paymentType == 'paypal')
        {
            $paymentData['po_number'] = $payment->transactionId . ' : ' . $payment->paypalPayerId;
            $paymentData['additional_information'] = 'PayPal Transaction: ' . $payment->transactionId . ', PayPal Payer Id: ' . $payment->paypalPayerId .
                ', Amount: ' . $payment->amount . ', Currency: ' . $payment->currency;
        }
        else
        {
            // don't really expect to be here anytime soon
            $paymentData['po_number'] = $payment->transactionId;
        }

        $quotePayment->importData($paymentData);
    }

    private function createMagentoOrder()
    {
        try
        {
            // NOTE: we don't support Magento versions < 1.5.0

            /** @var $service Mage_Sales_Model_Service_Quote */
            $service = Mage::getModel('sales/service_quote', $this->quote);
            $service->setOrderData($this->additionalData);
            $service->submitAll();

            $this->order = $service->getOrder();

            $this->quote->setIsActive(false)->save();
        }
        catch (Exception $e)
        {
            $this->quote->setIsActive(false)->save();
            throw $e;
        }
    }

}