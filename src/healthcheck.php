<?php

require('app/Mage.php');

// Initialise Magento
Mage::app();


// Get a random value for tests
$rand = rand();
$key = 'healthcheck'.$rand;

// Test database works
$product_id = Mage::getModel('catalog/product')->getCollection()->getAllIds(1);
echo 'DB OK'.PHP_EOL;

// Test cache works
$cache = Mage::app()->getCacheInstance();
$cache->save($rand, $key, array(), 5);
if ($cache->load($key) == $rand) {
    echo 'Cache OK'.PHP_EOL;
} else {
    throw new Exception('Cannot read from cache');
}
$cache->remove($key);

// Test sessions work
$session = Mage::getSingleton('core/session');
$session->setData($key, $rand);
if ($session->getData($key) == $rand) {
    echo 'Sessions OK'.PHP_EOL;
} else {
    throw new Exception('Cannot read from session');
}
$session->clear();

echo 'OK';
