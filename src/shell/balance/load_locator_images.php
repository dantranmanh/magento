
<?php
/**
 * Magento
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../abstract.php';
class Balance_UpdateAvailableStock extends Mage_Shell_Abstract
{
    public $_inputFile = 'locator.txt';
    public $_outputFile="locator_images.csv";

    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function __construct() {
        parent::__construct();

        // Time limit to infinity
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

    }
    public function run()
    {
        //$this->_read_txt_file();
        $collection = Mage::getModel('ak_locator/location')->getCollection();
        foreach($collection as $cl){
            $_locator=Mage::getModel('ak_locator/location')->load($cl->getEntityId());
            $_branch=$_locator->getData('branch_no');
            if(!empty($_branch)){
                $this->setLocatorImage($_locator);
                Mage::log($_locator->getData('branch_no'), Zend_Log::DEBUG, 'bi_debug13333333.log');
            }
            else $this->showdata('can not find out branch no at this locator : '.$_locator->getEntityId()."  ".$_locator->getTitle());
        }
    }

    /**
     *
     */
    public function setLocatorImage($_locator){
        $_branch=$_locator->getData('branch_no');
        $_image_url="http://happytel.com/template/images/stores/".$_branch.".jpg";
        $_image_name=$_branch.".jpg";
        $this->DownloadImage($_image_name,$_image_url);
        $_locator->setImage($_image_name)->save();
    }
    public function _read_txt_file(){
        $file_handle = fopen(dirname(__FILE__).DS.$this->_inputFile, 'r');
        $i=0;
        while ($line = fgets($file_handle)) {
            $this->showdata($i);
            if($i >= 1000000) break;
            if(empty($line)) continue;
            if($this->isHeadline($line)) continue;
            $this->getImageFromFile($line);
            $i++;

        }
    }

    /**
     * param $line ['101','Westfield Belconnen','HAPPYTEL',-35.238412,149.068206,'K309 (near Sunglass Hut) <br>Westfield Belconnen,<br>Benjamin way,<br>Belconnen ACT 2617','<a href="tel:+61262510859"><font color="blue">02 6251 0859</font></a>','[Mon] 9:00am-5:30pm<br>[Tue] 9:00am-5:30pm<br>[Wed] 9:00am-5:30pm<br>[Thu] 9:00am-5:30pm<br>[Fri] 9:00am-9:00pm<br>[Sat] 9:00am-5:00pm<br>[Sun] 10:00am-4:00pm', '<iframe class="enlarge" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3258.755821794702!2d149.06615299999999!3d-35.23744800000001!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6b17ad706732019b%3A0xa52be93abd681663!2sWestfield+Belconnen!5e0!3m2!1sen!2sau!4v1410408075543" width="90%" height="200" frameborder="0" ></iframe>']
     */
    function getImageFromFile($line){
        $_line=explode(",",$line);
        $_id=str_replace("[","",$_line[0]);
    }
    function isHeadline($line){
        $_line=explode(",",$line);
        if($_line[0] == "Branch No" || $_line[1] == "Shopping Centre Name" || $_line[2] =="Company Name") return true;
        return false;
    }
    /**
     * @param $imagename
     * @param $imageurl
     */
    public function DownloadImage($imagename,$imageurl){
        $_productId = Mage::getModel('catalog/product')->getIdBySku($productsku);
        $data=array("imagename"=> $imagename,"imageurl"=>$imageurl);
        $image_url = $data['imageurl'];
        //$image_url  =str_replace("https://", "http://", $image_url); // replace https tp http
        $image_type = substr(strrchr($image_url,"."),1); //find the image extension
        $filename   = $data['imagename']; //give a new name, you can modify as per your requirement
        $filepath   = Mage::getBaseDir('media') . DS . 'locator' . DS . 'location' . DS.$filename; //path for temp storage folder: ./media/import/
        if (file_exists($filepath)) {
            Mage::log($imagename."is duplicated", Zend_Log::DEBUG, 'bi_debug_image_error.log');
            return;
        }
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$image_url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Cirkel');$query = curl_exec($curl_handle);curl_close($curl_handle);
        file_put_contents($filepath, $query); //store the image from external url to the temp storage folder file_get_contents(trim($image_url))

    }
}

$updateAvailableStock = new Balance_UpdateAvailableStock();

$updateAvailableStock->run();

?>