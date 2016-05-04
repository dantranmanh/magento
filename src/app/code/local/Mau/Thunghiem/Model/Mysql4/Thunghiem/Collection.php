<?php

class Mau_Thunghiem_Model_Mysql4_Thunghiem_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('thunghiem/thunghiem');
    }
}