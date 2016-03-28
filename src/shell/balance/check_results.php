<?php
require_once '../abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Compiler extends Mage_Shell_Abstract
{
    public $_categories=array();
    public $_inputFile = 'input.csv';
    public $_outputFile="output.csv";
    public $_productFile="product.csv";
    public $_imgFile="image.csv";

    public $_color_label=array('Colours PC-LIGHTUSB-M','Colours HC-G900-77','Colours LC-4S-MSG','Colour LC-G900-WF','Colour HTC ONE','Colours HTC ONE Soft',
        'Colour - LC iPad 3 19','Colour - LC iPad Air 19 Flower','Colours - Mint/Black','Colours - iphone 6 6 plus Walnutt','Colours - Marble','Colours - Denim Options',
        'Colours - Marine Anchor','Colours','Color','Colour','Colours - iPhone 6S Lifeproof Fre','Colours - Oscar Fruit Case','Colours - Oscar Wood','Colour - Oscar Marble','Colour - Oscar Marble Matte','Colour - Oscar Sea',
        'Colour - Vintage Leather','Colours - iPhone 6/ 6S Otterbox Defender'
    );
    public $_patterns=array('Pattern','Patterns');
    public $_patters_colours=array('Patters / Colours');
    public $_g_raft_covers=array('G Raft Covers');
    public $_options=array('Options');
    public $_prints=array('Prints - Oscar Animal','Prints - Luxo Denim','Prints - Oscar Brush','Prints - Oscar Travel','PRINTS - Oscar Universe','Prints - Oscar Landscape');

    public $_index_csv_brand=4;
    public $_csv_brand_name="Brand";
    public $_match_csv_header=array();
    public $_current_configure_product=null;
    public $_current_configure_product_supper_attr=null;
    public $_current_configure_product_cat=array();

    public $_new_attributes=array();
    public $_new_attributes_value=array();

    public $_supper_attributes_arr=array();
    public $_supper_attributes=array('options','patterns','patters_colours','g_raft_covers','prints','color');

    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {
        /**
         * check configurable products that do not have any simple products as associated product
         */
        $empty=$this->check_empty_configurable_product();
        if(!empty($empty)){
            $this->showdata('there are '.count($empty).' configurable product that has no associate simple product.Read file var/log/bi_debug_empty_configurable_product.log for more details');
            Mage::log($empty, Zend_Log::DEBUG, 'bi_debug_empty_configurable_product.log');
        }

        /**
     * check products that have no images
     */
        $empty_images=$this->check_empty_configurable_product();
        if(!empty($empty_images)){
            $this->showdata('there are '.count($empty_images).' configurable product that has no images.Read file var/log/bi_debug_empty_product_images.log for more details');
            Mage::log($empty, Zend_Log::DEBUG, 'bi_debug_empty_product_images.log');
        }


        /**
         * check configurable products that do not have images for all options
         */
        $empty_images = $this->check_missing_images_in_product_options();
        if(!empty($empty_images)){
            $this->showdata('there are '.count($empty_images).' configurable product that has no images.Read file media/import/bi_debug_missing_images_in_product_options.csv for more details');

            $this->writeCSV_check_missing_images_in_product_options($empty_images,'bi_debug_missing_images_in_product_options.csv');
        }
    }

    public function writeCSV_check_missing_images_in_product_options($data,$outputFile){
        $file=Mage::getBaseDir('media') . DS . 'import'. DS .$outputFile;
        $csv = new Varien_File_Csv();
        $csv->setLineLength(20480);
        $csvdata = array();
        /*write the header of csv file*/
        $csvdata[] = $this->getHeaderCSVLine_check_missing_images_in_product_options();
        foreach($data as $index => $dt){
            foreach($dt[1] as $_vl){
                $csvdata[]=array($dt[0],$index,$_vl);
            }
        }
        $csv->saveData($file, $csvdata);
        fclose($file_handle);
        ///return $line_of_text;
        return ;
    }


    /**
     * @return array
     */

    public function check_missing_images_in_product_options(){
        $empty_img=array();
        $_productCollection1 = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id','configurable');
        $empty_images=array();
        foreach ($_productCollection1 as $product1) {
            $empty=array();
            $product1=Mage::getModel('catalog/product')->load($product1->getEntityId());
            $supper_attr=$this->_get_supper_attributes($product1);
            if(!empty($supper_attr)){
                $childProducts = Mage::getModel('catalog/product_type_configurable')
                    ->getUsedProducts(null, $product1);
                $option=array();
                foreach($childProducts as $child){
                    $child=Mage::getModel('catalog/product')->load($child->getId());
                    $option[]=$child->getAttributeText($supper_attr);
                };
                $curren_label=array();
                foreach($product1->getData('media_gallery') as $each){
                    foreach($each as $image){
                        if(!empty($image['label'])) $curren_label[]=$image['label'];
                    }
                }

                foreach($option as $_opt){
                    if(!in_array($_opt,$curren_label)) $empty[]=$_opt;
                }
            }
            if(!empty($empty)) $empty_img[$product1->getSku()]=array($product1->getSku(),$empty);
        }
        return $empty_img;
    }

    /**
     * @param $product1 This product only have 1 supper attributes
     */
    public function _get_supper_attributes($product1){
        $_supper_attributes = $product1->getTypeInstance(true)->getUsedProductAttributeIds($product1);
        $super=null;
        foreach($_supper_attributes as $attr){
            $_attribute_mode=Mage::getModel('eav/entity_attribute')->load($attr);
            $super=$_attribute_mode->getData('attribute_code');
        }
        return $super;
    }
    /**
     * @return array
     */
    public function check_empty_product_images(){
        $empty=array();
        $_productCollection1 = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id','configurable');
        $empty_images=array();
        foreach ($_productCollection1 as $product1) {
            $_img=0;
            foreach($product1->getData('media_gallery') as $each){
                foreach($each as $image){
                    $_img++;
                }
            }
            if($_img == 0) $empty_images[]=$product1->getSku();
        }
        return $empty_images;
    }





    public function check_empty_configurable_product(){
        $empty=array();
        $_productCollection1 = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id','configurable');
        foreach ($_productCollection1 as $product1) {
            $childProducts = Mage::getModel('catalog/product_type_configurable')
                ->getUsedProducts(null, $product1);
            if(empty($childProducts)){
                $empty[]=array($product1->getEntityId(),$product1->getName());
            }
        }
        return $empty;
    }
    function check_sku($sku){
        $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        if($_product){
            return $_product;
        }
        return false;
    }

    /**return
    [prints] => Array
    (
    [id] => 312
    [label] => Prints
    [code] => prints
    )
     */

    function process_supper_attributes(){
        $attr=array();
        foreach($this->_supper_attributes as $code){
            $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $code);
            $attributeId = $attributeModel->getAttributeId();
            $attributeLabel = $attributeModel->getFrontendLabel();
            $attributeCode = $attributeModel->getAttributeCode();
            $attr[$code]=array('id'=>$attributeId,'label'=>$attributeLabel,'code'=>$attributeCode);
        }
        $this->_supper_attributes_arr =$attr;

    }

    function findAtributte_csvLine_name($name) {
        if(empty($name)) return false;
        $colors=$this->_color_label;
        $patterns=$this->_patterns;
        $patters_colours=$this->_patters_colours;
        $g_raft_covers=$this->_g_raft_covers;
        $_options=$this->_options;
        $_prints=$this->_prints;
        $name_arr=explode("]",$name);
        $name_arr1=explode(",",$name_arr[1]);
        foreach($name_arr1 as $color){
            $color_str=explode('=',$color);
            if(!empty($color_str[0])) {
                $attribute=$color_str[0];
                if(in_array($attribute,$colors)){
                    return 'color';
                }
                if(in_array($attribute,$patterns)){
                    return 'patterns';
                }
                if(in_array($attribute,$patters_colours)){
                    return 'patters_colours';
                }
                if(in_array($attribute,$g_raft_covers)){
                    return 'g_raft_covers';
                }
                if(in_array($attribute,$_options)){
                    return 'options';
                }
                if(in_array($attribute,$_prints)){
                    return 'prints';
                }
                //$this->_new_attributes_value[$color_str[0]]=
            }
            //if(!empty($color_str[0]) && !in_array($color_str[0],$this->_new_attributes)) $this->_new_attributes[]= $color_str[0];
        }
        return '';
    }

    /*csv process*/

    public function getIndex($csvFile,$columnname){
        $line_of_text=array();
        if(!empty($this->_match_csv_header)) $line_of_text=$this->_match_csv_header;
        else{
            $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
            $i=0;
            $header=array();
            while (!feof($file_handle) ) {
                if($i>=1) break;
                $i++;
                $line_of_text = fgetcsv($file_handle, 20480);
            }
            fclose($file_handle);
            $this->_match_csv_header=$line_of_text;
        }
        $header=$line_of_text;
        foreach($header as $index=> $column){
            if($column == $columnname ) return $index;
        }
        return -1;
    }


    /**process string data and csv data*/
    function cut_space($string=''){
        return str_replace(" ","",$string);
    }

    function add_enclose($data) {
        $result=array();
        foreach($data as $dt){
            $result[]="\"$dt\"";
        }
        return $result;
    }
    function getHeaderCSVLine_check_missing_images_in_product_options(){
        $data=array();
        $data[0] = 'product_id';
        $data[1] = 'product_sku';
        $data[2] = 'missing attribute option';
        return $data;
        /*$fp = fopen($file, 'a') or die('can not open file');
        fputcsv($fp, $this->add_enclose($data),',','^');
        fclose($fp);*/
    }






}

$shell = new Mage_Shell_Compiler();
$shell->run();

