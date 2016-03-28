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
    public $_categories=array();
    public $_inputFile = 'input.csv';
    public $_outputFile="output.csv";
    public $_productFile="product.csv";
    public $_imgFile="image.csv";
    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                'status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            );
        $i=0;
        foreach($products as $product){
            $productId = $product->getEntityId();
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);

            $this->showdata($stockItem->getManageStock());
            if ($stockItem->getId() > 0 and $stockItem->getManageStock()) {
                $this->showdata('running '.$i);
                $this->showdata('updating inventory for product :'.$product->getName());
                $qty = 9999;
                $stockItem->setQty($qty);
                $stockItem->setIsInStock((int)($qty > 0));
                $stockItem->save();
            }
            $i++;
        }

    }


}

$shell = new Mage_Shell_Compiler();
$shell->run();
