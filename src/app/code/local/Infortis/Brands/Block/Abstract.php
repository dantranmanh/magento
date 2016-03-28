<?php

/**
 * Class Infortis_Brands_Block_Abstract
 */
class Infortis_Brands_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * @var Infortis_Brands_Helper_Data
     */
    protected $_helper;
    /**
     * @var Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected $_attributeModel = null;
    /**
     * @var
     */
    protected $_brandImagePath;
    /**
     * @var
     */
    protected $_brandOptionImagePath;
    /**
     * @var
     */
    protected $_urlKeySeperator;
    /**
     * @var
     */
    protected $_imgUrlKeySeperator;

    /**
     * Constructor
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('brands');
        $this->_brandImagePath = $this->_helper->getBrandImagePath();
        $this->_brandOptionImagePath = $this->_helper->getBrandOptionImagePath();
        $this->_urlKeySeperator = trim($this->_helper->getCfg('general/url_key_separator'));
        $this->_imgUrlKeySeperator = trim($this->_helper->getCfg('general/img_url_key_separator'));
        $this->_getAttributeModel();
    }

    /**
     * @return false|Mage_Eav_Model_Entity_Attribute_Abstract|null
     */
    protected function _getAttributeModel()
    {
        if (null === $this->_attributeModel) {
            $this->_attributeModel = Mage::getSingleton('eav/config')
                ->getAttribute('catalog_product', $this->getBrandAttributeId());
        }
        return $this->_attributeModel;
    }

    /**
     * @return mixed
     */
    public function getBrandAttributeId()
    {
        return $this->_helper->getCfg('general/attr_id');
    }

    /**
     * @return mixed
     */
    public function getBrandAttributeTitle()
    {
        return $this->_attributeModel->getStoreLabel();
    }

    /**
     * Get brand name of current product
     *
     * @param Mage_Catalog_Model_Product $product Current product
     *
     * @return string
     */
    public function getBrand($product)
    {
        $attribute = $product->getResource()->getAttribute($this->getBrandAttributeId());
        return trim($attribute->getFrontend()->getValue($product));
    }

    /**
     * @param bool $gomage If this mode enable, then use option_image directory instead default directory
     *
     * @return string
     */
    protected function _getBrandImageBaseDir($gomage = false)
    {
        $imgPath = ($gomage) ? $this->_brandOptionImagePath : $this->_brandImagePath;
        return Mage::getBaseDir('media') . DS . $imgPath;
    }

    /**
     * @param bool $gomage If this mode enable, then use option_image directory instead default directory
     *
     * @return string
     */
    protected function _getBrandImageBaseUrl($gomage = false)
    {
        $imgPath = ($gomage) ? $this->_brandOptionImagePath : $this->_brandImagePath;
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $imgPath;
    }

    /**
     * Get brand image url
     *
     * @param string $brand Brand name
     *
     * @return string
     */
    public function getBrandImageUrl($brand)
    {
        $optionId = $this->_attributeModel->getSource()->getOptionId($brand);
        $brandAttrOption = Mage::getModel('gomage_navigation/attribute_option')->load($optionId);
        $imgExtension = trim($this->_helper->getCfg('general/image_extension'));
        $brandOptionImg = $brandAttrOption->getData('filename');
        $brandDefaultImg = $this->getBrandUrlKey($brand, $this->_imgUrlKeySeperator) . '.' . $imgExtension;
        if ($brandOptionImg !== null && file_exists($this->_getBrandImageBaseDir(true) . $brandOptionImg)) {
            return $this->_getBrandImageBaseUrl(true) . $brandOptionImg;
        } elseif (file_exists($this->_getBrandImageBaseDir() . $brandDefaultImg)) {
            return $this->_getBrandImageBaseUrl() . $brandDefaultImg;
        } else {
            return '';
        }
    }

    /**
     * Get brand page url
     *
     * @param string $brand Brand name
     *
     * @return string
     */
    public function getBrandPageUrl($brand)
    {
        $brandPageUrl = '';
        $linkToSearch = $this->_helper->getCfgLinkToSearch();
        if ($linkToSearch == 3) {
            $brandPageUrl = '';
        } elseif ($linkToSearch == '2') {
            $brandAttributeId = $this->getBrandAttributeId();
            $optionId = $this->_attributeModel->getSource()->getOptionId($brand);
            $brandPageUrl = Mage::getBaseUrl() . 'catalogsearch/advanced/result/?' . $brandAttributeId . urlencode('[]')
                . '=' . $optionId;
        } elseif ($linkToSearch == 1) {
            $brandPageUrl = Mage::getBaseUrl() . 'catalogsearch/result/?q=' . str_replace("\040", "\x2b", $brand);
        } elseif ($linkToSearch == '0') {
            $brandUrlKey = $this->getBrandUrlKey($brand, $this->_urlKeySeperator);
            $pageBasePath = trim($this->_helper->getCfg('general/page_base_path'), ' /');
            if ($pageBasePath !== '') {
                $pageBasePath .= '/';
            }
            $brandPageUrl = Mage::getBaseUrl() . $pageBasePath . $brandUrlKey;
            if ($this->_helper->getCfg('general/append_category_suffix')) {
                $brandPageUrl .= Mage::getStoreConfig('catalog/seo/category_url_suffix');
            }
        }
        return $brandPageUrl;
    }

    /**
     * Get brand url key
     *
     * @param string $brand     Brand name
     * @param string $seperator String seperator
     *
     * @return mixed|string
     */
    public function getBrandUrlKey($brand, $seperator)
    {
        return $this->_formatBrandUrlKey($brand, $seperator);
    }

    /**
     * Format brand url key
     *
     * @param string $brand     Brand name
     * @param string $seperator String seperator
     *
     * @return mixed|string
     */
    protected function _formatBrandUrlKey($brand, $seperator)
    {
        $urlKey = Mage::helper('catalog/product_url')->format($brand);
        $newUrlKey = preg_replace('#[^0-9a-z]+#i', $seperator, $urlKey);
        $newUrlKey = strtolower($newUrlKey);
        $newUrlKey = trim($newUrlKey, $seperator);
        return $newUrlKey;
    }
}
