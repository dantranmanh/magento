<?php

/**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 4.9
 * @since        Class available since Release 1.0
 */
class GoMage_Navigation_Model_Observer
{
    public function loadAttribute($event)
    {
        $attribute    = $event->getAttribute();
        $attribute_id = ( int )$attribute->getAttributeId();

        $data = Mage::getModel('gomage_navigation/attribute')->load($attribute_id)->getData();

        $attribute_option = Mage::getModel('gomage_navigation/attribute_option')
            ->getCollection()
            ->addFieldToFilter('attribute_id', $attribute_id)
            ->load();

        $_option_images = array();
        foreach ($attribute_option as $option) {
            $_option_images[] = $option->getData();
        }

        $option_images = array();

        foreach ($_option_images as $imageInfo) {

            $option_images[$imageInfo['option_id']] = $imageInfo;

        }

        $data['option_images'] = $option_images;

        if ($data && is_array($data) && !empty($data)) {

            $attribute->addData($data);

        }
    }

    public function moveImageFromTmp($file)
    {

        $ioObject      = new Varien_Io_File();
        $destDirectory = Mage::getBaseDir('media') . '/option_image';

        try {
            $ioObject->open(array('path' => $destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path' => $destDirectory));
        }

        if (strrpos($file, '.tmp') == strlen($file) - 4) {
            $file = substr($file, 0, strlen($file) - 4);
        }

        $destFile = Varien_File_Uploader::getNewFileName($file);

        $dest = $destDirectory . '/' . $destFile;

        $ioObject->mv($this->_getMadiaConfig()->getTmpMediaPath($file), $dest);

        return $destFile;
    }

    public function saveAttribute($event)
    {

        $attribute_id        = ( int )$event->getAttribute()->getAttributeId();
        $filter_type         = ( int )$event->getAttribute()->getData('filter_type');
        $inblock_type        = ( int )$event->getAttribute()->getData('inblock_type');
        $round_to            = ( int )$event->getAttribute()->getData('round_to');
        $show_currency       = ( int )$event->getAttribute()->getData('show_currency');
        $image_align         = ( int )$event->getAttribute()->getData('image_align');
        $image_width         = ( int )$event->getAttribute()->getData('image_width');
        $image_height        = ( int )$event->getAttribute()->getData('image_height');
        $show_minimized      = ( int )$event->getAttribute()->getData('show_minimized');
        $show_image_name     = ( int )$event->getAttribute()->getData('show_image_name');
        $visible_options     = ( int )$event->getAttribute()->getData('visible_options');
        $show_help           = ( int )$event->getAttribute()->getData('show_help');
        $show_checkbox       = ( int )$event->getAttribute()->getData('show_checkbox');
        $popup_text          = ( array )$event->getAttribute()->getData('popup_text');
        $popup_width         = ( int )$event->getAttribute()->getData('popup_width');
        $popup_height        = ( int )$event->getAttribute()->getData('popup_height');
        $filter_reset        = ( int )$event->getAttribute()->getData('filter_reset');
        $is_ajax             = ( int )$event->getAttribute()->getData('is_ajax');
        $inblock_height      = ( int )$event->getAttribute()->getData('inblock_height');
        $max_inblock_height  = ( int )$event->getAttribute()->getData('max_inblock_height');
        $filter_button       = ( int )$event->getAttribute()->getData('filter_button');
        $category_ids_filter = trim($event->getAttribute()->getData('category_ids_filter'));
        $attribute_location  = ( int )$event->getAttribute()->getData('attribute_location');

        $range_options = ( int )$event->getAttribute()->getData('range_options');
        $range_auto    = '';
        $range_manual  = trim($event->getAttribute()->getData('range_manual'));
        if ($range_options == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO) {
            $to_value = $event->getAttribute()->getData('to_value');
            $step     = $event->getAttribute()->getData('step');

            foreach ($to_value as $key => $val) {
                $range_auto .= trim($val) . '=' . trim($step[$key]) . ',';
            }
        }

        $attribute = Mage::getModel('gomage_navigation/attribute')->load($attribute_id, 'attribute_id');

        if (!$attribute->getData('attribute_id')) {
            $attribute->setData('attribute_id', $attribute_id);
            $attribute->isObjectNew(true);
        }

        $attribute->addData(array(
                'filter_type'         => $filter_type,
                'inblock_type'        => $inblock_type,
                'round_to'            => $round_to,
                'show_currency'       => $show_currency,
                'image_align'         => $image_align,
                'image_width'         => $image_width,
                'image_height'        => $image_height,
                'show_minimized'      => $show_minimized,
                'show_image_name'     => $show_image_name,
                'show_checkbox'       => $show_checkbox,
                'visible_options'     => $visible_options,
                'show_help'           => $show_help,
                'popup_width'         => $popup_width,
                'popup_height'        => $popup_height,
                'filter_reset'        => $filter_reset,
                'is_ajax'             => $is_ajax,
                'inblock_height'      => $inblock_height,
                'max_inblock_height'  => $max_inblock_height,
                'filter_button'       => $filter_button,
                'category_ids_filter' => $category_ids_filter,
                'range_options'       => $range_options,
                'range_manual'        => $range_manual,
                'range_auto'          => $range_auto,
                'attribute_location'  => $attribute_location)
        );

        $attribute->save();

        foreach ($popup_text as $store_id => $text) {
            $attribute_store = Mage::getModel('gomage_navigation/attribute_store')
                ->getCollection()
                ->addFieldToFilter('attribute_id', $attribute_id)
                ->addFieldToFilter('store_id', $store_id
                )->getFirstItem();
            if (!$attribute_store->getId()) {
                $attribute_store->setData('attribute_id', $attribute_id);
                $attribute_store->setData('store_id', $store_id);
            }

            $attribute_store->setData('popup_text', $text);
            $attribute_store->save();
        }
    }

    public function checkAjax()
    {
        if ($layout = Mage::getSingleton('core/layout')) {
            if (intval(Mage::helper('gomage_navigation')->getRequest()->getParam('ajax'))) {
                $layout->removeOutputBlock('root');
                $layout->removeOutputBlock('core_profiler');

                if (!($productsBlock = $layout->getBlock('search_result_list'))) {
                    $productsBlock = $layout->getBlock('product_list');
                }

                $product_list_html = ($productsBlock ? Mage::getModel('core/url')->sessionUrlVar($productsBlock->toHtml()) : '');

                if (Mage::helper('gomage_navigation/config')->isCMSPage()) {
                    $leftnav_block    = $layout->getBlock('gomage.navigation.cms.left');
                    $rightnav_block   = $layout->getBlock('gomage.navigation.cms.right');
                    $contentnav_block = $layout->getBlock('gomage.navigation.cms.content');
                } else {
                    $leftnav_block    = $layout->getBlock('gomage.navigation.left');
                    $rightnav_block   = $layout->getBlock('gomage.navigation.right');
                    $contentnav_block = $layout->getBlock('gomage.navigation.content');
                }

                $navigation_more_button = $layout->getBlock('gomage.navigation.more.button');

                $navigation_html_left  = '';
                $navigation_html       = '';
                $navigation_html_right = '';

                $is_enterprise    = Mage::helper('gomage_navigation')->isEnterprise();
                $catalogConfigKey = ($is_enterprise) ? 'enterprisecatalog' : 'catalog';

                if ($layout->getBlock('gomage.' . $catalogConfigKey . '.leftnav')) {
                    $navigation_html_left = $layout->getBlock('gomage.' . $catalogConfigKey . '.leftnav');
                }

                if ($navigation_html_left) {
                    $navigation_html_left = Mage::getModel('core/url')->sessionUrlVar($navigation_html_left->toHtml());
                }

                if ($layout->getBlock('gomage.' . $catalogConfigKey . '.content')) {
                    $navigation_html = $layout->getBlock('gomage.' . $catalogConfigKey . '.content');
                }

                if ($navigation_html) {
                    $navigation_html = Mage::getModel('core/url')->sessionUrlVar($navigation_html->toHtml());
                }

                if ($layout->getBlock('gomage.' . $catalogConfigKey . '.right')) {
                    $navigation_html_right = $layout->getBlock('gomage.' . $catalogConfigKey . '.right');
                }

                if ($navigation_html_right) {
                    $navigation_html_right = Mage::getModel('core/url')->sessionUrlVar($navigation_html_right->toHtml());
                }

                $gomage_ajax = Mage::getBlockSingleton('gomage_navigation/ajax');

                $gomage_ajax->addData(array(
                        'navigation_shop_left'  => ($navigation_html_left ? $navigation_html_left : ''),
                        'navigation_shop_right' => ($navigation_html_right ? $navigation_html_right : ''),
                        'navigation'            => $navigation_html,
                        'product_list'          => $product_list_html,
                        'navigation_left'       => ($leftnav_block ? Mage::getModel('core/url')->sessionUrlVar($leftnav_block->toHtml()) : ''),
                        'navigation_right'      => ($rightnav_block ? Mage::getModel('core/url')->sessionUrlVar($rightnav_block->toHtml()) : ''),
                        'navigation_content'    => ($contentnav_block ? Mage::getModel('core/url')->sessionUrlVar($contentnav_block->toHtml()) : ''),
                        'navigation_more'       => ($navigation_more_button ? Mage::getModel('core/url')->sessionUrlVar($navigation_more_button->toHtml()) : ''),

                    )
                );

                if (Mage::getStoreConfig('gomage_procart/general/enable')) {
                    if ($productsBlock) {
                        $gomage_ajax->addEvalJs("if (typeof(GomageProcartConfig) != 'undefined') {
                            gomage_procart_product_list = " . $productsBlock->getProcartProductList() . ";
                            GomageProcartConfig.initialize(gomage_procart_config);
                            };", "eval_js_procart"
                        );
                    }
                }

                $gomage_ajax->setNameInLayout('gomage_ajax');

                Mage::dispatchEvent('gomage_navigation_ajax_result', array('gomage_ajax' => $gomage_ajax));

                $layout->setBlock('gomage_ajax', $gomage_ajax);
                $layout->addOutputBlock($gomage_ajax->getNameInLayout(), 'toJson');
            }

        }

    }

    protected function _getMadiaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }

    static public function checkK($event)
    {

        $key = Mage::getStoreConfig('gomage_activation/advancednavigation/key');

        Mage::helper('gomage_navigation')->a($key);

    }

    public function setContinueShoppingUrl($event)
    {
        $session = Mage::getSingleton('checkout/session');
        $url     = $session->getContinueShoppingUrl();
        $url     = Mage::helper('gomage_navigation/url')->removeRequestParam($url, 'ajax');
        $session->setContinueShoppingUrl($url);
    }

    public function prepareFilterParams(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        $helper = Mage::helper('gomage_navigation');

        if ($helper->isFrendlyUrl()) {

            $request = $action->getRequest();
            $attr    = array();

            $attributes = Mage::getSingleton('catalog/layer')->getFilterableAttributes();

            foreach ($attributes as $attribute) {
                $attr[$attribute->getAttributeCode()]['type'] = $attribute->getBackendType();
                $options                                      = $attribute->getSource()->getAllOptions();
                foreach ($options as $option) {
                    $attr[$attribute->getAttributeCode()]['options'][$helper->formatUrlValue($option['label'], $option['value'])] = $option['value'];
                }
            }
            Mage::register('gan_filter_attributes', $attr);

            if (($layerParams = $request->getQuery()) && !empty($layerParams)) {
                foreach ($layerParams as $param => $value) {
                    if ($param == 'cat') {
                        $values         = explode(',', $value);
                        $prepare_values = array();

                        foreach ($values as $_value) {
                            if ($_value) {
                                if (Mage::getStoreConfigFlag('gomage_navigation/filter_settings/expend_frendlyurl')) {
                                    $_value         = explode('|', $_value);
                                    $parentCategory = Mage::getModel('catalog/category')->loadByAttribute('url_key', $_value[0]);
                                    if ($parentCategory && $parentCategory->getId()) {
                                        if (count($_value) > 1) {
                                            $collectionCategory = Mage::getModel('catalog/category')->getCollection()
                                                ->addAttributeToFilter('url_key', array('eq' => $_value[1]))
                                                ->addAttributeToFilter('parent_id', array('eq' => $parentCategory->getId()));

                                            foreach ($collectionCategory as $category) {
                                                if ($category && $category->getId()) {
                                                    $prepare_values[] = $category->getId();
                                                }
                                            }
                                        } else {
                                            $prepare_values[] = $parentCategory->getId();
                                        }
                                    }
                                } else {
                                    $category = Mage::getModel('catalog/category')->loadByAttribute('url_key', $_value);
                                    if ($category && $category->getId()) {
                                        $prepare_values[] = $category->getId();
                                    }
                                }
                            }
                        }
                        if (!empty($prepare_values)) {
                            $request->setQuery($param, implode(',', $prepare_values));
                        }
                    } elseif (isset($attr[$param]) && !in_array($attr[$param]['type'], array('price', 'decimal'))) {
                        $values         = explode(',', $value);
                        $prepare_values = array();
                        foreach ($values as $_value) {
                            if (isset($attr[$param]['options'][$_value])) {
                                $prepare_values[] = $attr[$param]['options'][$_value];
                            }
                        }
                        if (!empty($prepare_values)) {
                            $request->setQuery($param, implode(',', $prepare_values));
                        }
                    }
                }
            }
        }
    }
}