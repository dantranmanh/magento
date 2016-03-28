<?php

class Citybeach_Omnivore_Block_Adminhtml_Payment extends Mage_Payment_Block_Info
{
    private $order = NULL;

// ########################################

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('omnivore/payment.phtml');
    }

    /**
     * Get absolute path to template
     *
     * @return string
     */
    public function getTemplateFile()
    {
        $params = array(
            '_relative' => true,
            '_area' => 'adminhtml',
            '_package' => 'default',
            '_theme' => 'default'
        );

        return Mage::getDesign()->getTemplateFilename($this->getTemplate(), $params);
    }

    public function getPoNumber()
    {
        /** @var Mage_Sales_Model_Order_Payment $foo */
        $foo = $this->getInfo();
        Mage::log("--> foo is " . get_class($foo));

        return $foo->getPoNumber();
    }

    public function getAdditionalInformation()
    {
        /** @var Mage_Sales_Model_Order_Payment $foo */
        $foo = $this->getInfo();
    }

    public function getTransactionNumber()
    {
        return "abc123";
    }
}
