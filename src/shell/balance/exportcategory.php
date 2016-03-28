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
    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {
        /*run on source categories*/
        $this->export_cat();

    }
    public function import_cat(){
        $header=$this->getHeaderCSVLine();
        $file_handle = fopen(dirname(__FILE__).DS.'category.csv', 'r');
        $i=0;

        while (!feof($file_handle) ) {
            //if($i>=5) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            if(empty($line_of_text[0]) ||$line_of_text[0]=='entity_id' || $line_of_text[0]==1 ) continue;
            $cat = Mage::getModel('catalog/category')->load($line_of_text[0]);
            if($cat->getEntityId() == $line_of_text[0] ){
                $categoryData=$this->convertCategoryData($header,$line_of_text);
                $cat->setData($categoryData)->save();

            }else{
                $model=Mage::getModel('catalog/category');
                $categoryData=$this->convertCategoryData($header,$line_of_text);
                $model->setData($categoryData)->setId($categoryData['entity_id'])->save();
            }
            //Mage::log($line_of_text, Zend_Log::DEBUG, 'bi_debug_cat.log');
            //$this->showdata($line_of_text[1]);

        }
        fclose($file_handle);
        ///return $line_of_text;
        return ;
    }
    public function export_cat(){
        $csv = new Varien_File_Csv();
        $csv->setLineLength(20480);
        $csvdata= $this->getMagentoCategory();
        /*write the header of csv file*/
        $file=Mage::getBaseDir('media') . DS . 'export'. DS .'category.csv';
        $csv->saveData($file, $csvdata);
        fclose($file_handle);
    }

    function getHeaderCSVLine(){
        $data=array();
        $data[0]= 'entity_id';
        $data[1]= 'entity_type_id';
        $data[2]= 'attribute_set_id';
        $data[3]= 'parent_id';
        $data[4]= 'created_at';
        $data[5]= 'updated_at';
        $data[6]= 'path';
        $data[7]= 'position';
        $data[8]= 'level';
        $data[9]= 'children_count';
        $data[10]= 'available_sort_by';
        $data[11]= 'description';
        $data[12]= 'meta_keywords';
        $data[13]= 'meta_description';
        $data[14]= 'custom_layout_update';
        $data[15]= 'name';
        $data[16]= 'meta_title';
        $data[17]= 'catlist';
        $data[18]= 'display_mode';
        $data[19]= 'custom_design';
        $data[20]= 'page_layout';
        $data[21]= 'url_key';
        $data[22]= 'is_active';
        $data[23]= 'include_in_menu';
        $data[24]= 'landing_page';
        $data[25]= 'is_anchor';
        $data[26]= 'custom_apply_to_products';
        $data[27]= 'custom_design_from';
        $data[28]= 'custom_design_to';
        $data[29]= 'filter_price_range';
        $data[30]= 'thumbnail';
        $data[31]= 'label';
        $data[32]= 'custom_use_parent_settings';

        return $data;
        /*$fp = fopen($file, 'a') or die('can not open file');
        fputcsv($fp, $this->add_enclose($data),',','^');
        fclose($fp);*/
    }
    function exportCatData($catData){
        $data=array();
        $data[0]= $catData['entity_id'];
        $data[1]= $catData['entity_type_id'];
        $data[2]= $catData['attribute_set_id'];
        $data[3]= $catData['parent_id'];
        $data[4]= $catData['created_at'];
        $data[5]= $catData['updated_at'];
        $data[6]= $catData['path'];
        $data[7]= $catData['position'];
        $data[8]= $catData['level'];
        $data[9]= $catData['children_count'];
        $data[10]= $catData['available_sort_by'];
        $data[11]= $catData['description'];
        $data[12]= $catData['meta_keywords'];
        $data[13]= $catData['meta_description'];
        $data[14]= $catData['custom_layout_update'];
        $data[15]= $catData['name'];
        $data[16]= $catData['meta_title'];
        $data[17]= $catData['catlist'];
        $data[18]= $catData['display_mode'];
        $data[19]= $catData['custom_design'];
        $data[20]= $catData['page_layout'];
        $data[21]= $catData['url_key'];
        $data[22]= $catData['is_active'];
        $data[23]= $catData['include_in_menu'];
        $data[24]= $catData['landing_page'];
        $data[25]= $catData['is_anchor'];
        $data[26]= $catData['custom_apply_to_products'];
        $data[27]= $catData['custom_design_from'];
        $data[28]= $catData['custom_design_to'];
        $data[29]= $catData['filter_price_range'];
        $data[30]= $catData['thumbnail'];
        $data[31]= $catData['label'];
        $data[32]= $catData['custom_use_parent_settings'];

        return $data;
        /*$fp = fopen($file, 'a') or die('can not open file');
        fputcsv($fp, $this->add_enclose($data),',','^');
        fclose($fp);*/
    }
    public function getMagentoCategory(){
        $category = Mage::getModel('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        $magento_cat=array();
        $magento_cat[] = $this->getHeaderCSVLine();
        if ($ids){
            foreach ($ids as $id){
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                //Mage::log($cat->getData(), Zend_Log::DEBUG, 'bi_debug.log');
                $catData=$cat->getData();
                $magento_cat[]=$this->exportCatData($catData);
            }
        }
        return $magento_cat;
    }

    public function convertCategoryData($header,$readData){
        $data=array();
        echo 'processing category: '.$readData[15];
        echo "---"."\n";

        $data=array();
        for ($i = 0; $i < sizeof($header); $i++) {
            $data[$header[$i]]=$readData[$i];
        }
        return $data;
    }

}

$shell = new Mage_Shell_Compiler();
$shell->run();
