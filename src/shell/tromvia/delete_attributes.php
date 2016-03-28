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
		$attributes=array();
		foreach($attributes as $attr){
			
		}
    }
}

$shell = new Mage_Shell_Compiler();
$shell->run();
