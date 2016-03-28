<?php
/**
 * Created by PhpStorm.
 * User: radu
 * Date: 24/05/15
 * Time: 12:25 PM
 */

class Citybeach_Omnivore_Model_Mysql4_Rego extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('citybeach_omnivore/rego', 'rego_id');
    }
}