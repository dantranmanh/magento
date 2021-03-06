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
 * @since        Class available since Release 3.0
 */

class GoMage_Navigation_Model_Attribute_Store extends Mage_Core_Model_Abstract
{
			
    public function _construct()
    {
        parent::_construct();
        $this->_init('gomage_navigation/attribute_store');
    }
                  
}


