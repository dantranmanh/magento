<?php
/**
 * Import model
 *
 * @category   Happytel
 * @package    Happytel_Locator
 * @author     Nguyet Nguyen (nguyet@balanceinternet.com.au)
 */
class Happytel_Locator_Model_Import extends Varien_Object
{
    protected $headers = array();
    protected $_attributeModels = array();
    protected $_attributeOptions = array();

    protected $_skipped = array();

    public function __construct(){
        // map core table to csv
        $this->mapper = array(
            'title' => 'title',
            'phone' => 'phone',
            'description' => 'description',
            'address' => 'address',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'meta_title'        => 'meta_title',
            'meta_description' => 'meta_description',
            'meta_keywords' => 'meta_keywords',
            "country" => "country",
            'administrative_area' => 'administrative_area',
            'locality'  => 'locality',
            'dependent_locality'   => 'dependent_locality',
            'google_map_iframes'    => 'google_map_iframes',
            'branch_no' => 'branch_no',
            'company_name' => 'company_name',
            'postal_code' => 'postal_code',
            'monday'   => 'monday',
            'tuesday'   => 'tuesday',
            'wednesday' => 'wednesday',
            'thursday'  => 'thursday',
            'friday'    => 'friday',
            'saturday'  => 'saturday',
            'sunday'    => 'sunday'
        );
    }

    /**
     *
     */
    public function run($filePath)
    {
        //$filePath =  realpath(dirname(__FILE__)).'/../data/stores.csv';
        $i = 0;
        if(($handle = fopen("$filePath", "r")) !== false) {
            while(($data = fgetcsv($handle, 1000, ",")) !== false){
                if($i==0){
                    $this->setHeaders($data);
                }else{
                    $this->saveFromCsv($this->parseCsv($data));
                }
                $i++;
            }

            if(count($this->_skipped)){
                $this->log(count($this->_skipped).' stores were skipped');
            }
        }
        else{
            Mage::getSingleton('adminhtml/session')->addError("There is some Error");
        }
    }

    public function saveFromCsv($data)
    {
        $loc = Mage::getModel('ak_locator/location');
        $locs = $loc->getCollection()->addAttributeToSelect('*')
            ->addAttributeToFilter('title',$data['title'])
            ->addAttributeToFilter('company_name', $data['company_name']);
        if(count($locs)) {
            $this->log('updating existing store '.trim($data['title']));
            $loc = $locs->getFirstItem();
        }else{
            $this->log('importing new store '.trim($data['title']));
        }
        if (!isset($data['country'])) {
            $data['country'] = "Australia";
        }

        //preprocess data to manipulate values where required
        $data = $this->preprocess($data);
        foreach($this->mapper as $att => $col){
            if (isset($data[$col])) {
                switch ($this->getAttributeModel($att)->getFrontendInput()) {
                    case 'select':
                        $loc->setData($att, $this->getSelectValue($att, $data[$col]));
                        break;
                    case 'multiselect':
                        $loc->setData($att, $this->getMultiSelectValue($att, $data[$col]));
                        break;
                    default:
                        $loc->setData($att, $data[$col]);
                        break;
                }
            }
        }
        try {
            //$this->log($loc->getData());
            $loc->save();
        } catch (Exception $e) {
            $this->log($e->getMessage());
        }

        $this->log(trim($data['title']).' saved');
    }


    private function setHeaders($data)
    {
        foreach($data as $col){
            $this->headers[] = str_replace(' ', '_', strtolower($col));
        }

    }


    protected function getSelectValue($attribute_code, $label)
    {
        foreach($this->getAttributeOptions($attribute_code) as $option){
            if($option['label'] == $label){
                return $option['value'];
            }
        }
    }

    protected function getMultiSelectValue($attribute_code, $label)
    {
        $values = array();

        if(strstr($label, ' , ')){
            $labels = explode(' , ', $label);
        }else if(strstr($label, ' or ')){
            //specific to trackside data as sometimes is has " or " in place of commas
            $labels = explode(' or ', $label);
        }else{
            $labels[] = trim($label);
        }

        foreach($labels as $label){
            foreach($this->getAttributeOptions($attribute_code) as $option){
                if($option['label'] == trim($label)){
                    $values[] = $option['value'];
                }
            }
        }

        return implode(',', $values);
    }


    protected function getAttributeModel($attribute_code)
    {
        if(!isset($this->_attributeModels[$attribute_code])){
            $attribute_model = Mage::getModel('eav/entity_attribute');
            $id = $attribute_model->getIdByCode(Ak_Locator_Model_Location::ENTITY, $attribute_code);
            $this->_attributeModels[$attribute_code] = $attribute_model->load($id);
        }

        return $this->_attributeModels[$attribute_code];
    }

    protected function getAttributeOptions($attribute_code)
    {

        if(!$this->_attributeOptions[$attribute_code]){
            $this->_attributeOptions[$attribute_code] = $this->getAttributeModel($attribute_code)->getSource()->getAllOptions(false);
        }
        return $this->_attributeOptions[$attribute_code];
    }


    /**
     *  Get address in display format from csv data
     */
    public function getAddress($data)
    {
        $parts = array();

        if($data['address']){
            $parts[] = $data['address'];
        }
        if($data['address_2']){
            $parts[] = $data['address_2'];
        }
        if($data['suburb']){
            $parts[] = $data['suburb'];
        }

        $parts[] = 'australia';

        return implode(', ', $parts);
    }

    /**
     * parse csv row to array with column header as key
     */
    private function parseCsv($data)
    {
        $storeData = array();

        $col = 0;
        foreach($data as $value){
            $storeData[$this->headers[$col]] = trim($value);
            $col++;
        }

        return $storeData;
    }

    /**
     * attempt to generate a lat long value from address data givin
     */
    private function geocodeData($data)
    {
        //return false;
        include_once(Mage::getBaseDir('lib').'/geoPHP/geoPHP.inc');
        $key = Mage::getStoreConfig('locator_settings/google_maps/api_key');
        $geocoder = new GoogleGeocode($key);
        $query = $this->getAddress($data);

        try{
            $result = $geocoder->read($query,'raw');
        }catch (Exception $e){
            $_skipped[] = $data['store_name'];
            $this->log('skipping address as it could not be geocoded. '.$query);
            return false;
        }


        if($result){
            return $result;
        }else{
            return false;
        }
    }


    /**
     * Preprocess the row to manipulate any data
     *
     * @param $data
     * @return mixed
     */
    protected function preprocess($data)
    {
        if (!isset($data['enabled'])) {
            $data['is_enabled'] = '1';
        }

        if (!isset($data['stockist'])) {
            $data['is_stockist'] = '0';
        }

        foreach($data as $key => $val){
            if($val == 'NULL'){
                $data[$key] = '';
            }
        }

        return $data;
    }


    protected function log($msg)
    {
        Mage::log($msg,Zend_Log::DEBUG, 'store_import.log');
    }

}