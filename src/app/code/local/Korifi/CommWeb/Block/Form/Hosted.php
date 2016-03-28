<?php
class Korifi_CommWeb_Block_Form_Hosted extends Mage_Payment_Block_Form_Cc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('korifi/commweb/form/hosted.phtml');
    }

    /**
     * Get available types
     */
    public function getCcAvailableTypes()
    {
        return $this->getMethod()->getCcAvailableTypes();
    }

}
