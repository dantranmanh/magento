<?php
$installer = $this;

$installer->startSetup();

/* $enStore = Mage::app()->getStore('default');
// For creating a block for a store

$array_cms_block = array (
    'terms-conditions-repair-quote-voucher'=>array('title'=>'Happytel Voucher Terms & Condition', 'stores'=> array($enStore->getId())),
);
foreach($array_cms_block as $cms_block => $cms_block_info) {
    $page = array (
        'identifier' => $cms_block,
        'title' => $cms_block_info['title'],
        'root_template' => 'one_column',
        'stores' => $cms_block_info['stores'],
        'content' => file_get_contents(__DIR__ .
        '/cms_block/0.1.0/'.$cms_block.'.html')
    );  
    $cmsPage = Mage::getModel('cms/page')->load($page['identifier'], 'identifier');
    if($cmsPage->getId()) {
        array_shift($cmsPage);
        $cmsPage->addData($page)->save();
    } else {
        Mage::getModel('cms/page')->setData($page)->save();
    }
} */

$installer->endSetup();
