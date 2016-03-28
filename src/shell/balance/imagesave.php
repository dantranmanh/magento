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

    public $_supper_attributes_arr=array();
    public $_supper_attributes=array('options','patterns','patters_colours','g_raft_covers','prints','color');


    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {
        //$this->empty_image_configurable_product();
        //$product=Mage::getModel('catalog/product')->load(3401);
        //$this->remove_images($product);
        //$this->saveImageForProduct('SkuTestJin','testJin','photo__75686.png');return;
        $this->image();
    }
    public function image()
    {
        $r1=$this->createImageCSV(dirname(__FILE__).DS.$this->_inputFile,$this->_imgFile);

        //$result=$this->ReadImageCSV($this->_imgFile);

        //$result=$this->MapImageCSV($this->_imgFile);
    }
    function remove_images($product){
        Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
        $this->showdata('Deleting images of product : '.$product->getSku());
        if ($product->getId()){
            $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
            $items = $mediaApi->items($product->getId());
            foreach($items as $item)
                $mediaApi->remove($product->getId(), $item['file']);
        }
    }
    public function empty_image_configurable_product(){

        $_productCollection1 = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id','configurable');
        foreach ($_productCollection1 as $product1) {
            $product1=Mage::getModel('catalog/product')->load($product1->getEntityId());
            $this->remove_images($product1);
        }
    }

    public function ReadImageCSV($csvFile){
        $csv=new Varien_File_Csv();
        $file=Mage::getBaseDir('media') . DS . 'import'. DS .$csvFile;
        $images=$csv->getData($file);
        $i=0;
        foreach($images as $img)
        {
            // if ($i >=5) break;
            // Mage::log($img[0], Zend_Log::DEBUG, 'bi_debug.log');
            $this->DownloadImage($img[2],$img[3]);
            echo 'downloaded: '.$i."image"."\n";
            $i++;
        }
        echo 'finish : '.$i."image";
        return ;
    }
    //read file
    public function createImageCSV($csvFile,$outputFile){
        $file_handle = fopen($csvFile, 'r');
        $i=0;
        $file=Mage::getBaseDir('media') . DS . 'import'. DS .$outputFile;
        $csv = new Varien_File_Csv();
        $csv->setLineLength(10480);
        $csvdata = array();
        $current_attribute_option_SKU=array();
        $current_attribute_option_Rule=0;
        while (!feof($file_handle) ) {
            // if($i>=3) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            //showdata($line_of_text);
            if(empty($line_of_text[3])) $line_of_text[3]='PRD-'.$line_of_text[1];
            if(empty($line_of_text[1]) && empty($line_of_text[3])) continue;
            $optionset=$line_of_text[$this->getIndex($this->_inputFile,"Option Set")];
            if($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Product Type")]) =="P"){ /*create configurable product*/
                if(!empty($optionset)){ /** add configurable product images*/
                    $configurable=$this->_current_configure_product;
                    if($configurable){
                        //if(!$current_attribute_option_Rule) {
                        $config_data=$this->getImageCSVLine_configure($configurable,$current_attribute_option_SKU);
                        //Mage::log($config_data, Zend_Log::DEBUG, 'bi_debug1eeeeeeeeeeeee.log');
                        foreach($config_data as $rd){
                            $csvdata[]=$rd;
                        }
                        //}
                    }
                    $this->_current_configure_product=$line_of_text;
                    $current_attribute_option_SKU=array();
                    $current_attribute_option_Rule=0;
                }else{ /** add simple product images*/
                    $data=$this->getImageCSVLine($line_of_text,$outputFile,true,false);
                    if(!empty($data))
                        $read =$this->getImageCSVLine($line_of_text,$outputFile,true,false);
                    foreach($read as $rd){
                        $csvdata[]=$rd;
                    }
                }

            }elseif($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Item Type")]) =="SKU"){ /*create simple product*/
                $data_sku=$this->getAssociatedProductOption($line_of_text);
                $current_attribute_option_SKU[]=$data_sku;
            }elseif($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Item Type")]) =="Rule"){
                $current_attribute_option_Rule=1;
                $data_rule=$this->getImageCSVLine($line_of_text,$outputFile,false,true);
                if(!empty($data_rule))
                    $read =$this->getImageCSVLine($line_of_text,$outputFile,false,true);
                foreach($read as $rd){
                    $csvdata[]=$rd;
                }
            }
        }
        $csv->saveData($file, $csvdata);
        fclose($file_handle);
        return ;
    }

    /**
     * @param $line_of_text
     * [RB]Colours=Rose Gold
     */
    public function getAssociatedProductOption($line_of_text){
        $_attr_val=null;
        $_name=explode('=',$line_of_text[$this->getIndex($this->_inputFile,"Name")]);
        //$_value=explode(':',$_name[1]);
        $_attr_val=$_name[1];
        $_attr_val=$this->_replace_sign_from_attribute_label($_attr_val);
        //Mage::log($line_of_text[$this->getIndex($this->_inputFile,"Product SKU")].":  ".$_attr_val, Zend_Log::DEBUG, 'bi_debug1ccccccc.log');
        return $_attr_val;
    }
    /**
     * @param $array_line
     * Product Image ID: 414, Product Image File: galaxy_s5_aluminize_white_1__57738.jpg, Product Image Path: e/098/galaxy_s5_aluminize_white_1__57738.jpg, Product Image URL: http://www.happytel.com/product_images/e/098/galaxy_s5_aluminize_white_1__57738.jpg, Product Image Description: Samsung Galaxy S5 Cases Covers - Hard Case Aluminize White, Product Image Is Thumbnail: 0, Product Image Index: 6|
     */
    public function getImageCSVLine_configure($array_line,$option_val){
        $_order_product_img=$this->getIndex($this->_inputFile,"Product Images");
        $_order_code=$this->getIndex($this->_inputFile,"Product SKU");
        $_order_name=$this->getIndex($this->_inputFile,"Name");
        $images=$array_line[$_order_product_img];
        $imgs=explode('|',$images);
        $csv_line=array();
        //Mage::log($option_val, Zend_Log::DEBUG, 'bi_debug1dddddddđ.log');
        foreach($imgs as $img){
            $img_1=explode(',',$img);
            $description=$img_1[4];
            //Mage::log($description, Zend_Log::DEBUG, 'bi_debug1dddddddđ.log');
            $title='';
            foreach($option_val as $val){
                $_option=$val;
                $val=explode("-",$val);
                $val=$val[0];
                $pos = strpos($description,$val);
                if ($pos === false) {
                    continue;
                } else {
                    $title=$_option;
                }
            }

            $data=array();
            $data[0]= $array_line[$_order_code];    //"sku";
            $data[1]= $array_line[$_order_name];    // "name";
            $filename='';
            $url='';
            $description='';
            $image_mess=explode("Product Image URL: ",$img);
            $image_mess_1=explode(",",$image_mess[1]);
            if($image_mess_1[0]) $url=$image_mess_1[0];
            $image_mess_2=explode("/",$url);
            $filename=end($image_mess_2);
            if(empty($filename) or empty($url)) {
                echo 'image or url is missing at product sku: '.$array_line[$_order_code]."\n"."<br>";
                return false;
            }
            $data[2]=$filename; //"image file name"
            $data[3]=$url; //"image url"
            $data[4]=$title; //"image Label"
            $data[5]='';
            if(empty($title))
                $data[5]=implode('|',$option_val); //"empty image Label"
            $csv_line[]=$data;
        }
        //Mage::log($csv_line, Zend_Log::DEBUG, 'bi_debug1dddddddđ.log');
        return $csv_line;
    }

    /**[26] => Product Image File: 8_pin_car_charger_mint_1__22840.jpg, Product Image URL: http://www.happytel.com/product_images/m/114/8_pin_car_charger_mint_1__22840.jpg|Product Image File: 8_pin_car_charger_pink_1__38734.jpg, Product Image URL: http://www.happytel.com/product_images/v/792/8_pin_car_charger_pink_1__38734.jpg|Product Image File: 8_pin_car_charger_yellowk_1_1__32742.jpg, Product Image URL: http://www.happytel.com/product_images/w/917/8_pin_car_charger_yellowk_1_1__32742.jpg|Product Image File: 8_pin_car_charger_2__14299.jpg, Product Image URL: http://www.happytel.com/product_images/x/233/8_pin_car_charger_2__14299.jpg*/
    /**Product Image ID: 408, Product Image File: galaxy_s5_hard_eureka_emerald_blue_2__36399.jpg, Product Image Path: f/234/galaxy_s5_hard_eureka_emerald_blue_2__36399.jpg, Product Image URL: http://www.happytel.com/product_images/f/234/galaxy_s5_hard_eureka_emerald_blue_2__36399.jpg, Product Image Description: Galaxy S5 Cases Covers - Hard Case Eureka Blue (Opal Card Holder Phone Case), Product Image Is Thumbnail: 0, Product Image Index: 6|*/
    public function getImageCSVLine($array_line,$file=null,$simple=false,$rule=false){
        $current_cf=$this->_current_configure_product;
        if($simple)$current_cf=$array_line;
        $_order_product_img=$this->getIndex($this->_inputFile,"Product Images");
        $_order_code=$this->getIndex($this->_inputFile,"Product SKU");
        $_order_name=$this->getIndex($this->_inputFile,"Name");
        /*if($rule){
            $attr=$this->findAtributte_csvLine_name($array_line[$_order_name]);
            if(empty($attr)){
                Mage::log($line_of_text, Zend_Log::DEBUG, 'bigcomerce_missing_supperattribute.log');
                $this->showdata('can not find out the value for supper attribute on product: '.$array_line[$_order_code]);
                $attr='';
            }
        }*/

        if(empty($array_line[$_order_product_img])) return;
        if(!$current_cf[$_order_code]) $current_cf[$_order_code] = ($current_cf[$_order_name]?$current_cf[$_order_name]:$current_cf[$_order_product_url]);

        if($current_cf[$_order_code] == "Product SKU") return;
        if(!$current_cf[$_order_code] || !$current_cf[$_order_name]) {
            echo 'can not find out the sku for the product id: '.$array_line[1]."<br>";
            return;
        }
        $_attr_val=$this->getAssociatedProductOption($array_line);
        $csv_line=array();
        $image_arr=explode("|",$array_line[$_order_product_img]);
        foreach($image_arr as $imgline){
            if(empty($imgline)) continue;
            $data=array();
            $data[0]= $current_cf[$_order_code];    //"sku";
            $data[1]= $current_cf[$_order_name];    // "name";
            $filename='';
            $url='';
            $description='';
            $image_mess=explode("Product Image URL: ",$imgline);
            $image_mess_1=explode(",",$image_mess[1]);
            if($image_mess_1[0]) $url=$image_mess_1[0];
            $image_mess_2=explode("/",$url);
            $filename=end($image_mess_2);
            if(empty($filename) or empty($url)) {
                echo 'image or url is missing at product sku: '.$current_cf[$_order_code]."\n"."<br>";
                return false;
            }
            $data[2]=$filename; //"image file name"
            $data[3]=$url; //"image url"
            $data[4]=$_attr_val; //"image Label"
            $data[5]='';
            $csv_line[]=$data;
        }

        return $csv_line;
        /* $fp = fopen($file, 'a') or die('can not open file');
         fputcsv($fp, $this->add_enclose($data),',', '^');
         fclose($fp);*/
    }
    public function DownloadImage($imagename,$imageurl){
        $_productId = Mage::getModel('catalog/product')->getIdBySku($productsku);
        $newProduct = Mage::getModel('catalog/product')->load($_productId);
        $data=array("imagename"=> $imagename,"imageurl"=>$imageurl);
        $image_url = $data['imageurl'];
        //$image_url  =str_replace("https://", "http://", $image_url); // replace https tp http
        $image_type = substr(strrchr($image_url,"."),1); //find the image extension
        $filename   = $data['imagename']; //give a new name, you can modify as per your requirement
        $filepath   = Mage::getBaseDir('media') . DS . 'import'. DS .'image'.DS.$filename; //path for temp storage folder: ./media/import/
        if (file_exists($filepath)) {
            Mage::log($imagename."is duplicated", Zend_Log::DEBUG, 'bi_debug_image_error.log');
            return;
        }


        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$image_url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Cirkel');$query = curl_exec($curl_handle);curl_close($curl_handle);
        file_put_contents($filepath, $query); //store the image from external url to the temp storage folder file_get_contents(trim($image_url))
        $filepath_to_image=$filepath;
    }

    /**
     *
     */
    public function _check_image_exists($newProduct,$image_name){
        $name=explode('.',$image_name);
        foreach($newProduct->getData('media_gallery') as $each){
            foreach($each as $image){
                $file=$image['file'];
                $pos = strpos($file, $name);
                if ($pos === false) {
                    continue;
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $name name of product includes values of supper attributes.
     * @return bool|string
     */
    function findAtributte_csvLine_name($name) {
        if(empty($name)) return false;
        Mage::log($name, Zend_Log::DEBUG, 'bi_debug1aaaaaaaaaaa.log');
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
            $color_str[1]=$this->_replace_sign_from_attribute_label($color_str[1]);
            if(!empty($color_str[0])) {
                $attribute=$color_str[0];
                Mage::log($attribute, Zend_Log::DEBUG, 'bi_debug1aaaaaaaaaaa.log');
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

    function add_enclose($data) {
        $result=array();
        foreach($data as $dt){
            $result[]="\"$dt\"";
        }
        return $result;
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
    /**
     *
     */

    function _replace_sign_from_attribute_label($label){
        $label=str_replace(' & '," ",$label);
        $label=str_replace(' ('," ",$label);
        $label=str_replace(')',"",$label);
        $label=str_replace(' / '," ",$label);
        $label=str_replace('('," ",$label);
        $label=str_replace('/'," ",$label);
        $label=str_replace(' - '," ",$label);
        $label=str_replace('-'," ",$label);
        $label=str_replace('  '," ",$label);

        $label=str_replace(':',"-",$label);
        $label=str_replace('|',"-",$label);
        $label=str_replace('#',"",$label);


        return $label;
    }
}

$shell = new Mage_Shell_Compiler();
$shell->run();
