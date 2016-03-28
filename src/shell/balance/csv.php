<?php
require_once '../abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Csv extends Mage_Shell_Abstract
{
    public $_inputFile = 'input.csv';
    public $_outputFile="output.csv";
    public $_productFile="product.csv";
    public $_imgFile="image.csv";

    public $_input_csv_header=array();

    function _construct()
    {
        parent::_construct();
        $this->_inputFile=dirname(__FILE__). DS .$this->_inputFile;
        //$this->_inputFile=Mage::getBaseDir('media'). DS . 'import'.DS.$this->_inputFile;
        $this->_outputFile=Mage::getBaseDir('media'). DS . 'import'.DS.$this->_outputFile;
        $this->_productFile=Mage::getBaseDir('media'). DS . 'import'.DS.$this->_productFile;
        $this->_imgFile=Mage::getBaseDir('media'). DS . 'import'.DS.$this->_imgFile;

        $this->showdata($this->_inputFile);
        $this->getCsvHeader($this->_inputFile);
        Mage::log($this->_input_csv_header, Zend_Log::DEBUG, 'bi_debug_csv.log');
        $this->showdata('Csv object');
    }

    public function run()
    {


    }
    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }

    /**
     * @param string $csvFile
     * @return array
     */
    public function getCsvHeader($csvFile=""){
        $line_of_text=array();
        if(!empty($this->_input_csv_header)) $line_of_text=$this->_input_csv_header;
        else{
            $file_handle = fopen($csvFile, 'r');
            $i=0;
            while (!feof($file_handle) ) {
                if($i>=1) break;/** Get the first line of the csv file */
                $i++;
                $line_of_text = fgetcsv($file_handle, 20480);
            }
            fclose($file_handle);
            $this->_input_csv_header=$line_of_text;
        }
        return $line_of_text;
    }
    public function getIndex($csvFile,$columnname){
        $line_of_text=array();
        if(empty($this->_input_csv_header)){
            $this->getCsvHeader($csvFile);
        }
        $header=$this->_input_csv_header;
        foreach($header as $index=> $column){
            if($column == $columnname ) return $index;
        }
        return -1;
    }
    public function getAllCsvColumns($csvFile){
        $file_handle = fopen($csvFile, 'r');
        $i=0;
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
/*
$shell = new Mage_Shell_Csv();
$shell->run();*/
