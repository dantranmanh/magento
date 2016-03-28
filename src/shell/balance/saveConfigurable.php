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
        //$this->process_supper_attributes();
        //$result=$this->ReadExportCSV($this->_inputFile,$this->_productFile);
        $empty=$this->check_empty_configurable_product();
        $this->showdata('there are '.count($empty).' configurable product that has no associate simple product.Read file var/log/bi_debug_empty_configurable_product.log for more details');
        Mage::log($empty, Zend_Log::DEBUG, 'bi_debug_empty_configurable_product.log');

    }
    public function ReadExportCSV($csvFile,$outputFile){
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        $file=Mage::getBaseDir('media') . DS . 'import'. DS .$outputFile;
        $csv = new Varien_File_Csv();
        $csv->setLineLength(20480);
        $csvdata = array();
        /*write the header of csv file*/
        $current_product=null;
        while (!feof($file_handle) ) {
            //if($i>=25) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            if(empty($line_of_text[3])) $line_of_text[3]='PRD-'.$line_of_text[1];
            //$this->showdata($line_of_text[1]);
            if(empty($line_of_text[1]) && empty($line_of_text[3])) continue;
            if($line_of_text[0] == 'Product ID') continue;
            $optionset=$line_of_text[$this->getIndex($this->_inputFile,"Option Set")];
            if($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Product Type")]) =="P"){ /*create configurable product*/
                if(!empty($optionset)){
                    $configurable=$this->_current_configure_product;
                    if($configurable && !empty($this->_current_configure_product_supper_attr)){
                        $this->create_configurable_product($configurable,$this->_current_configure_product_supper_attr);
                    }
                    $this->_current_configure_product=$line_of_text;
                }
            }elseif($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Item Type")]) =="SKU"){ /*create simple product*/
                $data=$this->findAtributte_csvLine_name($line_of_text[$this->getIndex($this->_inputFile,"Name")]);
                if(empty($data)){
                    Mage::log($line_of_text, Zend_Log::DEBUG, 'bi_debug3.log');
                }else{
                    $this->_current_configure_product_supper_attr=$data;
                }

            }
        }
        //$csv->saveData($file, $csvdata);
        fclose($file_handle);
        ///return $line_of_text;
        return ;
    }
    public function create_configurable_product($line_of_text,$supperattribute_code){
        $cProduct = Mage::getModel('catalog/product');
        $sku=$line_of_text[$this->getIndex($this->_inputFile,"Product SKU")];
        $check_sku=$this->check_sku($sku);
        if($check_sku){
            $cProduct=$check_sku;
        }
        $name=$line_of_text[$this->getIndex($this->_inputFile,"Name")];
        $this->showdata('processing '.$name);
        $this->showdata('sku '.$sku);
        $weight='1';
        $sdescription=$line_of_text[$this->getIndex($this->_inputFile,"Description")];
        $description=$line_of_text[$this->getIndex($this->_inputFile,"Description")];
        $price=$line_of_text[$this->getIndex($this->_inputFile,"Price")];
        //set configurable product base data
        $cProduct->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            ->setWebsiteIds(array(0,1)) //Website Ids
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setTaxClassId(2)
            ->setAttributeSetId(4) // Attribute Set Id
            ->setSku($sku)
            ->setName($name)
            ->setWeight('1')
            ->setShortDescription($sdescription)
            ->setDescription($description)
            ->setPrice(sprintf("%0.2f",$price));
        $attribute_id=$this->_supper_attributes_arr[$supperattribute_code]['id'];
        $attribute_code=$this->_supper_attributes_arr[$supperattribute_code]['code'];
        $attribute_label=$this->_supper_attributes_arr[$supperattribute_code]['label'];
        $superAttributeIds = array($attribute_id); // attribute ids of super attributes
        $cProduct->getTypeInstance()->setUsedProductAttributeIds($superAttributeIds); //set super attribute for configurable product
        /** assigning associated product to configurable */
        $data = array(
            '0' => array(
                'id'        => NULL,
                'label'     => $attribute_label,
                'position'  => NULL,
                'values'    => array(
                    /*'0' => array(
                        'value_index'   => 125,
                        'label'         => 'Silver',
                        'is_percent'    => 0,
                        'pricing_value' => '0',
                        'attribute_id'  =>'92',
                    ),
                    '1' => array(
                        'value_index'   => 235,
                        'label'         => 'Gold',
                        'is_percent'    => 0,
                        'pricing_value' => '0',
                        'attribute_id'  =>'92',
                    ),*/
                ),
                'attribute_id'      => $attribute_id,
                'attribute_code'    => $attribute_code,
                'frontend_label'    => $attribute_label,
                'html_id'           => 'config_super_product__attribute_0',
            ),
        );
        if(!$check_sku){
            $this->showdata('creating new '.$name);
            $cProduct->setConfigurableAttributesData($data);
        }
        $cProduct->setCanSaveConfigurableAttributes(true);
        try{
            $cProduct->save();
        }catch( Exception $e) {
            echo $e->getMessage();
        }
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

   /**Process Attributes*/

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

}

$shell = new Mage_Shell_Compiler();
$shell->run();

