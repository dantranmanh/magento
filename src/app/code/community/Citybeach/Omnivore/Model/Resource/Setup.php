<?php

class Citybeach_Omnivore_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    public function startSetup()
    {
        $this->getConnection()->startSetup();
        return $this;
    }
}
