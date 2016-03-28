<?php
$this->startSetup();
Korifi_Base_Helper_Module::baseModuleInstalled();
$feedData = array();
$feedData[] = array(
    'severity'      => 4,
    'date_added'    => gmdate('Y-m-d H:i:s', time()),
    'title'         => 'JET`s extension has been installed. Remember to flush all cache, recompile, log-out and log back in.',
    'description'   => 'You can see versions of the installed extensions right in the admin, as well as configure notifications about major updates.',
    'url'           => 'http://www.jetextension.com'
);
Mage::getModel('adminnotification/inbox')->parse($feedData);
$this->endSetup();