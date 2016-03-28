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
		$_productCollection1 = Mage::getModel('catalog/product')->getCollection();
			$this->showdata('there are '.count($_productCollection1).' product');
			$i=0;
         foreach ($_productCollection1 as $product1) {
			 $product1=Mage::getModel('catalog/product')->load($product1->getEntityId());
			 $this->showdata('deleting: '.$product1->getName());			
			$this->showdata('deleting: '.$product1->getSku());	
            $product1->delete();$i++;
			$this->showdata('deleted: '.$i.' products');				
        } 
    }
}

$shell = new Mage_Shell_Compiler();
$shell->run();
