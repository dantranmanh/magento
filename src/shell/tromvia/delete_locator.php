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
		$collection = Mage::getModel('ak_locator/location')->getCollection();
			$this->showdata('there are '.count($collection).' locations');
			$i=0;
         foreach ($collection as $cl) {
			 $_locator=Mage::getModel('ak_locator/location')->load($cl->getEntityId());
			 $this->showdata('deleting: '.$_locator->getTitle());
            $_locator->delete();$i++;
			$this->showdata('deleted: '.$i.' locations');				
        } 
    }
}

$shell = new Mage_Shell_Compiler();
$shell->run();
