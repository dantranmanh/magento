<?php
/**
 * Location extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://andrewkett.github.io/Ak_Locator/
 */

class Happytel_Locator_Block_Adminhtml_Location extends Ak_Locator_Block_Adminhtml_Location
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_location';
        $this->_headerText = Mage::helper('happytel_locator')->__('Manage Locations');
        $this->_blockGroup = 'happytel_locator';
        $this->_addButtonLabel = Mage::helper('happytel_locator')->__('Add New Location');

        $this->_addButton('import', array(
            'label'     => Mage::helper('happytel_locator')->__('Import Locations'),
            'onclick'   => 'setLocation(\'' . $this->getImportUrl() .'\')',
            'class'     => 'add',
        ));

        parent::__construct();
    }

    public function getImportUrl()
    {
        return $this->getUrl('adminhtml/location_import/index');
    }
}
