<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Happytel
 * @package     Happytel_ConfigurableSwatches
 * @copyright   Copyright (c) 2016 Balance Internet Pty,. Ltd.
 * @license     http://www.magento.com/license/enterprise-edition
 */

/**
 * Class Happytel_ConfigurableSwatches_Helper_Productimg
 */
class Happytel_ConfigurableSwatches_Helper_Productimg extends Mage_ConfigurableSwatches_Helper_Productimg
{
    /**
     * @var Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected $_attributeModel = null;

    const SWATCH_GOMAGE_MEDIA_DIR = 'option_image';

    /**
     * @return false|Mage_Eav_Model_Entity_Attribute_Abstract|null
     */
    protected function _getAttributeModel()
    {
        if (null === $this->_attributeModel) {
            $this->_attributeModel = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'color');
        }
        return $this->_attributeModel;
    }

    /**
     * Return the appropriate swatch URL for the given value (matches against product's image labels)
     *
     * @param Mage_Catalog_Model_Product $product         Product object
     * @param string                     $value           Swatch label
     * @param int                        $width           Swatch image width
     * @param int                        $height          Swatch image height
     * @param string                     $swatchType      Swatch type
     * @param string                     $fallbackFileExt Swatch file extension
     *
     * @return string
     */
    public function getSwatchUrl(
        $product,
        $value,
        $width = self::SWATCH_DEFAULT_WIDTH,
        $height = self::SWATCH_DEFAULT_HEIGHT,
        &$swatchType,
        $fallbackFileExt = null
    ) {
        $url = '';
        $swatchType = 'none';

        // Get the (potential) swatch image that matches the value
        $image = $this->getProductImgByLabel($value, $product, 'swatch');

        // Check in swatch directory if $image is null
        if (is_null($image)) {
            $optionId = $this->_getAttributeModel()->getSource()->getOptionId($value);
            $colorAttrOption = Mage::getModel('gomage_navigation/attribute_option')->load($optionId);
            $filename = $colorAttrOption->getData('filename');
            if (!is_null($filename)) {
                $swatchImage = $this->_resizeSwatchImage($filename, 'gomage', $width, $height);
                $swatchType = 'media';
                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $swatchImage;
            } else {
                // Check if file exists in fallback directory
                $fallbackUrl = $this->getGlobalSwatchUrl($product, $value, $width, $height, $fallbackFileExt);
                if (!empty($fallbackUrl)) {
                    $url = $fallbackUrl;
                    $swatchType = 'media';
                }
            }
        }

        // If we still don't have a URL or matching product image, look for one that matches just
        // the label (not specifically the swatch suffix)
        if (empty($url) && is_null($image)) {
            $image = $this->getProductImgByLabel($value, $product, 'standard');
        }

        if (!is_null($image)) {
            $filename = $image->getFile();
            $swatchImage = $this->_resizeSwatchImage($filename, 'product', $width, $height);
            $swatchType = 'product';
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $swatchImage;
        }

        return $url;
    }

    /**
     * Performs the resize operation on the given swatch image file and returns a
     * relative path to the resulting image file
     *
     * @param string $filename
     * @param string $tag
     * @param int    $width
     * @param int    $height
     *
     * @return string
     */
    protected function _resizeSwatchImage($filename, $tag, $width, $height)
    {
        // Form full path to where we want to cache resized version
        $destPathArr = array(
            self::SWATCH_CACHE_DIR,
            Mage::app()->getStore()->getId(),
            $width . 'x' . $height,
            $tag,
            trim($filename, '/'),
        );

        $destPath = implode('/', $destPathArr);

        // Check if cached image exists already
        if (!file_exists(Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $destPath)) {
            // Check for source image
            if ($tag == 'product') {
                $sourceFilePath = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath() . $filename;
            } elseif ($tag === 'gomage') {
                $sourceFilePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                    . DS . self::SWATCH_GOMAGE_MEDIA_DIR . DS . $filename;
            } else {
                $sourceFilePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA)
                    . DS . self::SWATCH_FALLBACK_MEDIA_DIR . DS . $filename;
            }

            if (!file_exists($sourceFilePath)) {
                return false;
            }

            // Do resize and save
            $processor = new Varien_Image($sourceFilePath);
            $processor->resize($width, $height);
            $processor->save(Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $destPath);
            Mage::helper('core/file_storage_database')->saveFile($destPath);
        }

        return $destPath;
    }
}
