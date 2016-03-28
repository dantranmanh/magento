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
    public $_inputFile = 'url301.csv';
    public $_outputFile="output.csv";
    public $_productFile="product.csv";
    public $_imgFile="image.csv";
    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {
        //$this->_find_delete_url_rewrite_by_request_path("tablet-cases/apple-1/ipod-touch-4.html");
        //$this->createRule("1abcd", "iphone-6-lifeproof-nuud-case.html");
        //$this->createRule("1abcde", "iphone-6-lifeproof-nuud-case.html");


        $result=$this->ReadExportCSV($this->_inputFile,$this->_productFile);
    }
    public function ReadExportCSV($csvFile,$outputFile){
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        $file=Mage::getBaseDir('media') . DS . 'import'. DS .$outputFile;
        $csv = new Varien_File_Csv();
        $csv->setLineLength(20480);
        $csvdata = array();
        /*write the header of csv file*/
        while (!feof($file_handle) ) {
            //if($i>=35) break;
            $i++;
            $this->showdata($i);
            $line_of_text = fgetcsv($file_handle, 20480);
            if($line_of_text[$this->getIndex($this->_inputFile,"Old Path")] == "Old Path" || $line_of_text[$this->getIndex($this->_inputFile,"Old URL")] =="Old URL") continue;
            //if(empty(line_of_text[$this->getIndex($this->_inputFile,"New URL")])) continue;

            $old_path= $this->cut_slash_at_last_and_first($line_of_text[$this->getIndex($this->_inputFile,"Old Path")]);
            $new_url=$line_of_text[$this->getIndex($this->_inputFile,"New URL")];
            $_newurl=str_replace("http://www.happytel.com/","",$new_url);
            $_newurl=$this->cut_slash_at_last_and_first($_newurl);
            if(empty($_newurl)) continue;//$_newurl=$new_path;
            $_newurl=$_newurl.".html";
            try{
                $this->_find_delete_url_rewrite_by_request_path($old_path);
                $this->createRule($old_path, $_newurl);
            }catch (Exception $e){
                echo $e->getMessage();
            }
        }
        fclose($file_handle);
        ///return $line_of_text;
        return ;
    }

    /**
     * @param $_string
     * @return string
     */
    function _find_delete_url_rewrite_by_request_path($request_path){
        $url_model1 = Mage::getModel('enterprise_urlrewrite/url_rewrite')->getCollection()->addFieldToFilter('request_path',$request_path);
        foreach($url_model1 as $_model){
            $_model->delete();
            $this->showdata("deleted :".$request_path);
        }
    }


    function cut_slash_at_last_and_first($_string){
        $path=explode("/",$_string);
        $new_path=array();
        foreach($path as $pt){
            if(!empty($pt)) $new_path[]=$pt;
        }
        $new_path=implode("/",$new_path);
        return $new_path;

    }

    /**
     * Import rule:
     * @param $fromUrl
     * @param $toUrl
     */
    public function createRule($fromUrl, $toUrl)
    {
        // Create rewrite:
        /** @var Enterprise_UrlRewrite_Model_Redirect $rewrite */
        $rewrite = Mage::getModel('enterprise_urlrewrite/redirect');

        // Check for existing rewrites:

        // Attempt loading it first, to prevent duplicates:
        $rewrite->loadByRequestPath($fromUrl, $storeId);
        $rewrite->setStoreId(0);/*internal*/
        //$rewrite->setStoreId(1);/*local*/
        $rewrite->setOptions('RP');
        $rewrite->setIdentifier($fromUrl);
        $rewrite->setTargetPath($toUrl);
        $rewrite->setEntityType(Mage_Core_Model_Url_Rewrite::TYPE_CUSTOM);

        $rewrite->save();

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
}

$shell = new Mage_Shell_Compiler();
$shell->run();
