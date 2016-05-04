<?php

class Mau_Thunghiem_Model_Mysql4_Thunghiem extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the thunghiem_id refers to the key field in your database table.
        $this->_init('thunghiem/thunghiem', 'thunghiem_id');
    }
}