<?php
require_once 'csv.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Date extends Mage_Shell_Csv
{
    public function run()
    {
        $this->ReadExportCSV($this->_inputFile);

    }


    public function ReadExportCSV($csvFile){ //echo $csvFile;die;
        $file_handle = fopen($csvFile, 'r');
        $i=0;

        $child_product=1;
        $_current_configure_product=1;

        while (!feof($file_handle) ) {
           //if($i>=15) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            if(empty($line_of_text[3])) $line_of_text[3]='PRD-'.$line_of_text[1];
            //$this->showdata($line_of_text[1]);
            if(empty($line_of_text[1]) && empty($line_of_text[3])) continue;
            if($line_of_text[0] == 'Product ID') continue;

            if($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Product Type")]) =="P"){
                if($child_product > 0){
                    if($line_of_text[$this->getIndex($this->_inputFile,"Product SKU")] != $_current_configure_product[$this->getIndex($this->_inputFile,"Product SKU")]){
                        $this->_updateDate($_current_configure_product);
                        $_current_configure_product=$line_of_text;
                        $child_product=0;
                    }

                }else{
                    $this->_updateDate($line_of_text);
                    $child_product=0;
                }
            }elseif($this->cut_space($line_of_text[$this->getIndex($this->_inputFile,"Item Type")]) =="SKU"){
                $this->_updateDate($line_of_text,$_current_configure_product);
                    $child_product++;

            }
        }
        $this->_updateDate($_current_configure_product);
        fclose($file_handle);
        ///return $line_of_text;
        return ;
    }
    function _updateDate($array_line=array(),$parent=array()){
        $_created_date=$array_line[$this->getIndex($this->_inputFile,"Date Added")];
        $_modified_date=$array_line[$this->getIndex($this->_inputFile,"Date Modified")];
        if(!empty($parent)){
            $_created_date=$parent[$this->getIndex($this->_inputFile,"Date Added")];
            $_modified_date=$parent[$this->getIndex($this->_inputFile,"Date Modified")];
        }
        $_sku=$array_line[$this->getIndex($this->_inputFile,"Product SKU")];
        $_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_sku);

        if($_product) {

            if(!empty($_created_date))$_product->setCreatedAt($this->_converdate($_created_date));
            if(!empty($_modified_date))$_product->setUpdatedAt($this->_converdate($_modified_date));
            $_product->save();

            $this->showdata($_product->getEntityId());
            $this->showdata($_created_date);
            $this->showdata($_modified_date);
        }

    }
    /**
     * @param string $date  MM/DD/YYYY
     * @return string
     */
    function _converdate($date=""){
        $date=explode("/",$date);
        $_new="20".$date[2]."-".$date[0]."-".$date[1];
        return strtotime ($_new);
    }
}

$shell = new Mage_Shell_Date();
$shell->run();
