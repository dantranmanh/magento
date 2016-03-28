<?php

class Infortis_Brands_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get path of the directory with default brand images
     *
     * @return string
     */
    public function getBrandImagePath()
    {
        return 'wysiwyg/infortis/brands';
    }

    /**
     * Get path of the directory with user upload images
     *
     * @return string
     */
    public function getBrandOptionImagePath()
    {
        return 'option_image/';
    }

    /**
     * Get module settings
     *
     * @param string $optionString Config string
     *
     * @return string
     */
    public function getCfg($optionString)
    {
        return Mage::getStoreConfig('brands/' . $optionString);
    }

    /**
     * Get config flag: show brand image
     *
     * @return string
     */
    public function isShowImage()
    {
        return Mage::getStoreConfig('brands/general/show_image');
    }

    /**
     * Get config flag: show brand name (simple text) if brand image doesn't exist
     *
     * @return string
     */
    public function isShowImageFallbackToText()
    {
        return Mage::getStoreConfig('brands/general/show_image_fallback_to_text');
    }

    /**
     * Get config: logo is a link to search results
     *
     * @return string
     */
    public function getCfgLinkToSearch()
    {
        return Mage::getStoreConfig('brands/general/link_search_enabled');
    }
}
