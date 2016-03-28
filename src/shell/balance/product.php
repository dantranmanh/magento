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
    public $_prints=array('Prints - Oscar Animal','Prints - Luxo Denim','Prints - Oscar Brush','Prints - Oscar Travel','PRINTS - Oscar Universe','Prints - Oscar Landscape','Prints - Oscar Vector','Prints - Oscar Vector');

    public $_index_csv_brand=4;
    public $_csv_brand_name="Brand";
    public $_match_csv_header=array();
    public $_current_configure_product=null;
    public $_current_configure_product_cat=array();

    public $_new_attributes=array();
    public $_new_attributes_value=array();

    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {
        $this->_index_csv_brand=$this->getIndex($this->_inputFile,$this->_csv_brand_name);
        if($this->_index_csv_brand == '-1') $this->showdata('can not find out the index of Brand in csv file');
        else $this->showdata('Index of Brand column is: '.$this->_index_csv_brand);
        $this->process_attribute();
        //$this->_match_csv_header_magento_attribue =$this->MapColumnAttribute();
        //$this->getAllCsvColumns($this->_inputFile);


        /*$magento_header_export=array("store","websites","attribute_set","type","category_ids","sku","has_options","name","country_of_manufacture","is_returnable","msrp_enabled","msrp_display_actual_price_type","meta_title","meta_description","image","small_image","thumbnail","custom_design","page_layout","options_container","gift_message_available","gift_wrapping_available","url_key","weight","price","special_price","msrp","gift_wrapping_price","status","visibility","ebizmarts_mark_visited","tax_class_id","is_recurring","description","short_description","tax_code","depth","height","width","fixed_shipping_price","meta_keyword","custom_layout_update","news_from_date","news_to_date","special_from_date","special_to_date","custom_design_from","custom_design_to","qty","min_qty","use_config_min_qty","is_qty_decimal","backorders","use_config_backorders","min_sale_qty","use_config_min_sale_qty","max_sale_qty","use_config_max_sale_qty","is_in_stock","low_stock_date","notify_stock_qty","use_config_notify_stock_qty","manage_stock","use_config_manage_stock","stock_status_changed_auto","use_config_qty_increments","qty_increments","use_config_enable_qty_inc","enable_qty_increments","is_decimal_divided","stock_status_changed_automatically","use_config_enable_qty_increments","product_name","store_id","product_type_id","product_status_changed","product_changed_websites");
        Mage::log($magento_header_export, Zend_Log::DEBUG, 'bi_debug_mgt.log');*/
        // $this->process_category();
        //$this->getAllCsvColumns($this->_inputFile);/*log the header to see the column name of csv file*/

        $result=$this->ReadExportCSV($this->_inputFile,$this->_productFile);
    }
    public function MapColumnAttribute(){

    }
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
    public function getAllCsvColumns($csvFile){
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        $header=array();
        while (!feof($file_handle) ) {
            if($i>=2) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            Mage::log($line_of_text, Zend_Log::DEBUG, 'bi_debug_csv_column.log');
        }
        fclose($file_handle);
        $header=$line_of_text;
        //Mage::log($header, Zend_Log::DEBUG, 'bi_debug_csv_column.log');
        return $header;

    }
    public function process_category(){
        echo 'REMEMBER CATEGORY LIST'."\n";
        $mgt_cat=$this->getMagentoCategory();
        $this->_categories =$mgt_cat;
        Mage::log($this->_categories, Zend_Log::DEBUG, 'data_category.log');
    }

    function cut_space($string=''){
        return str_replace(" ","",$string);
    }
    public function ReadExportCSV_getBrand($csvFile){
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        /*write the header of csv file*/
        $_brands=array();
        while (!feof($file_handle) ) {
            //if($i>=5) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            if(empty($line_of_text[1]) && empty($line_of_text[3])) continue;
            if($line_of_text[0] == 'Product ID') continue;
            $brand=$line_of_text[$this->_index_csv_brand];
            if(!empty($brand) && !in_array($brand,$_brands))$_brands[]=$brand;
        }
        fclose($file_handle);
        //Mage::log($_brands, Zend_Log::DEBUG, 'bi_debug.log');
        return $_brands ;
    }


    public function ReadExportCSV($csvFile,$outputFile){
        $this->process_category();
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        $file=Mage::getBaseDir('media') . DS . 'import'. DS .$outputFile;
        $csv = new Varien_File_Csv();
        $csv->setLineLength(20480);
        $csvdata = array();
        /*write the header of csv file*/
        $csvdata[] = $this->getHeaderCSVLine();
        $child_product=1;
        while (!feof($file_handle) ) {
            //if($i>=15) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            if(empty($line_of_text[3])) $line_of_text[3]='PRD-'.$line_of_text[1];
            //$this->showdata($line_of_text[1]);
            if(empty($line_of_text[1]) && empty($line_of_text[3])) continue;
            if($line_of_text[0] == 'Product ID') continue;
            $optionset=$line_of_text[$this->getIndex($this->_inputFile,"Option Set")];
            if($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Product Type")]) =="P"){ /*create configurable product*/
                if($child_product > 0){
                    if($line_of_text[$this->getIndex($this->_inputFile,"Product SKU")] != $this->_current_configure_product[$this->getIndex($this->_inputFile,"Product SKU")]){
                        $data=$this->getCSVLine($this->_current_configure_product);
                        if(!empty($data))
                            $csvdata[]=$this->getCSVLine($this->_current_configure_product);
                        $this->_current_configure_product=$line_of_text;
                        $child_product=0;
                    }

                }else{
                    $data=$this->getCSVLine($line_of_text,true);
                    if(!empty($data))
                        $csvdata[]=$this->getCSVLine($line_of_text,true);
                    $child_product=0;
                }
            }elseif($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Item Type")]) =="SKU"){ /*create simple product*/
                $data=$this->getCSVLineSimple($line_of_text,$outputFile);
                if(!empty($data)){
                    $csvdata[]=$this->getCSVLineSimple($line_of_text,$this->_current_configure_product,$outputFile);
                    $child_product++;
                }
            }
        }
        /**
         * create the last configurable product
         */
        $data=$this->getCSVLine($this->_current_configure_product);
        if(!empty($data))
            $csvdata[]=$this->getCSVLine($this->_current_configure_product);


        $csv->saveData($file, $csvdata);
        fclose($file_handle);
        ///return $line_of_text;
        return ;
    }
    function _is_configureable(){

    }
    function getHeaderCSVLine(){
        $data=array();
        $data[0] = 'store';
        $data[1] = 'websites';
        $data[2] = 'attribute_set';
        $data[3] = 'type';
        $data[4] = 'category_ids';
        $data[5] = 'sku';
        $data[6] = 'has_options_disable';
        $data[7] = 'name';
        $data[8] = 'country_of_manufacture';
        $data[9] = 'is_returnable';
        $data[10] = 'msrp_enabled';
        $data[11] = 'msrp_display_actual_price_type';
        $data[12] = 'meta_title';
        $data[13] = 'meta_description';
        $data[14] = 'image_empty';
        $data[15] = 'small_image_empty';
        $data[16] = 'thumbnail_empty';
        $data[17] = 'custom_design';
        $data[18] = 'page_layout';
        $data[19] = 'options_container';
        $data[20] = 'gift_message_available';
        $data[21] = 'gift_wrapping_available';
        $data[22] = 'url_key';
        $data[23] = 'weight';
        $data[24] = 'price';
        $data[25] = 'special_price';
        $data[26] = 'msrp';
        $data[27] = 'gift_wrapping_price';
        $data[28] = 'status';
        $data[29] = 'visibility';
        $data[30] = 'ebizmarts_mark_visited';
        $data[31] = 'tax_class_id';
        $data[32] = 'is_recurring';
        $data[33] = 'description';
        $data[34] = 'short_description';
        $data[35] = 'tax_code';
        $data[36] = 'depth';
        $data[37] = 'height';
        $data[38] = 'width';
        $data[39] = 'fixed_shipping_price';
        $data[40] = 'meta_keyword';
        $data[41] = 'custom_layout_update';
        $data[42] = 'news_from_date';
        $data[43] = 'news_to_date';
        $data[44] = 'special_from_date';
        $data[45] = 'special_to_date';
        $data[46] = 'custom_design_from';
        $data[47] = 'custom_design_to';
        $data[48] = 'qty';
        $data[49] = 'min_qty';
        $data[50] = 'use_config_min_qty';
        $data[51] = 'is_qty_decimal';
        $data[52] = 'backorders';
        $data[53] = 'use_config_backorders';
        $data[54] = 'min_sale_qty';
        $data[55] = 'use_config_min_sale_qty';
        $data[56] = 'max_sale_qty';
        $data[57] = 'use_config_max_sale_qty';
        $data[58] = 'is_in_stock';
        $data[59] = 'low_stock_date';
        $data[60] = 'notify_stock_qty';
        $data[61] = 'use_config_notify_stock_qty';
        $data[62] = 'manage_stock';
        $data[63] = 'use_config_manage_stock';
        $data[64] = 'stock_status_changed_auto';
        $data[65] = 'use_config_qty_increments';
        $data[66] = 'qty_increments';
        $data[67] = 'use_config_enable_qty_inc';
        $data[68] = 'enable_qty_increments';
        $data[69] = 'is_decimal_divided';
        $data[70] = 'stock_status_changed_automatically';
        $data[71] = 'use_config_enable_qty_increments';
        $data[72] = 'product_name';
        $data[73] = 'store_id';
        $data[74] = 'product_type_id';
        $data[75] = 'product_status_changed';
        $data[76] = 'product_changed_websites';
        $data[77] = 'brand';
        //'options','patterns','patters_colours','g_raft_covers'
        $data[78] = 'options';
        $data[79] = 'patterns';
        $data[80] = 'patters_colours';
        $data[81] = 'g_raft_covers';
        $data[82] = 'color';
        $data[83] = 'prints';

        $data[84] = 'category_string';

        $data[85] = 'style';
        $data[86] = 'function';

        return $data;
        /*$fp = fopen($file, 'a') or die('can not open file');
        fputcsv($fp, $this->add_enclose($data),',','^');
        fclose($fp);*/
    }
    function getCSVLine($array_line,$_simple=false){
        $_order_category_string=$this->getIndex($this->_inputFile,"Category String");
        $_order_category_details=$this->getIndex($this->_inputFile,"Category Details");
        $_order_code=$this->getIndex($this->_inputFile,"Product SKU");/*sku*/
        $_order_name=$this->getIndex($this->_inputFile,"Name");

        $_order_page_title=$this->getIndex($this->_inputFile,"Page Title");
        $_order_meta_des=$this->getIndex($this->_inputFile,"Meta Description");
        $_order_product_url=$this->getIndex($this->_inputFile,"Product URL");

        $_order_product_price=$this->getIndex($this->_inputFile,"Price");
        $_order_retail_price=$this->getIndex($this->_inputFile,"Retail Price");
        $_order_sale_price=$this->getIndex($this->_inputFile,"Sale Price");
        $_order_calculated_price=$this->getIndex($this->_inputFile,"Calculated Price");

        $_order_product_weight=$this->getIndex($this->_inputFile,"Weight");
        $_order_product_des=$this->getIndex($this->_inputFile,"Description");
        $_order_product_vis=$this->getIndex($this->_inputFile,"Product Visible");
        $_order_product_brand=$this->getIndex($this->_inputFile,"Brand");
        $_order_meta_kw=$this->getIndex($this->_inputFile,"Meta Keywords");

        $_order_taxcode=$this->getIndex($this->_inputFile,"Avalara Product Tax Code");
        $_order_qty=$this->getIndex($this->_inputFile,"Stock Level");
        $_order_min_qty=$this->getIndex($this->_inputFile,"Low Stock Level");

        $_order_type=$this->getIndex($this->_inputFile,"Product Type");
        $_order_item_type=$this->getIndex($this->_inputFile,"Item Type");

        if(!$array_line[$_order_code]) $array_line[$_order_code] = ($array_line[$_order_name]?$array_line[$_order_name]:$array_line[$_order_product_url]);

        if($array_line[$_order_code] == "Code") return;
        if(!$array_line[$_order_code] || !$array_line[$_order_name]) {
            echo 'can not find out the sku for the product id: '.$array_line[1]."<br>";
            return;
        }
        $data=array();
        $data[0]= "admin";//"store"
        $data[1]= "base";//"websites"
        $data[2]= "Default";//"attribute_set"
        $data[3]= "simple";//"type"
        $optionset=$array_line[$this->getIndex($this->_inputFile,"Option Set")];
        if(!empty($optionset) && empty($_simple)){
            $data[3]= "configurable";//"type"
        }
        if($this->cut_space($array_line[$_order_item_type]) == "SKU"){
            $cat_string= $this->_current_configure_product[$_order_category_details];
        }else $cat_string=$array_line[$_order_category_details];

        $category=$this->FindCategoryId($cat_string,$cat_string);
        if(empty($category)) {
            echo 'can not find out the categry for the product : '.$array_line[$_order_name]."<br>";
            Mage::log('can not findout category', Zend_Log::DEBUG, 'bi_debug_not_cat.log');
            Mage::log($array_line, Zend_Log::DEBUG, 'bi_debug_not_cat.log');
            $data[4]="";
            Mage::log($array_line, Zend_Log::DEBUG, 'bigcomerce_missing_categories.log');

        }else{
            $data[4]=$category;             // "category_ids";
            //$this->_current_configure_product_cat=$category;
        }


        $data[5]= $array_line[$_order_code];    //"sku";

        $data[6]= "0"; //"has_options"

        $data[7]= $array_line[$_order_name];    // "name";

        $data[8] = '';//'country_of_manufacture';
        $data[9] = 'Use config';//'is_returnable';
        $data[10] = 'Use config';//'msrp_enabled';
        $data[11] = 'Use config';//'msrp_display_actual_price_type';
        $data[12] = $array_line[$_order_page_title];//'meta_title';
        $data[13] = $array_line[$_order_meta_des]; // 'meta_description';
        $data[14] = "";//'image';
        $data[15] = "";//'small_image';
        $data[16] = "";//'thumbnail';
        $data[17] = "";//'custom_design';
        $data[18] = "No layout updates";// 'page_layout';
        $data[19] = "Product Info Column";//'options_container';
        $data[20] = "No";//'gift_message_available';
        $data[21] = "No";//'gift_wrapping_available';

        $url=str_replace("/",'',$array_line[$_order_product_url]);
        $data[22] = $url;//'url_key';
        $data[23] = $array_line[$_order_product_weight];//'weight';



        $price=$array_line[$_order_product_price];
        $special_price="";
        $retails_price=$array_line[$_order_retail_price];
        $sale_price=$array_line[$_order_sale_price];
        $calculate_price=$array_line[$_order_calculated_price];
        if(!empty($retails_price) && $retails_price > $price && $retails_price > $calculate_price){
            $special_price=$price;
            $price=$retails_price;
        }

        $data[24] = $price;//'price';
        $data[25] = $special_price;// 'special_price';
        $data[26] = $retails_price;//$array_line[$_order_retail_price];// 'msrp';
        $data[27] = "";//'gift_wrapping_price';
        //if($array_line[$_order_product_vis]=='Y')
        $data[28]= "Enabled";               //"status";
        //else
        //$data[28]= "Disabled";               //"status";





        $data[29] = "Catalog, Search";//'visibility';
        $data[30] = "No";//'ebizmarts_mark_visited';
        $data[31] = "Taxable Goods"; //'tax_class_id';
        $data[32] = "No";   //'is_recurring';
        $data[33] = $array_line[$_order_product_des];   //'description';
        $data[34] = $array_line[$_order_product_des];   //'short_description';
        $data[35] = $array_line[$_order_taxcode];   //'tax_code';
        $data[36] = '1';    //'depth';
        $data[37] = "1";    //'height';
        $data[38] = "1";    //'width';
        $data[39] = ""; //'fixed_shipping_price';
        $data[40] = $array_line[$_order_meta_kw];   //'meta_keyword';
        $data[41] = ""; //'custom_layout_update';
        $data[42] = ""; //'news_from_date';
        $data[43] = ""; //'news_to_date';
        $data[44] = ""; //'special_from_date';
        $data[45] = ""; //'special_to_date';
        $data[46] = ""; //'custom_design_from';
        $data[47] = ""; //'custom_design_to';
        $data[48] = $array_line[$_order_qty];       //'qty';
        $data[49] = $_order_min_qty;        //'min_qty';
        $data[50] = "1";        //'use_config_min_qty';
        $data[51] = "0";        //'is_qty_decimal';
        $data[52] = "0";        //'backorders';
        $data[53] = "1";        //'use_config_backorders';
        $data[54] = "1";        //'min_sale_qty';
        $data[55] = "1";        //'use_config_min_sale_qty';
        $data[56] = "0";        // 'max_sale_qty';
        $data[57] = "1";        //'use_config_max_sale_qty';
        $data[58] = "1";        //'is_in_stock';
        $data[59] = "";        //'low_stock_date';
        $data[60] = "";        // 'notify_stock_qty';
        $data[61] = "1";        //'use_config_notify_stock_qty';
        $data[62] = "0";        //'manage_stock';
        $data[63] = "1";        //'use_config_manage_stock';
        $data[64] = "0";        //'stock_status_changed_auto';
        $data[65] = "1";        //'use_config_qty_increments';f
        $data[66] = "0";        //'qty_increments';
        $data[67] = "1";        //'use_config_enable_qty_inc';
        $data[68] = "0";        //'enable_qty_increments';
        $data[69] = "0";        //'is_decimal_divided';
        $data[70] = "0";        //'stock_status_changed_automatically';
        $data[71] = "1";        //'use_config_enable_qty_increments';
        $data[72] = $array_line[$_order_name];      //'product_name';
        $data[73] = "0";        //'store_id';
        $data[74] = "simple";       //'product_type_id';
        $optionset=$array_line[$this->getIndex($this->_inputFile,"Option Set")];
        if(!empty($optionset) && empty($_simple)){
            $data[74]= "configurable";//"type"
        }
        $data[75] = "";     //'product_status_changed';
        $data[76] = "";         //'product_changed_websites';
        $data[77] = $array_line[$_order_product_brand];     //'brand';



        //'options','patterns','patters_colours','g_raft_covers'
        $data[78] = $this->process_csvLine_name($array_line[$_order_name],'options');
        $data[79] = $this->process_csvLine_name($array_line[$_order_name],'patterns');
        $data[80] = $this->process_csvLine_name($array_line[$_order_name],'patters_colours');
        $data[81] = $this->process_csvLine_name($array_line[$_order_name],'g_raft_covers');
        $data[82] = $this->process_csvLine_name($array_line[$_order_name],'color');
        $data[83] = $this->process_csvLine_name($array_line[$_order_name],'prints');

        $data[84]=$cat_string;

        $_custom_field=$array_line[$this->getIndex($this->_inputFile,"Product Custom Fields")];
        if(!empty($_custom_field)){
            $data[85] = $this->_findStyle($_custom_field);//'style';
            $data[86] =  $this->_findFunction($_custom_field);//'function';
        }else{
            $data[85] ='' ;//'style';
            $data[86] = '';//'function';
        }



        foreach($data as $dt){
            $dt=(string) $dt;
        };
        return $data;
        /*  $fp = fopen($file, 'a') or die('can not open file');
          fputcsv($fp, $this->add_enclose($data),',', '^');

          fclose($fp);*/
    }

    function getCSVLineSimple($array_line,$current_configure,$filename){
        $_order_category_details=$this->getIndex($this->_inputFile,"Category Details");
        $_order_code=$this->getIndex($this->_inputFile,"Product SKU");/*sku*/
        $_order_name=$this->getIndex($this->_inputFile,"Name");

        $_order_page_title=$this->getIndex($this->_inputFile,"Page Title");
        $_order_meta_des=$this->getIndex($this->_inputFile,"Meta Description");
        $_order_product_url=$this->getIndex($this->_inputFile,"Product URL");

        $_order_product_price=$this->getIndex($this->_inputFile,"Price");
        $_order_retail_price=$this->getIndex($this->_inputFile,"Retail Price");
        $_order_sale_price=$this->getIndex($this->_inputFile,"Sale Price");
        $_order_calculated_price=$this->getIndex($this->_inputFile,"Calculated Price");

        $_order_product_weight=$this->getIndex($this->_inputFile,"Weight");
        $_order_product_des=$this->getIndex($this->_inputFile,"Description");
        $_order_product_vis=$this->getIndex($this->_inputFile,"Product Visible");
        $_order_product_brand=$this->getIndex($this->_inputFile,"Brand");
        $_order_meta_kw=$this->getIndex($this->_inputFile,"Meta Keywords");

        $_order_taxcode=$this->getIndex($this->_inputFile,"Avalara Product Tax Code");
        $_order_qty=$this->getIndex($this->_inputFile,"Stock Level");
        $_order_min_qty=$this->getIndex($this->_inputFile,"Low Stock Level");
        $_order_type=$this->getIndex($this->_inputFile,"Product Type");
        $_order_item_type=$this->getIndex($this->_inputFile,"Item Type");

        if(!$array_line[$_order_code]) $array_line[$_order_code] = ($array_line[$_order_name]?$array_line[$_order_name]:$array_line[$_order_product_url]);

        if($array_line[$_order_code] == "Code") return;
        if(!$array_line[$_order_code] || !$array_line[$_order_name]) {
            echo 'can not find out the sku for the product id: '.$array_line[1]."<br>";
            return;
        }
        $data=array();
        $data[0]= "admin";//"store"
        $data[1]= "base";//"websites"
        $data[2]= "Default";//"attribute_set"
        $data[3]= "simple";//"type"
        $cat_string= $this->_current_configure_product[$_order_category_details];

        $category=$this->FindCategoryId($cat_string,$cat_string);
        if(empty($category)) {
            echo 'can not find out the categry for the product : '.$array_line[$_order_name]."<br>";
            Mage::log('can not findout category', Zend_Log::DEBUG, 'bi_debug_not_cat.log');
            Mage::log($array_line, Zend_Log::DEBUG, 'bi_debug_not_cat.log');
            $data[4]="";
            Mage::log($array_line, Zend_Log::DEBUG, 'bigcomerce_missing_categories.log');
        }else{
            $data[4]=$category;             // "category_ids";
            //$this->_current_configure_product_cat=$category;
        }


        $data[5]= $array_line[$_order_code];    //"sku";

        $data[6]= "0"; //"has_options"

        $data[7]= $current_configure[$_order_name];    // "name";

        $data[8] = '';//'country_of_manufacture';
        $data[9] = 'Use config';//'is_returnable';
        $data[10] = 'Use config';//'msrp_enabled';
        $data[11] = 'Use config';//'msrp_display_actual_price_type';
        $data[12] = $current_configure[$_order_page_title];//'meta_title';
        $data[13] = $current_configure[$_order_meta_des]; // 'meta_description';
        $data[14] = "";//'image';
        $data[15] = "";//'small_image';
        $data[16] = "";//'thumbnail';
        $data[17] = "";//'custom_design';
        $data[18] = "No layout updates";// 'page_layout';
        $data[19] = "Product Info Column";//'options_container';
        $data[20] = "No";//'gift_message_available';
        $data[21] = "No";//'gift_wrapping_available';

        $url=str_replace("/",'',$array_line[$_order_product_url]);
        $data[22] = $url;//'url_key';
        $data[23] = $current_configure[$_order_product_weight];//'weight';


        $price=$current_configure[$_order_product_price];
        $special_price="";
        $retails_price=$array_line[$_order_retail_price];
        $sale_price=$array_line[$_order_sale_price];
        $calculate_price=$array_line[$_order_calculated_price];
        if(!empty($retails_price) && $retails_price > $price && $retails_price > $calculate_price){
            $special_price=$price;
            $price=$retails_price;
        }

        $data[24] = $price;//'price';
        $data[25] = $special_price;// 'special_price';
        $data[26] = $retails_price;//$array_line[$_order_retail_price];// 'msrp';


        $data[27] = "";//'gift_wrapping_price';
        $data[28]= "Enabled";               //"status";
        $data[29] = "Not Visible Individually";//'visibility';
        $optionset=$array_line[$this->getIndex($this->_inputFile,"Option Set")];
        if(!empty($optionset)) $data[29] = "Catalog, Search";//'visibility';

        $data[30] = "No";//'ebizmarts_mark_visited';
        $data[31] = "Taxable Goods"; //'tax_class_id';
        $data[32] = "No";   //'is_recurring';
        $data[33] = $current_configure[$_order_product_des];   //'description';
        $data[34] = $current_configure[$_order_product_des];   //'short_description';
        $data[35] = $current_configure[$_order_taxcode];   //'tax_code';
        $data[36] = '1';    //'depth';
        $data[37] = "1";    //'height';
        $data[38] = "1";    //'width';
        $data[39] = ""; //'fixed_shipping_price';
        $data[40] = $current_configure[$_order_meta_kw];   //'meta_keyword';
        $data[41] = ""; //'custom_layout_update';
        $data[42] = ""; //'news_from_date';
        $data[43] = ""; //'news_to_date';
        $data[44] = ""; //'special_from_date';
        $data[45] = ""; //'special_to_date';
        $data[46] = ""; //'custom_design_from';
        $data[47] = ""; //'custom_design_to';
        $data[48] = $current_configure[$_order_qty];       //'qty';
        $data[49] =  $current_configure[$_order_min_qty];         //'min_qty';
        $data[50] = "1";        //'use_config_min_qty';
        $data[51] = "0";        //'is_qty_decimal';
        $data[52] = "0";        //'backorders';
        $data[53] = "1";        //'use_config_backorders';
        $data[54] = "1";        //'min_sale_qty';
        $data[55] = "1";        //'use_config_min_sale_qty';
        $data[56] = "0";        // 'max_sale_qty';
        $data[57] = "1";        //'use_config_max_sale_qty';
        $data[58] = "1";        //'is_in_stock';
        $data[59] = "";        //'low_stock_date';
        $data[60] = "";        // 'notify_stock_qty';
        $data[61] = "1";        //'use_config_notify_stock_qty';
        $data[62] = "1";        //'manage_stock';
        $data[63] = "1";        //'use_config_manage_stock';
        $data[64] = "0";        //'stock_status_changed_auto';
        $data[65] = "1";        //'use_config_qty_increments';f
        $data[66] = "0";        //'qty_increments';
        $data[67] = "1";        //'use_config_enable_qty_inc';
        $data[68] = "0";        //'enable_qty_increments';
        $data[69] = "0";        //'is_decimal_divided';
        $data[70] = "0";        //'stock_status_changed_automatically';
        $data[71] = "1";        //'use_config_enable_qty_increments';
        $data[72] = $current_configure[$_order_name];      //'product_name';
        $data[73] = "0";        //'store_id';
        $data[74] = "simple";      //'product_type_id';

        $data[75] = "";     //'product_status_changed';
        $data[76] = "";         //'product_changed_websites';
        $data[77] = $current_configure[$_order_product_brand];     //'brand';

        $data[78] = $this->process_csvLine_name($array_line[$_order_name],'options');
        $data[79] = $this->process_csvLine_name($array_line[$_order_name],'patterns');
        $data[80] = $this->process_csvLine_name($array_line[$_order_name],'patters_colours');
        $data[81] = $this->process_csvLine_name($array_line[$_order_name],'g_raft_covers');
        $data[82] = $this->process_csvLine_name($array_line[$_order_name],'color');
        $data[83] = $this->process_csvLine_name($array_line[$_order_name],'prints');

        $data[84]=$cat_string;
        foreach($data as $dt){
            $dt=(string) $dt;
        };
        return $data;
        /*  $fp = fopen($file, 'a') or die('can not open file');
          fputcsv($fp, $this->add_enclose($data),',', '^');

          fclose($fp);*/
    }

    function add_enclose($data) {
        $result=array();
        foreach($data as $dt){
            $result[]="\"$dt\"";
        }
        return $result;
    }

    function FindCategoryId($string,$details){
        /* Category ID: 72, Category Name: Chargers, Category Path: Accessories/Chargers | Category Name: Chargers, Category Path: Accessories/Chargers */
        if(empty($string)){
            return false;
            $this->showdata('This product is missing category in csv export file.');

        }
        /* $catagory_array=array("2" => "Default Category", "3" => "Guitars / Amps / Effects", "4" => "Guitars Efects", "5" => "Guitar Pedal Accessories", "12" => "Lifestyle", "13" => "Bras", "15" => "Scarves", "16" => "Scarves", "21" => "Drums", "23" => "PA Equipment / IT & Audio", "24" => "Sheet Music", "25" => "Piano & Keybroad", "26" => "Bestseller", "27" => "Orchestral Instruments", "28" => "Clearance", "40" => "Guitar Packs", "41" => "Acoustic Guitars", "42" => "Classical Guitars", "43" => "Electric Guitars", "44" => "Bass Guitars", "45" => "Amplifiers", "46" => "Guitar Effects Pedals", "47" => "Guitar Accessories", "48" => "Bass Accessories", "49" => "Folk Instruments", "50" => "Ukuleles", "51" => "Bass Packs", "52" => "Yamaha and Vox Promo", "53" => "Takamine Factory 2nd Clearance", "54" => "Roland V-Guitar Systems", "55" => "Guitar Heads", "56" => "Guitar Cabinets", "57" => "Guitar Combos", "58" => "Acoustic Amps", "59" => "Bass Heads", "60" => "Bass Cabinets", "61" => "Bass Combos", "62" => "Amp Accessories", "63" => "Effects Pedal Accessories", "64" => "Guitar Pedal Accessories", "65" => "Bass Pedals", "66" => "Chorus Pedals", "67" => "Compressor Pedals", "68" => "Delay Pedals", "69" => "Distortion & Overdrive Pedals", "70" => "EQ & Boost Pedals", "71" => "Flanger Pedals", "72" => "Loop Pedals", "73" => "Multi Effects Pedals", "74" => "Phaser Pedals", "75" => "Pitch Pedals", "76" => "Reverb Pedals", "77" => "Tremolo Pedals", "78" => "Tuner Pedals", "79" => "Volume & Expression Pedals", "80" => "Wah & Filter Pedals", "81" => "Wooden Stompbox", "82" => "Guitar Strings", "83" => "Guitar Interface", "84" => "Guitar Cables", "85" => "Rack Effects", "86" => "Guitar Care Products", "87" => "Guitar Cases", "88" => "Guitar Parts", "89" => "Guitar Pickups", "90" => "Guitar Stands", "91" => "Guitar Straps, Picks and Miscellaneous", "92" => "Guitar Tuners & Metronomes", "93" => "Slides & Capos", "94" => "Acoustic Guitar Strings", "95" => "Classical Guitar Strings", "96" => "Electric Guitar Strings", "97" => "Folk Instrument Strings", "98" => "Bass Strings", "99" => "Bass Cases", "100" => "Bass Pickups", "101" => "Bass Parts", "102" => "Resonators", "103" => "Banjos", "104" => "Harmonicas", "105" => "Lap Steels", "106" => "Mandolins", "107" => "Ukuleles", "108" => "Ukulele Accessories", "109" => "Cases & Bags", "110" => "Acoustic Drums", "111" => "Drum Amps / Monitors", "112" => "Electronic Drums", "113" => "Snare Drums", "114" => "Cymbals", "115" => "Drum Hardware, Stools & Pedals", "116" => "Drum Heads", "117" => "Percussion", "118" => "Drum Accessories", "119" => "Drumsticks and Mallets", "120" => "Drum Monitors", "121" => "Electronic Drum Accessories", "122" => "Snare Drum Heads", "123" => "Tom Heads", "124" => "Bass Drum Heads", "125" => "Drum Head Packs", "126" => "Megaphones", "127" => "Vocal Effects", "128" => "DJ Products", "129" => "DI Boxes", "130" => "Microphones", "131" => "iPhone & iPad Accessories", "132" => "Recording", "133" => "Cables", "134" => "PA Systems", "135" => "Mixers", "136" => "Speakers", "137" => "Foldback Monitors", "138" => "Power Amps", "139" => "Accessories", "140" => "Speaker Stands", "141" => "In Ear Monitoring", "142" => "Lighting", "143" => "Rack Gear", "144" => "Rack Cases", "145" => "Testers and Hum Eliminators", "146" => "Stage Microphones", "147" => "Studio Microphones", "148" => "Wireless Microphone", "149" => "Microphone Accessories", "150" => "Microphone Stands", "151" => "Mic Cables", "152" => "Controller Keyboards", "153" => "Studio Mon", "154" => "Recording Accessories", "155" => "Audio and MIDI Interfaces", "156" => "Digital Recorders", "157" => "Headphones", "158" => "Studio Monitors", "159" => "Outboard Gear", "160" => "Acoustic Treatments", "161" => "CD and DVD Recorders", "162" => "Consoles", "163" => "Control Surfaces", "164" => "Software", "165" => "Instrument Cables", "166" => "Microphone Cables", "167" => "Speaker Cables", "168" => "Adaptors & Plugs", "169" => "MIDI Cables", "170" => "Patch Cables", "171" => "Insert & Y Cables", "172" => "Single Interconnect Cables", "173" => "Dual Interconnect Cables", "174" => "Digital Cables", "175" => "Multicores and Looms", "176" => "Brass Instruments", "177" => "Woodwind", "178" => "Stringed Instruments", "179" => "Orchestral Percussion", "180" => "Metronomes", "181" => "Orchestral Tuners", "182" => "Orchestral Stands", "183" => "Conductors Batons", "184" => "Trumpets", "185" => "Lower Brass", "186" => "Brass Mouthpieces", "187" => "Brass Accessories", "188" => "French Horns", "189" => "Clarinets", "190" => "Flutes and Piccolos", "191" => "Saxophones", "192" => "Recorders", "193" => "Double Reed", "194" => "Woodwind Instrument Accessories", "195" => "Electronic Wind Instruments", "196" => "Accessories", "197" => "Strings Accessories", "198" => "Strings", "199" => "Violin", "200" => "Viola", "201" => "Violin and Viola Strings", "202" => "Cello", "203" => "Double Bass", "204" => "Cello and Double Bass Strings", "205" => "Electric Stringed Instruments", "206" => "Stringed Instrument Accessories", "207" => "Tuned Percussion", "208" => "Untuned Percussion", "209" => "Alto Saxophone Books", "210" => "AMEB Publishing", "211" => "Aural Books", "212" => "Baritone Saxophone Books", "213" => "Bass Guitar Books", "214" => "Broadway and Movie Books", "215" => "Cello Books", "216" => "Choral 2 Part Books", "217" => "Choral 3 Part Mixed Books", "218" => "Choral SATB Books", "219" => "Choral SSA Books", "220" => "Choral SSAA and TTBB Books", "221" => "Choral Unison Books", "222" => "Christmas Music Books", "223" => "Clarinet Books", "224" => "Classical Guitar Books", "225" => "Concert Band Books", "226" => "Double Bass Books", "227" => "Drums and Percussion Books", "228" => "Easy Piano Books", "229" => "Flute Books", "230" => "Folk Instrument Books", "231" => "French Horn Books", "232" => "Guitar Books", "233" => "Guitar DVD", "234" => "Guitar Method Books", "235" => "Guitar TAB Books", "236" => "Home Recording Books", "237" => "Jazz Ensemble Books", "238" => "Jazz Play Along Books - All Instruments", "239" => "Keyboard Books", "240" => "Lower Brass Books", "241" => "Manuscript", "242" => "Oboe and Bassoon Books", "243" => "Piano Duet Books", "244" => "Piano Play Along Books", "245" => "Piano Solo Books", "246" => "Piano Vocal Guitar Books", "247" => "Recorder Books", "248" => "Reference Books", "249" => "Single Sheets", "250" => "String Orchestra Books", "251" => "Teacher Resources", "252" => "Tenor Saxophone", "253" => "Theory Books", "254" => "Trombone Books", "255" => "Trumpet Books", "256" => "Viola Books", "257" => "Violin Books", "258" => "Vocal Books", "259" => "AMEB AURAL TRAINING", "260" => "AMEB BASSOON", "261" => "AMEB BRASS", "262" => "AMEB CELLO", "263" => "AMEB CLARINET", "264" => "AMEB CPM", "265" => "AMEB FLUTE", "266" => "AMEB GUITAR", "267" => "AMEB LOWER BRASS", "268" => "AMEB MUSIC CRAFT", "269" => "AMEB OBOE", "270" => "AMEB PIANO", "271" => "AMEB PIANO FOR LEISURE", "272" => "AMEB RECORDER", "273" => "AMEB SAXOPHONE", "274" => "AMEB SAXOPHONE FOR LEISURE", "275" => "AMEB SINGING", "276" => "AMEB SINGING FOR LEISURE", "277" => "AMEB SYLLABUS", "278" => "AMEB TRUMPET", "279" => "AMEB VIOLA", "280" => "AMEB VIOLIN", "281" => "Acoustic Pianos", "282" => "Digital Pianos", "283" => "Keyboards", "284" => "Keyboard Amps", "285" => "Hammond & Rodgers Organs", "286" => "Synthesizers and Sound Modules", "287" => "Controller Keyboards", "288" => "Stage Keyboards", "289" => "Piano & Keyboard Accessories", "290" => "Secondhand", "291" => "Grand Pianos", "292" => "Upright Pianos", "293" => "FLASH SALE", "294" => "EVENT TICKETS", "295" => "Admin", "296" => "Prestige & Limited Edition","297" =>"Piano Method Books",
             "298"=>"Cases & Bags",
             "299"=>"Woodwind Mouthpieces",
             "300"=>"Woodwind Accessories",
             "301"=>"Single Reeds",
             "302"=>"Double Reeds",

         );*/
        $catagory_array=$this->_categories;
        $categories=array();
        $cat_array=explode("|",$string);
        foreach($cat_array as $cat){  /* Category ID: 72, Category Name: Chargers, Category Path: Accessories/Chargers */

            $name_arr=explode(",",$cat);
            $cat_name='';
            $cat_name =str_replace("Category Name: ","",$name_arr[1]);
            if(!empty($cat_name)){
                foreach($catagory_array as $index => $category){
                    if(trim(strtolower($category['name'])) == trim(strtolower($cat_name)))
                    {
                        $path_csv= str_replace("Category Path: ","",$name_arr[2]);
                        $path_mgt=$category['parent']."/".str_replace("/","^",$category['name']);
                        $pos = strpos($path_csv, $path_mgt);
                        if ($pos !== false) {
                            $categories[] =$index;
                        } else {
                            if(trim(strtolower($cat_name)) == trim(strtolower($path_csv))) $categories[] =$index;
                        }

                    }
                }
            }


        }
        if(empty($categories)) {
            echo 'can not find out the categry  : '.$string."\n";
            return false;
        }
        return implode(",",$categories);
    }
    /*$type: text,int
    $input: text,select

    */
    public function getMagentoCategory(){
        $category = Mage::getModel('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        $magento_cat=array();
        if ($ids){
            foreach ($ids as $id){
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                $parent=Mage::getModel('catalog/category')->load($cat->getParentId());
                $parent_name=$parent->getName();
                $entity_id = $cat->getId();
                $name = $cat->getName();
                $magento_cat[$entity_id]=array('name'=>$name,'parent' =>$parent_name);
                //Mage::log($cat->getData(), Zend_Log::DEBUG, 'bi_debug5.log');
            }
        }
        return $magento_cat;
    }

    /**process attribute*/
    public function addAttributeText($code,$name,$groupname){
        $setup = new Mage_Eav_Model_Entity_Setup();
        $attb = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode('catalog_product',$code);
        if(null===$attb->getId()) {
            echo $code." is not exists!"."\n";

            $setup->addAttribute('catalog_product', $code, array(
                'input'         => 'text',
                'type'          => 'text',
                'label'         => $name,
                'user_defined'  => false,
                'visible'       => 1,
                'required'      => 0,
                'position'    => 340,
            ));

            $this->addAttributeintoSet($code,$groupname);
        }else {
            echo $code." is exists!\n";
        }
    }
    public function addAttributeOption($code,$name,$groupname){
        $setup = new Mage_Eav_Model_Entity_Setup();
        $attb = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode('catalog_product',$code);
        if(null===$attb->getId()) {
            echo $code." is not exists!"."\n";

            $setup->addAttribute('catalog_product', $code, array(
                'input'         => 'select',
                'type'          => 'int',
                'label'         => $name,
                'user_defined'  => false,
                'visible'       => 1,
                'required'      => 0,
                'position'    => 340,
                'backend'    => 'eav/entity_attribute_backend_array',
                'option'     => array (
                    'values' => array(
                        1 => 'Yes',
                        2 => 'No',
                    )
                ),
            ));

            $this->addAttributeintoSet($code,$groupname);
        }else {
            echo $code." is exists!\n";
        }
    }

    public function addAttributeDropdown($code,$name,$groupname){
        $setup = new Mage_Eav_Model_Entity_Setup();
        $attb = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode('catalog_product',$code);
        if(null===$attb->getId()) {
            echo $code." is not exists!"."\n";

            $setup->addAttribute('catalog_product', $code, array(
                'input'         => 'select',
                'type'          => 'int',
                'label'         => $name,
                'user_defined'  => false,
                'visible'       => 1,
                'required'      => 0,
                'user_defined' => true,
                'position'    => 340,
            ));

            $this->addAttributeintoSet($code,$groupname);
        }else {
            echo $code." is exists!\n";
        }
    }

    public function addAttributeintoSet($code,$groupname){
        if(empty($code)) {
            echo "empty code!"."\n";
            return;
        }
        $attSet = Mage::getModel('eav/entity_type')->getCollection()->addFieldToFilter('entity_type_code','catalog_product')->getFirstItem(); // This is because the you adding the attribute to catalog_products entity ( there is different entities in magento ex : catalog_category, order,invoice... etc )
        $attSetCollection = Mage::getModel('eav/entity_type')->load($attSet->getId())->getAttributeSetCollection(); // this is the attribute sets associated with this entity
        $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setCodeFilter($code)
            ->getFirstItem();
        $attCode = $attributeInfo->getAttributeCode();
        $attId = $attributeInfo->getId();
        foreach ($attSetCollection as $a)
        {
            $set = Mage::getModel('eav/entity_attribute_set')->load($a->getId());
            $setId = $set->getId();
            $group=null;
            $collection=Mage::getModel('eav/entity_attribute_group')->getCollection()->addFieldToFilter('attribute_set_id',$setId)->setOrder('attribute_group_id',ASC);
            foreach($collection as $gr){
                if($gr->getData('attribute_group_name')== $groupname){
                    $group= $gr;

                }
            }
            if(!empty($group)){
                $groupId = $group->getId();
                $newItem = Mage::getModel('eav/entity_attribute');
                $newItem->setEntityTypeId($attSet->getId()) // catalog_product eav_entity_type id ( usually 10 )
                    ->setAttributeSetId($setId) // Attribute Set ID
                    ->setAttributeGroupId($groupId) // Attribute Group ID ( usually general or whatever based on the query i automate to get the first attribute group in each attribute set )
                    ->setAttributeId($attId) // Attribute ID that need to be added manually
                    ->setSortOrder(10) // Sort Order for the attribute in the tab form edit
                    ->save()
                ;
                echo "Attribute ".$attCode." Added to Attribute Set ".$set->getAttributeSetName()." in Attribute Group ".$group->getAttributeGroupName()."\n";
            }else{
                $setup = new Mage_Eav_Model_Entity_Setup();
                $setup->addAttributeGroup('catalog_product', $a->getId(), $groupname, 1000);
                $collection=Mage::getModel('eav/entity_attribute_group')->getCollection()->addFieldToFilter('attribute_set_id',$setId)->setOrder('attribute_group_id',ASC);
                foreach($collection as $gr){
                    if($gr->getData('attribute_group_name')== $groupname){
                        $group= $gr;

                    }
                }
                $groupId = $group->getId();
                $newItem = Mage::getModel('eav/entity_attribute');
                $newItem->setEntityTypeId($attSet->getId()) // catalog_product eav_entity_type id ( usually 10 )
                    ->setAttributeSetId($setId) // Attribute Set ID
                    ->setAttributeGroupId($groupId) // Attribute Group ID ( usually general or whatever based on the query i automate to get the first attribute group in each attribute set )
                    ->setAttributeId($attId) // Attribute ID that need to be added manually
                    ->setSortOrder(10) // Sort Order for the attribute in the tab form edit
                    ->save()
                ;
                echo "Attribute ".$attCode." Added to Attribute Set ".$set->getAttributeSetName()." in Attribute Group ".$group->getAttributeGroupName()."\n";
            }

        }




    }

    public function addAttributeValue($arg_attribute, $arg_value)
    {
        $attribute_model        = Mage::getModel('eav/entity_attribute');

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        if(!$this->attributeValueExists($arg_attribute, $arg_value))
        {
            $value['option'] = array($arg_value,$arg_value);
            $result = array('value' => $value);
            $attribute->setData('option',$result);
            $attribute->save();
        }

        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option)
        {
            if ($option['label'] == $arg_value)
            {
                return $option['value'];
            }
        }
        return false;
    }
    public function attributeValueExists($arg_attribute, $arg_value)
    {
        $attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option)
        {
            if ($option['label'] == $arg_value)
            {
                return $option['value'];
            }
        }

        return false;
    }


    public function process_attribute(){
        /*Add extra attributes;*/
        $this->addAttributeText("tax_code","Product Tax Code","General");
        $this->addAttributeText("width","Width","General");
        $this->addAttributeText("height","Height","General");
        $this->addAttributeText("depth","Depth","General");

        $this->addAttributeText("gps_gtin","GPS Global Trade Item Number","General");
        $this->addAttributeText("gps_mpn","GPS Manufacturer Part Number","General");
        $this->addAttributeText("gps_gender","GPS Gender","General");
        $this->addAttributeText("gps_ag","GPS Age Group","General");
        $this->addAttributeText("gps_color","GPS Color","General");
        $this->addAttributeText("gps_size","GPS Size","General");
        $this->addAttributeText("gps_material","GPS Material","General");
        $this->addAttributeText("gps_pattern","GPS Pattern","General");
        $this->addAttributeText("gps_igi","GPS Item Group ID","General");
        $this->addAttributeText("gps_cat","GPS Category","General");

        $this->addAttributeText("myob_aa","MYOB Asset Acct","General");
        $this->addAttributeText("myob_ia","MYOB Income Acct","General");
        $this->addAttributeText("myob_ea","MYOB Expense Acct","General");

        $this->addAttributeText("upc_ean","Product UPC/EAN","General");
        $this->addAttributeText("avalara_taxcode","Avalara Product Tax Code","General");
        $this->addAttributeText("sort_order","Sort Order","General");

        $this->addAttributeText("bin_picking_num","Bin Picking Number","General");
        $this->addAttributeText("gps_custom_item","GPS Custom Item","General");
        $this->addAttributeText("gps_age_group","GPS Age Group","General");

        $this->addAttributeText("warranty","Warranty","General");


        $this->addAttributeText("style","Style","General");
        $this->addAttributeText("function","Function","General");


        $this->addAttributeOption("gps_enable","GPS Enabled","General");

        $this->addAttributeText("fixed_shipping_price","Fixed Shipping Price","Prices");

        /*add option value for attribute brand*/
        $brand_option=$this->ReadExportCSV_getBrand($this->_inputFile);
        $i=0;
        foreach($brand_option as $brand){
            if(empty($brand)) continue;
            $this->addAttributeValue("brand", $brand);
            echo 'adding brand option: '.$brand."\n";
            echo 'adding'.$i."\n";
            $i++;
        }

        $this->addAttributeDropdown('options','Options',"General");
        $this->addAttributeDropdown('patterns','Patterns',"General");
        $this->addAttributeDropdown('patters_colours','Patters / Colours',"General");
        $this->addAttributeDropdown('g_raft_covers','G Raft Covers',"General");
        $this->addAttributeDropdown('prints','Prints',"General");
        //$color_option=$this->ReadExportCSV_getColor($this->_inputFile);
        $color_option=$this->ReadExportCSV_setColor($this->_inputFile);

    }
    public function ReadExportCSV_setColor($csvFile){
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        /*write the header of csv file*/
        $_colors=array();
        while (!feof($file_handle) ) {
            //if($i>=15) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            //$this->showdata($line_of_text[1]);
            if(empty($line_of_text[1]) && empty($line_of_text[3])) continue;
            if($line_of_text[0] == 'Product ID') continue;
            if($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Product Type")]) =="P"){ /*ignore configurable product*/
                continue;
            }elseif($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Item Type")])  =="SKU" || $this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Item Type")]) =="Rule"){ /*rule or simple product*/
                $name=$line_of_text[$this->getIndex($this->_inputFile,"Name")];
                Mage::log($name, Zend_Log::DEBUG, 'bi_debug1.log');
                $newcolors=$this->process_add_colour($name);
                /*$data=$this->getCSVLine($line_of_text,$outputFile);
                if(!empty($data))
                    $csvdata[]=$this->getCSVLine($line_of_text,$outputFile);*/
            }
        }
        fclose($file_handle);
        Mage::log($this->_new_attributes, Zend_Log::DEBUG, 'bi_debug1.log');
        //Mage::log($_brands, Zend_Log::DEBUG, 'bi_debug.log');
        return $_colors ;
    }

    function process_add_colour($name=null) {
        $new_atrributes_dropdown=array(
            'options','patterns','patters_colours','g_raft_covers','prints'
        );
        $colors=$this->_color_label;
        $patterns=$this->_patterns;
        $patters_colours=$this->_patters_colours;
        $g_raft_covers=$this->_g_raft_covers;
        $_options=$this->_options;
        $_prints=$this->_prints;
        if(empty($name)) return false;
        $name_arr=explode("]",$name);
        $name_arr1=explode(",",$name_arr[1]);
        foreach($name_arr1 as $color){
            $color_str=explode('=',$color);
            $color_str[1]=$this->_replace_sign_from_attribute_label($color_str[1]);

            if(!empty($color_str[0]) && !in_array($color_str[0],$this->_new_attributes)) {
                $attribute=$color_str[0];
                if(in_array($attribute,$colors)){
                    $color=explode(':',$color_str[1]);
                    //$this->addAttributeValue("color", $color[0]);
                    $this->addAttributeValue("color", $color_str[1]);
                }
                if(in_array($attribute,$patterns)){
                    $this->addAttributeValue("patterns", $color_str[1]);
                }
                if(in_array($attribute,$patters_colours)){
                    $this->addAttributeValue("patters_colours", $color_str[1]);
                }
                if(in_array($attribute,$g_raft_covers)){
                    $this->addAttributeValue("g_raft_covers", $color_str[1]);
                }
                if(in_array($attribute,$_options)){
                    $this->addAttributeValue("options", $color_str[1]);
                }
                if(in_array($attribute,$_prints)){
                    $this->addAttributeValue("prints", $color_str[1]);
                }
                //$this->_new_attributes_value[$color_str[0]]=
            }
            //if(!empty($color_str[0]) && !in_array($color_str[0],$this->_new_attributes)) $this->_new_attributes[]= $color_str[0];
        }
        return $this->_new_attributes;
    }
    function process_csvLine_name($name,$_attr) {
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
            $color_str[1]=$this->_replace_sign_from_attribute_label($color_str[1]);
            if(!empty($color_str[0])) {
                $attribute=$color_str[0];
                if(in_array($attribute,$colors) && $_attr =='color'){
                    $color=explode(":",$color_str[1]);
                    //return $color[0];
                    return $color_str[1];
                }
                if(in_array($attribute,$patterns) && $_attr =='patterns'){
                    return $color_str[1];
                }
                if(in_array($attribute,$patters_colours) && $_attr =='patters_colours'){
                    return $color_str[1];
                }
                if(in_array($attribute,$g_raft_covers) && $_attr =='g_raft_covers'){
                    return $color_str[1];
                }
                if(in_array($attribute,$_options) && $_attr =='options' ){
                    return $color_str[1];
                }
                if(in_array($attribute,$_prints) && $_attr =='prints' ){
                    return $color_str[1];
                }
                //$this->_new_attributes_value[$color_str[0]]=
            }
            //if(!empty($color_str[0]) && !in_array($color_str[0],$this->_new_attributes)) $this->_new_attributes[]= $color_str[0];
        }
        return '';
    }
    public function ReadExportCSV_getColor($csvFile){
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        /*write the header of csv file*/
        $_colors=array();
        while (!feof($file_handle) ) {
            //if($i>=15) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            //$this->showdata($line_of_text[1]);
            if(empty($line_of_text[1]) && empty($line_of_text[3])) continue;
            if($line_of_text[0] == 'Product ID') continue;
            if($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Product Type")]) =="P"){ /*ignore configurable product*/
                continue;
            }elseif($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Item Type")])  =="SKU" || $this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Item Type")]) =="Rule"){ /*rule or simple product*/
                $name=$line_of_text[$this->getIndex($this->_inputFile,"Name")];
                Mage::log($name, Zend_Log::DEBUG, 'bi_debug1.log');
                $newcolors=$this->process_colour_in_name($name);
                /*$data=$this->getCSVLine($line_of_text,$outputFile);
                if(!empty($data))
                    $csvdata[]=$this->getCSVLine($line_of_text,$outputFile);*/
            }
        }
        fclose($file_handle);
        Mage::log($this->_new_attributes, Zend_Log::DEBUG, 'bi_debug1.log');
        //Mage::log($_brands, Zend_Log::DEBUG, 'bi_debug.log');
        return $_colors ;
    }

    /**
     * @param null $name :[RB]Colours=Purple,Colours=Mint,Colours=Hot Pink
     * @return bool
     */
    function process_colour_in_name($name=null) {
        if(empty($name)) return false;
        $name_arr=explode("]",$name);
        $name_arr1=explode(",",$name_arr[1]);
        foreach($name_arr1 as $color){
            $color_str=explode('=',$color);
            if(!empty($color_str[0]) && !in_array($color_str[0],$this->_new_attributes)) {
                $this->_new_attributes[]= $color_str[0];
                //$this->_new_attributes_value[$color_str[0]]=
            }
            //if(!empty($color_str[0]) && !in_array($color_str[0],$this->_new_attributes)) $this->_new_attributes[]= $color_str[0];
        }
        return $this->_new_attributes;
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

    /**
     * @param string $field :"Style=Hard Case";Function=Protective
     */
    function _findStyle($field=""){
        $field=str_replace('"', "", $field);
        $field=explode(";",$field);
        return str_replace("Style=","",$field[0]);
    }

    function _findFunction($field=""){
        $field=str_replace('"', "", $field);
        $field=explode(";",$field);
        return str_replace("Function=","",$field[1]);
    }

}

$shell = new Mage_Shell_Compiler();
$shell->run();
