<?php
class Tromvia_Nhapsanpham_Model_Service_Nhapsanpham
{
	
    public $_categories=array();
    public $_qty_map=array();
    public $_inputFile = 'input.csv';   
    public $_productFile="product.csv";
    
    public $_match_csv_header=array();
    public $_current_configure_product=null;
    public $_current_configure_product_cat=array();

    public $_new_attributes=array();
    public $_new_attributes_value=array();

    public  function setInputFile($filename){
		$this->_inputFile=$filename;
	}	
	public  function setProductFile($filename){
		$this->_productFile=$filename;
	}
	public function getImportDirectory(){
		return Mage::helper('nhapsanpham')->_getUploadFolder().DS;
	}
	public function getGeneratedDirectory(){
		return Mage::helper('nhapsanpham')->_getGeneratedFolder().DS;
	}
    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }

    public function generateSku($csvFile)
    {

        $fileInput = fopen($this->getImportDirectory().$csvFile, 'r');
        $i=0;
        $csv = new Varien_File_Csv();
        $csv->setLineLength(20480);
        $csvdata = array();
        /*write the header of csv file*/
        $csvdata[] = $this->getHeaderImportedFileSku();

        while (!feof($fileInput) ) {
            $data=array();
            //if($i>=15) break;
            $i++;
            $line_of_text = fgetcsv($fileInput, 20480);

            if(empty($line_of_text[$this->getIndex($this->_inputFile,"ten")]) && empty($line_of_text[$this->getIndex($this->_inputFile,"gia_ban")])) continue;
            if($line_of_text[$this->getIndex($this->_inputFile,"ten")] == 'ten') continue;
            $data[0]=Mage::helper('nhapsanpham')->generateSkuByNameFirstChar($line_of_text[$this->getIndex($this->_inputFile,"ten")]);
            foreach($line_of_text as $field){
                $data[]=$field;
            }
            $csvdata[]=$data;
        }
        fclose($fileInput);

        $file=$this->getImportDirectory().$csvFile;
        $csv->saveData($file, $csvdata);
		
        return ;
    }
        public function run()
    {    
		$_qty_map=array();
		$_qty_map_log=array();
		foreach(Mage::getModel('catalog/product')->getCollection() as $product){
			$stock=Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
			$_qty_map[$product->getSku()]=$stock->getQty();
			$_qty_map_log[]=array($product->getName(),$product->getSku(),$stock->getQty());
			
		}
		$this->_qty_map=$_qty_map;
		$file=$this->getImportDirectory()."backup".DS."backup_".$this->_productFile;
        $csv = new Varien_File_Csv();
        $csv->setLineLength(20480);
		$csv->saveData($file, $_qty_map_log);  
		
		$this->showdata("remembered the qty map!");
        $this->process_attribute();
        $result=$this->ReadExportCSV($this->_inputFile,$this->_productFile);
    }
    
    public function getIndex($csvFile,$columnname){
        $line_of_text=array();
        if(!empty($this->_match_csv_header)) $line_of_text=$this->_match_csv_header;
        else{
            $file_handle = fopen($this->getImportDirectory().$csvFile, 'r');
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
        $file_handle = fopen($this->getImportDirectory().$csvFile, 'r');
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
    }

    function cut_space($string=''){
        return str_replace(" ","",$string);
    }
    public function ReadExportCSV_getBrand($csvFile){
        $file_handle = fopen($this->getImportDirectory().$csvFile, 'r');
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
        $file_handle = fopen($this->getImportDirectory().$csvFile, 'r');
        $i=0;
        $file=$this->getGeneratedDirectory().$outputFile;
        $csv = new Varien_File_Csv();
        $csv->setLineLength(20480);
        $csvdata = array();
        /*write the header of csv file*/
        $csvdata[] = $this->getHeaderCSVLine();
       
        while (!feof($file_handle) ) {
            //if($i>=15) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
           
            if(empty($line_of_text[0]) && empty($line_of_text[1])) continue;
            if($line_of_text[0] == 'ten') continue;
			$data=$this->getCSVLineSimple($line_of_text);
                if(!empty($data)){
                    $csvdata[]=$this->getCSVLineSimple($line_of_text);
                }            
        }
        $csv->saveData($file, $csvdata);
        fclose($file_handle);
        ///return $line_of_text;
        return ;
    }
    function _is_configureable(){

    }
    
  function getCSVLineSimple($array_line){
        $_order_code=$this->getIndex($this->_inputFile,"ma_sp");/*sku*/
        $_order_name=$this->getIndex($this->_inputFile,"ten");

        $_order_product_price=$this->getIndex($this->_inputFile,"gia_ban");
        $_order_cost=$this->getIndex($this->_inputFile,"gia_buon");
        $_order_color=$this->getIndex($this->_inputFile,"mau_sac");
        $_order_qty=$this->getIndex($this->_inputFile,"so_luong");
		
		$current_qty=$this->_qty_map[$_order_code];
		if(empty($current_qty)) $current_qty=0;
		$qty=$current_qty +(int)$array_line[$_order_qty];
		
		
        if(!$array_line[$_order_code]) $array_line[$_order_code] = ($array_line[$_order_name]?$array_line[$_order_name]:$array_line[$_order_product_url]);

        if($array_line[$_order_code] == "ma_sp") return;
        if(!$array_line[$_order_code] || !$array_line[$_order_name]) {
            echo 'can not find out the sku for the product id: '.$array_line[1]."<br>";
            return;
        }
        $data=array();
        $data[0]= "admin";//"store"
        $data[1]= "base";//"websites"
        $data[2]= Mage::helper('core')->__("Đồ sơ sinh");//"attribute_set"
        $data[3]= Mage::helper('core')->__("simple");//"type"
       

        $data[4]='';


        $data[5]= $array_line[$_order_code];    //"sku";

        $data[6]= "0"; //"has_options"

        $data[7]= $array_line[$_order_name];    // "name";

        $data[8] = '';//'country_of_manufacture';
        $data[9] = Mage::helper('core')->__('Use config');//'is_returnable';
        $data[10] = Mage::helper('core')->__('Use config');//'msrp_enabled';
        $data[11] = Mage::helper('core')->__('Use config');//'msrp_display_actual_price_type';
        $data[12] = $array_line[$_order_page_title];//'meta_title';
        $data[13] = $array_line[$_order_meta_des]; // 'meta_description';
        $data[14] = "";//'image';
        $data[15] = "";//'small_image';
        $data[16] = "";//'thumbnail';
        $data[17] = "";//'custom_design';
        $data[18] = Mage::helper('core')->__("No layout updates");// 'page_layout';
        $data[19] = Mage::helper('core')->__("Product Info Column");//'options_container';
        $data[20] = "No";//'gift_message_available';
        $data[21] = "No";//'gift_wrapping_available';

        $url=str_replace("/",'',$array_line[$_order_product_url]);
        $data[22] = $url;//'url_key';
        $data[23] = "1";//'weight';


        $price=$array_line[$_order_product_price];
        

        $data[24] = $price;//'price';
        $data[25] = "";// 'special_price';
        $data[26] = $array_line[$_order_cost];//$array_line[$_order_retail_price];// 'msrp';


        $data[27] = "";//'gift_wrapping_price';
        $data[28]= Mage::helper('core')->__("Enabled");               //"status";
        $data[29] = Mage::helper('core')->__("Not Visible Individually");//'visibility';
         $data[29] = Mage::helper('core')->__("Catalog, Search");//'visibility';

        $data[30] = Mage::helper('core')->__("No");//'ebizmarts_mark_visited';
        $data[31] = Mage::helper('core')->__("Taxable Goods"); //'tax_class_id';
        $data[32] = Mage::helper('core')->__("No");   //'is_recurring';
        $data[33] = "";   //'description';
        $data[34] = "";   //'short_description';
        $data[35] = "";   //'tax_code';
        $data[36] = '1';    //'depth';
        $data[37] = "1";    //'height';
        $data[38] = "1";    //'width';
        $data[39] = ""; //'fixed_shipping_price';
        $data[40] = "";   //'meta_keyword';
        $data[41] = ""; //'custom_layout_update';
        $data[42] = ""; //'news_from_date';
        $data[43] = ""; //'news_to_date';
        $data[44] = ""; //'special_from_date';
        $data[45] = ""; //'special_to_date';
        $data[46] = ""; //'custom_design_from';
        $data[47] = ""; //'custom_design_to';
        $data[48] = $qty;       //'qty';
        $data[49] =  "1";         //'min_qty';
        $data[50] = "1";        //'use_config_min_qty';
        $data[51] = "0";        //'is_qty_decimal';
        $data[52] = "0";        //'backorders';
        $data[53] = "1";        //'use_config_backorders';
        $data[54] = "1";        //'min_sale_qty';
        $data[55] = "1";        //'use_config_min_sale_qty';
        $data[56] = "10000";        // 'max_sale_qty';
        $data[57] = "1";        //'use_config_max_sale_qty';
        $data[58] = "1";        //'is_in_stock';
        $data[59] = "";        //'low_stock_date';
        $data[60] = "";        // 'notify_stock_qty';
        $data[61] = "1";        //'use_config_notify_stock_qty';
        $data[62] = "1";        //'manage_stock';
        $data[63] = "0";        //'use_config_manage_stock';
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
        $data[74] = Mage::helper('sales')->__("simple");      //'product_type_id';

        $data[75] = "";     //'product_status_changed';
        $data[76] = "";         //'product_changed_websites';
        $data[77] = $array_line[$_order_product_brand];     //'brand';
        
		$data[78] = $array_line[$_order_color];
        $data[79] = $array_line[$_order_cost];
		
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
       $new_atrributes_dropdown=array(
            'chat_lieu'=>'chat_lieu','kieu_loai'=>'kieu_loai','kich_co'=>'ss_size','mau_sac'=>'color',
			'xuat_xu'=>'country_of_manufacture'
        );
        foreach($new_atrributes_dropdown as $csv_column=>$attb){
			$value=$this->ReadExportCSV_setSetAttributeValue($attb,$csv_column);
			foreach($value as $val){
				if(empty($val)) continue;
				$this->addAttributeValue($attb, $val);
				echo 'adding '.$attb.'option: '.$val."\n";
				echo 'adding'.$i."\n";
				$i++;
			}
		}		
    }
    public function ReadExportCSV_setSetAttributeValue($magento_attb,$csv_attb){
		$value_array=array();
        $file_handle = fopen($this->getImportDirectory().$this->_inputFile, 'r');
        $i=0;
        /*write the header of csv file*/       
        while (!feof($file_handle) ) {
            //if($i>=15) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            //$this->showdata($line_of_text[1]);
            if(empty($line_of_text[0]) && empty($line_of_text[1])) continue;
            if($line_of_text[0] == 'ten') continue;		
			$value=$line_of_text[$this->getIndex($this->_inputFile,$csv_attb)];
			if(!in_array($value,$value_array)) $value_array[]=$value;			
        }
        fclose($file_handle);      
        return $value_array;
    }

    function process_add_colour($line=null) {
        
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
        $file_handle = fopen($this->getImportDirectory().$csvFile, 'r');
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
    function getHeaderImportedFileSku(){
        $data=array("ma_sp","ten","don_vi","gia_buon","gia_ban","so_luong");
        return $data;
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
        $data[78] = 'color';
        $data[79] = 'cost';
        

        return $data;
        /*$fp = fopen($file, 'a') or die('can not open file');
        fputcsv($fp, $this->add_enclose($data),',','^');
        fclose($fp);*/
    }
}



