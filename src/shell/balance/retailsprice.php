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
        $this->updateEnabled();
        $this->updateDisabled();
    }
    public function updateEnabled(){
        $products = Mage::getModel('catalog/product')
          ->getCollection()
          ->addAttributeToSelect('*')
          ->addAttributeToFilter(
          'status',
          array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
          );
        $i=0;
        foreach($products as $product){
            /*if($i > 10) break;*/
            $retails=$product->getRetailPrice();
            if(!empty($retails)){
                $product->setPrice($retails)->save();
                /* $this->showdata('running '.$i);
                 $this->showdata('updating retails price for product :'.$product->getName());*/
                $i++;
            }
        }
        $this->showdata('updated enabled:  '.$i);
    }
    public function updateDisabled(){
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                'status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
            );
        $i=0;
        foreach($products as $product){
            if($i > 10) break;
            $retails=$product->getRetailPrice();
            if(!empty($retails)){
                $product->setPrice($retails)->save();
                /* $this->showdata('running '.$i);
                 $this->showdata('updating retails price for product :'.$product->getName());*/
                $i++;
            }
        }
        $this->showdata('updated disabled:  '.$i);
    }
}

$shell = new Mage_Shell_Compiler();
$shell->run();
