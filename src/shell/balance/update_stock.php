<?php
require_once '../abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Compiler extends Mage_Shell_Abstract
{
    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {
        try {
            $allTypes = Mage::app()->useCache();
            foreach($allTypes as $type => $blah) {
                Mage::app()->getCacheInstance()->cleanType($type);
                var_dump($type);
            }
        } catch (Exception $e) {
            // do something
            error_log($e->getMessage());
        }
    }


    public function reset_stock_setting(){
        $_productCollection1 = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id','simple');
        foreach ($_productCollection1 as $product1) {
            $product1=Mage::getModel('catalog/product')->load($product1->getEntityId());
            $this->reset_default_stock_setting($product1);
        }
    }

    /**
     * @param $product
     */
    function reset_default_stock_setting($product){
        $product->setStockData(array(
            'use_config_manage_stock' => 0
        ));
        $product->save();
        $this->showdata('updated product id : '.$product->getEntityId());
    }
}

$shell = new Mage_Shell_Compiler();
$shell->run();
