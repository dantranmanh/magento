<?php
class Korifi_CommWeb_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('korifi/commweb/info.phtml');
    }

    public function toPdf()
    {
        $this->setTemplate('korifi/commweb/pdf/info.phtml');
        return $this->toHtml();
    }

}
