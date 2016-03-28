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

    public $_color_label=array('Colours PC-LIGHTUSB-M','Colours HC-G900-77','Colours LC-4S-MSG','Colour LC-G900-WF','Colour HTC ONE','Colours HTC ONE Soft',
        'Colour - LC iPad 3 19','Colour - LC iPad Air 19 Flower','Colours - Mint/Black','Colours - iphone 6 6 plus Walnutt','Colours - Marble','Colours - Denim Options',
        'Colours - Marine Anchor','Colours','Color','Colour','Colours - iPhone 6S Lifeproof Fre','Colours - Oscar Fruit Case','Colours - Oscar Wood','Colour - Oscar Marble','Colour - Oscar Marble Matte','Colour - Oscar Sea',
        'Colour - Vintage Leather','Colours - iPhone 6/ 6S Otterbox Defender'
    );
    public $_patterns=array('Pattern','Patterns');
    public $_patters_colours=array('Patters / Colours');
    public $_g_raft_covers=array('G Raft Covers');
    public $_options=array('Options');
    public $_prints=array('Prints - Oscar Animal','Prints - Luxo Denim','Prints - Oscar Brush','Prints - Oscar Travel','PRINTS - Oscar Universe','Prints - Oscar Landscape');

    public $_supper_attributes_arr=array();
    public $_supper_attributes=array('options','patterns','patters_colours','g_raft_covers','prints','color');


    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {

        $this->image();
    }
    public function image()
    {

        $this->empty_image_configurable_product();

    }


    public function empty_image_configurable_product(){

        $_productCollection1 = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id','configurable');
        foreach ($_productCollection1 as $product1) {
            $product1=Mage::getModel('catalog/product')->load($product1->getEntityId());
            $this->remove_images($product1);
        }
    }
    function remove_images($product){
        Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
        $this->showdata('Deleting images of product : '.$product->getSku());
        if ($product->getId()){
            $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
            $items = $mediaApi->items($product->getId());
            foreach($items as $item)
                $mediaApi->remove($product->getId(), $item['file']);
        }
    }


}

$shell = new Mage_Shell_Compiler();
$shell->run();
