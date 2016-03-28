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
    public $_Mcategories_name=array();
    public $_Mcategories_id=array();
    public $_Bcategories=array();
    public $_inputFile = 'input.csv';
    public $_outputFile="output.csv";
    public $_productFile="product.csv";
    public $_imgFile="image.csv";
    public $_root_cat_path="1/2/";

    public $_categry_csv_index=0;
    public $_categry_csv_name="Category Details";
    public $_categories=array();



    public $_lv2=array();
    public $_lv3=array();
    public $_lv4=array();
    public $_lv5=array();
    public $_lv6=array();
    public $_lv7=array();
    public $_lv8=array();


    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {
        $this->process_category();

        //$this->check_cat_exist(' PHONE CASES');
       // return;

        $this->_categry_csv_index =$this->getIndex($this->_inputFile,$this->_categry_csv_name);
        if($this->_categry_csv_index == '-1') $this->showdata('can not find out the index of Categories details in csv file');
        else $this->showdata('Index of Category Details column is: '.$this->_categry_csv_index);
       /* $this->_Mcategories_name=$this->getMagentoCatName();
        $this->_Mcategories_id=$this->getMagentoCatId();*/
        $big_cat=$this->ReadExportCSV($this->_inputFile);
        Mage::log($big_cat, Zend_Log::DEBUG, 'bi_debug_cat.log');
        $this->_Bcategories=$big_cat;
        /*create lv2*/
        $this->showdata('creating category level  2 ');
        $this->_lv2 =$this->getCatByLevel(2);

        foreach($big_cat as $b_c_index=> $b_c){
            $path=explode("|",$b_c['originpath']);
            $path=$this->_revert_cat_name($path);
            if (in_array($path[0],$this->_lv2)) continue;
            if(!empty($path[0])){
                $this->_lv2[]=$path[0];
                $this->_create_new_cat($path[0],2,'');
            }
        }

        /*create lv3*/$this->showdata('creating category level  3 ');
        $this->_lv3 =$this->getCatByLevel(3);

        foreach($big_cat as $b_c_index=> $b_c){
            $path=explode("|",$b_c['originpath']);
            $parent_id=$this->getIdByCatName($path[0],2);
            $path=$this->_revert_cat_name($path);
            if(!empty($path[1])){
                if (in_array($path[1].$parent_id,$this->_lv3)) continue;
                $this->_lv3[]=$path[1].$parent_id;

                if(!empty($parent_id))
                    $this->_create_new_cat($path[1],$parent_id,'');
            }

        }

        /*create lv4*/
        $this->showdata('creating category level  4 ');
        $this->_lv4 =$this->getCatByLevel(4);
        foreach($big_cat as $b_c_index=> $b_c){
            $path=explode("|",$b_c['originpath']);
            $_cat_lv_2_id=$this->getIdByCatName($path[0],2);
            $_cat_lv_3_id=$this->getIdByCatName($path[1],3,$_cat_lv_2_id);
            $path=$this->_revert_cat_name($path);
            if(!empty($path[2])){
                if (in_array($b_c['originpath'],$this->_lv4)) continue;
                $this->_lv4[]=$b_c['originpath'];
                if(!empty($_cat_lv_3_id))
                    $this->_create_new_cat($path[2],$_cat_lv_3_id,'');
            }
        }
        /*create lv5*/
        $this->showdata('creating category level  5 ');
        $this->_lv5 =$this->getCatByLevel(5);
        foreach($big_cat as $b_c_index=> $b_c){
            $path=explode("|",$b_c['originpath']);
            $_cat_lv_2_id=$this->getIdByCatName($path[0],2);
            $_cat_lv_3_id=$this->getIdByCatName($path[1],3,$_cat_lv_2_id);
            $_cat_lv_4_id=$this->getIdByCatName($path[2],4,$_cat_lv_3_id);

            $path=$this->_revert_cat_name($path);
            if(!empty($path[3])){
                if (in_array($path[3].$parent_id,$this->_lv5)) continue;
                $this->_lv5[]=$path[3].$parent_id;
                if(!empty($_cat_lv_4_id))
                    $this->_create_new_cat($path[3],$_cat_lv_4_id,'');
            }
        }
    }

    public function getCatByLevel($level=null){
        if(empty($level)) return array();
        $category = Mage::getModel('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        $magento_cat=array();
        if ($ids){
            foreach ($ids as $id){
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                $name = $cat->getName();
                if($cat->getLevel() == $level){
                    $item=$name;
                    if($level = 3 ) $item=$item.$cat->getParentId();

                    if($level = 4 ) {
                        $_cat_lv3=Mage::getModel('catalog/category')->load($cat->getParentId());
                        $_cat_lv2=Mage::getModel('catalog/category')->load($_cat_lv3->getParentId());
                        $item=$_cat_lv2->getName()."|".$_cat_lv3->getName()."|".$item;
                    }
                    $magento_cat[]=$item;

                }
            }
        }
        return $magento_cat;
    }

    /**
     * @param null $name
     * @param null $level
     * @param null $parent_id
     * @return null
     */
    public function getIdByCatName($name=null,$level=null,$parent_id=null){
        if(empty($name)) return null;
        $category = Mage::getModel('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        if ($ids){
            foreach ($ids as $id){
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                if($cat->getName() == $name) {
                    if($level && $cat->getLevel()==$level){
                        if($parent_id){
                            if($parent_id ==$cat->getParentId())
                                return $id;
                        }
                        else return $id;
                    }
                }
            }
        }
        return null;
    }

    /**
     * replace ^ by / in cat name
     * @param array $path
     * @return array
     */

    public function _revert_cat_name($path = array()){
        $_new=array();
        foreach($path as $_p){
            $_new[]=str_replace("^","/",$_p);
        }
        return $_new;
    }

    /**
     * @param string $name
     * @param null $parentId
     * @param string $url
     */
    public function _create_new_cat($name="",$parentId=null,$url=""){
        try{
            $name=str_replace("^","/",$name);
            if($this->check_cat_exist($name,$parentId)) {
                $this->showdata('this category '.$name.' has been created!');
                return;
            }
            $category = Mage::getModel('catalog/category');
            $category->setName($name);
            $category->setUrlKey($url);
            $category->setIsActive(1);
            $category->setDisplayMode('PRODUCTS_AND_PAGE'); 
            $category->setIsAnchor(1); //for active achor
            //$category->setStoreId(Mage::app()->getStore()->getId());
            $parentCategory = Mage::getModel('catalog/category')->load($parentId);
            $category->setPath($parentCategory->getPath());
            $category->save();
        } catch(Exception $e) {
            var_dump($e);
        }

    }

    /**
     * @param string $name_path
     * @return null|string
     */
    public function convert_cat_path_b_2m($name_path=''){
        if(empty($name_path)) return 'not found';
        $name_path=explode("/",$name_path);
        $id_path=array();
        foreach($name_path as $cat){
            $id_path[]=$this->getCatIdByName($cat);
        }
        $new_path= implode('/',$id_path);
        if(!empty($new_path)) return $this->_root_cat_path.$new_path;
        return null;
    }

    public function getCatIdByName($cat){
        if(empty($cat)) return '';
        $category=$this->_Mcategories_name[$cat];
        if(!empty($category)){
            return $category['id'];
        }else{
            return null;
        }

    }
    public function getMagentoCatId(){
        $category = Mage::getModel('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        $magento_cat=array();
        if ($ids){
            foreach ($ids as $id){
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                $entity_id = $cat->getId();
                $name = $cat->getName();
                $name=$this->proccess_string($name);
                //$magento_cat[]=array($cat->getData());
                $path=$cat->getPath();
                $magento_cat[$entity_id]=array('name'=>$name ,'path'=>$path);
            }
        }
        return $magento_cat;
    }

    public function getMagentoCatName(){
        $category = Mage::getModel('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        $magento_cat=array();
        if ($ids){
            foreach ($ids as $id){
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                $name = $cat->getName();
                $name=$this->proccess_string($name);
                //$magento_cat[]=array($cat->getData());
                $path=$cat->getPath();
                $magento_cat[$name]=array('id'=>$cat->getId() ,'path'=>$path,'originname'=>$cat->getName());
            }
        }
        return $magento_cat;
    }
    public function getIndex($csvFile,$columnname){
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        $header=array();
        while (!feof($file_handle) ) {
            if($i>=1) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
        }
        fclose($file_handle);
        $header=$line_of_text;
        foreach($header as $index=> $column){
            if($column == $columnname ) return $index;
        }
        return -1;
    }
    public function ReadExportCSV($csvFile){
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        $csv = new Varien_File_Csv();
        $csv->setLineLength(20480);
        $big_cats = array();
        $big_cat_path=array();
        $max_level=0;
        while (!feof($file_handle) ) {
            //if($i>=5) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            if(empty($line_of_text[3])) $line_of_text[3]='PRD-'.$line_of_text[1];
            //$this->showdata($line_of_text[1]);
            if(empty($line_of_text[1]) && empty($line_of_text[3])) continue;

            $read_cat=$line_of_text[$this->_categry_csv_index];

            $cat_arr=explode('|',$read_cat);
            if($read_cat =='Category Details') continue;/*ignore the header line*/
            foreach($cat_arr as $cat){
                $original_name=explode(",",$cat);
                $cat=$this->proccess_categories_string($cat);
                $level=explode('|',$cat);
                if(count($level) > $max_level) $max_level = count($level);
                if (in_array($cat, $big_cat_path)) continue;
                $big_cat_path[]=$cat;
                $big_cats[$cat]=array('id'=>$cat,'originpath'=>$cat,'path'=>$cat,'originname'=>str_replace("Category Name: ","",$original_name[1]),'name'=>str_replace("Category Name: ","",$original_name[1]));
            }
        }
        fclose($file_handle);
        $this->showdata('max category level : '.$max_level);
        return $big_cats;
    }
    function proccess_string($string){
        $string=str_replace("\/", "",$string);
        $string=str_replace(" ", "",$string);
        $string=str_replace("&", "",$string);
        $string=strtolower($string);
        return $string;
    }
    function proccess_categories_string($string){ /* Category ID: 220, Category Name: iPhone, Category Path: PHONE CASES/iPhone */
        $path=explode(",",$string);
        $string=$path[2];
        $string=str_replace("Category Path: ", "",$string);
        $string=str_replace("/", "|",$string);
        return $string;
    }

    public function process_category(){
        echo 'REMEMBER CATEGORY LIST'."\n";
        $mgt_cat=$this->getMagentoCategory();
        $this->_categories =$mgt_cat;
        Mage::log($this->_categories, Zend_Log::DEBUG, 'data_category.log');
    }
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

    /**
     * @param $name
     * @param $parentid
     *
    [entity_id] => 141
    [entity_type_id] => 3
    [attribute_set_id] => 3
    [parent_id] => 2
    [created_at] => 2015-12-30 03:26:09
    [updated_at] => 2016-01-04 05:08:06
    [path] => 1/2/141
    [position] => 1
    [level] => 2
    [children_count] => 42
    [name] =>  PHONE CASES
     */
    public function check_cat_exist($name,$parentid){
        if(empty($parentid)) return false;
        $category = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('name', $name);
        foreach($category as $cat){
           if($parentid == $cat->getData('parent_id')) return true;
        }
        return false;
    }

}

$shell = new Mage_Shell_Compiler();
$shell->run();
