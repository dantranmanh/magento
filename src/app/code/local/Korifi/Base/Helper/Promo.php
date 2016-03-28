<?php 
class Korifi_Base_Helper_Promo extends Mage_Core_Helper_Abstract
{    
    function isSubscribed()
    {
        return Mage::getStoreConfig('kbase/feed/promo') == 1;
    }
}

?>