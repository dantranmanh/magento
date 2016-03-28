<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_UrlRewrite
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Block for UrlRedirects edit form and selectors container
 *
 * @category   Enterprise
 * @package    Enterprise_UrlRewrite
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Happytel_Locator_Block_Adminhtml_Location_Import_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Set default params
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'id';
        $this->_controller = 'adminhtml_location_import';
        $this->_blockGroup = 'happytel_locator';
        $this->_headerText = $this->__('Import Location');

        $this->removeButton('delete');
        $this->removeButton('reset');
        $this->_updateButton('save', 'label', $this->__('Import'));
    }

    public function getHeaderText()
    {
        return $this->__('Importing Location');
    }
}
