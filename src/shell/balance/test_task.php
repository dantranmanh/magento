<?php
/**
 * Magento
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../abstract.php';
class Balance_UpdateAvailableStock extends Mage_Shell_Abstract
{
    public $_inputFile = 'locator.csv';
    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function __construct() {
        parent::__construct();

        // Time limit to infinity
        /*ini_set('memory_limit', '1024M');
        set_time_limit(0);*/

    }
    public function run()
    {
        echo 'asdfasdfafd';
        $locator=Mage::getModel('ak_locator/location')->getCollection();
        foreach($locator as $lc){
            echo 'asdfasdfafd';
            Mage::log(Mage::getModel('ak_locator/location')->load($lc->getEntityId())->getData(), Zend_Log::DEBUG, 'bi_debug.log');
        }

       /* $order = Mage::getModel("sales/order")->load(5311);
        Mage::helper('michel_sales/data')->getAuthenticateClientFromApi();
        Mage::helper('michel_sales/data')->uploadOrderApi($order);*/
    }
    public function ReadExportCSV($csvFile){
        $file_handle = fopen(dirname(__FILE__).DS.$csvFile, 'r');
        $i=0;
        $data=array();
        while (!feof($file_handle) ) {
            //if($i >=3) break;
            $i++;
            $line_of_text = fgetcsv($file_handle, 20480);
            /*Mage::log('afterdfgfsg', Zend_Log::DEBUG, 'bi_debug.log');
            Mage::log($line_of_text, Zend_Log::DEBUG, 'bi_debug.log');*/
            //$this->showdata($line_of_text[1]);
            if($line_of_text[0] == "Store Name" && $line_of_text[1]=="Trading Address1") continue;
            if(empty($line_of_text[0]))  continue;
            $this->createNewBIlocator($line_of_text);
        }
        fclose($file_handle);
        ///return $line_of_text;
        return ;
    }

    function createNewBIlocator($line_of_text){
        $this->showdata('importting locator '.$line_of_text[0]."<br>");
        $url=str_replace("^",",",$line_of_text[0]);
        $url=str_replace(" ","-",strtolower($url))."-".str_replace("^",",",$line_of_text[5]);
        $address=str_replace("^",",",$line_of_text[1]).", ".str_replace("^",",",$line_of_text[2]);
        if(empty($address)) {
            echo 'this locator does not have address:  '.$line_of_text[0];
            Mage::log('empty address', Zend_Log::DEBUG, 'bi_debug_locator.log');
            Mage::log($line_of_text, Zend_Log::DEBUG, 'bi_debug_locator.log');
            return null;
        }
        $collection = Mage::getModel('ak_locator/location')->getCollection()
            ->addAttributeToSelect('address')
            ->addAttributeToSelect('country')
            ->addAttributeToFilter('address', array('eq' => $address))
        ;
        if(count($collection) > 1) {
            echo 'There are many BI locator with the same address '.$address;
            Mage::log('same address', Zend_Log::DEBUG, 'bi_debug_locator.log');
            Mage::log($onibi_locator->getData(), Zend_Log::DEBUG, 'bi_debug_locator.log');
            return null;
        }
        $model=null;
        if(count($collection) == 0) {
            $model = Mage::getModel('ak_locator/location');
        }
        if(count($collection) == 1) {
            $model =$collection->getFirstItem();
        }

        $model
            ->setData('location_key',$url)
            ->setData('is_enable',1)
            ->setData('title',str_replace("^",",",$line_of_text[0]))
            ->setData('address',str_replace("^",",",$line_of_text[1]).", ".str_replace("^",",",$line_of_text[2]))
            ->setData('postal_code',str_replace("^",",",$line_of_text[5]))
            ->setData('administrative_area',str_replace("^",",",$line_of_text[4]))
            ->setData('country','Australia')
            ->setData('phone',str_replace("^",",",$line_of_text[6]))
            ->setData('stockist_phone',str_replace("^",",",$line_of_text[6]))
            ->setData('oo_name',str_replace("^",",",$line_of_text[12]))
            ->setData('fax',str_replace("^",",",$line_of_text[7]))
            ->setData('url_key',$url)
            ->setData('trade_sub',str_replace("^",",",$line_of_text[3]))

            ->setData('nab_id',str_replace("^",",",$line_of_text[10]))
            ->setData('nab_pass',str_replace("^",",",$line_of_text[11]))
            ->setData('geocoded',1)

            ->setData('latitude',$line_of_text[8])
            ->setData('longitude',$line_of_text[9])

            ->setData('hours_mon',str_replace("^",",",$line_of_text[13])." - ".str_replace("^",",",$line_of_text[14]))
            ->setData('hours_tue',str_replace("^",",",$line_of_text[15])." - ".str_replace("^",",",$line_of_text[16]))
            ->setData('hours_wed',str_replace("^",",",$line_of_text[17])." - ".str_replace("^",",",$line_of_text[18]))
            ->setData('hours_thu',str_replace("^",",",$line_of_text[19])." - ".str_replace("^",",",$line_of_text[20]))
            ->setData('hours_fri',str_replace("^",",",$line_of_text[21])." - ".str_replace("^",",",$line_of_text[22]))
            ->setData('hours_sat',str_replace("^",",",$line_of_text[23])." - ".str_replace("^",",",$line_of_text[24]))
            ->setData('hours_sun',str_replace("^",",",$line_of_text[25])." - ".str_replace("^",",",$line_of_text[26]))
            ->save()
        ;
    }

}

$updateAvailableStock = new Balance_UpdateAvailableStock();

$updateAvailableStock->run();

?>