<?php
class Korifi_Base_Block_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        sort($modules);

        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'Korifi_') === false) {
                if(strstr($moduleName, 'JET_') === false){
                    if(strstr($moduleName, 'Jet_') === false){
                        if(strstr($moduleName, 'Batsatla_Lookbook') === false){
                            continue;
                        }
                    }
                }
            }

            if (in_array($moduleName, array(
                'Korifi_Base', 'JET_Base', 'Jet_Base'
            ))) {
                continue;
            }

            if ((string)Mage::getConfig()->getModuleConfig($moduleName)->is_system == 'true')
                continue;

            $html.= $this->_getFieldHtml($element, $moduleName);
        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }

    protected function _getFieldHtml($fieldset, $moduleCode)
    {
        $currentVer = Mage::getConfig()->getModuleConfig($moduleCode)->version;
        if (!$currentVer)
            return '';

         // in case we have no data in the RSS
        $moduleName = (string)Mage::getConfig()->getNode('modules/' . $moduleCode . '/name');
        if ($moduleName) {
            $name = $moduleName;
            $url = (string)Mage::getConfig()->getNode('modules/' . $moduleCode . '/url');
            $moduleName = '<a href="' . $url . '" target="_blank" title="' . $name . '">' . $name . "</a>";
        } else {
            $moduleName = substr($moduleCode, strpos($moduleCode, '_') + 1);
        }

        $allExtensions = Korifi_Base_Helper_Module::getAllExtensions();
            
        $status = '<a  target="_blank"><img src="'.$this->getSkinUrl('images/kbase/ok.gif').'" title="'.$this->__("Installed").'"/></a>';

        if ($allExtensions && isset($allExtensions[$moduleCode])){
            $ext = $allExtensions[$moduleCode];

            $url     = $ext['url'];
            $name    = $ext['name'];
            $lastVer = $ext['version'];

            $moduleName = '<a href="'.$url.'" target="_blank" title="'.$name.'">'.$name."</a>";
            
            if (version_compare($currentVer, $lastVer, '<')) {
                $status = '<a href="'.$url.'" target="_blank"><img src="'.$this->getSkinUrl('images/kbase/update.gif').'" alt="'.$this->__("Update available").'" title="'.$this->__("Update available").'"/></a>';
            }
        }

        // in case if module output disabled
        if (Mage::getStoreConfig('advanced/modules_disable_output/' . $moduleCode)) {
            $status = '<a  target="_blank"><img src="' . $this->getSkinUrl('images/kbase/bad.gif') . '" alt="' . $this->__('Output disabled') . '" title="' . $this->__('Output disabled') . '"/></a>';
        }

        $moduleName = $status . ' ' . $moduleName;

        $field = $fieldset->addField($moduleCode, 'label', array(
            'name'  => 'dummy',
            'label' => $moduleName,
            'value' => $currentVer,
        ))->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}