<?php
require_once dirname(__FILE__) . '/../abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Balance_UpdateImage extends Mage_Shell_Abstract
{
   
    public function run()
    {
        $count = 0;
        // $products = Mage::getModel('catalog/product')->getCollection();
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(array('image', 'thumbnail', 'small_image'))
            ->addAttributeToFilter('thumbnail',array('neq'=>'no_selection'));
        foreach ($products as $key => $_product) {
            // $_product = Mage::getModel('catalog/product')->load($_product->getId());            
            $base_image = $_product->getImage();
            if (!$base_image) {
                $media = $_product->getMediaGalleryImages();
                foreach ($media as $key => $_item) {
                    $fileName = $_item->getFile();
                    $_product->setImage($fileName);
                    $_product->setThumbnail($fileName);
                    $_product->setSmallImage($fileName);
                    $_product->save();
                    $count++;
                    break;
                }
            }
        }

        echo "Updated " . $count . " products";
    }


}

$shell = new Mage_Shell_Balance_UpdateImage();
$shell->run();
