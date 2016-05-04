<?php

class Mau_Thunghiem_Model_Thunghiem extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('thunghiem/thunghiem');
    }
}