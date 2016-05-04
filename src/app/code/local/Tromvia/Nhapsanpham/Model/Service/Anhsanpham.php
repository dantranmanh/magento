<?php
class Tromvia_Nhapsanpham_Model_Service_Anhsanpham
{
    public $_categories=array();
    public $_inputFile = 'input.csv';
	
	public  function setInputFile($filename){
		$this->_inputFile=$filename;
	}	
	public function getImportDirectory(){
		return Mage::helper('nhapsanpham')->_getUploadFolder().DS;
	}
    public  function showdata($data){
        var_dump($data);
        echo '\n';
    }
    public function run()
    {

        $this->image();
    }
    public function image()
    {
        $result=$this->MapImageCSV($this->_inputFile);
    }
	public function MapImageCSV($csvFile){
        $csv=new Varien_File_Csv();
        $file=$this->getImportDirectory().$csvFile;
        $products=$csv->getData($file);
        $i=0;

        $deleted_product=0;
        foreach($products as $product)
        {
            //if ($i >=5) break;
            if ($product[1] =="ma_sp") continue;
            $i++;     

            $this->saveImageForProduct($product[1],$product[0],$product[1]);
            echo "saved image for product : ".$product[0]." \n";
            echo 'saved : '.$i."image \n";

        }
        echo 'saved : '.$i."image";
        return ;
    }

   
    
    public function saveImageForProduct($productsku,$title,$imagename){
        $_productId = Mage::getModel('catalog/product')->getIdBySku($productsku);
        /* if($_productId != $this->_image_deleted_product){
            $this->remove_images($_productId);
            $this->_image_deleted_product= $_productId;
        } */
        $newProduct = Mage::getModel('catalog/product')->load($_productId);

        $data=array("imagename"=> $imagename,"imageurl"=>$imageurl);
		$imageType=array($imagename.".jpg",$imagename.".jpeg",$imagename.".png");
		$filename="";
		foreach($imageType as $type){
			$filepath   = Mage::helper('nhapsanpham')->getImageImportedFolder().$type;
			//$this->showdata($filepath);
			if (file_exists($filepath)){
				$filename   = $type; //give a new name, you can modify as per your requirement
				break;
			}
		}        
        
        $filepath   = Mage::helper('nhapsanpham')->getImageImportedFolder().$filename; //path for temp storage folder: ./media/import/
        $filepath_to_image=$filepath;
     
        try{
            if(filesize($filepath_to_image) < 500) {
                $this->showdata('the file '.$filepath_to_image.'is crashed');
                return;
            }
            if (file_exists($filepath_to_image)) {
                if(!$this->checkImageExists($newProduct,$imagename)){

                    $newProduct->addImageToMediaGallery($filepath_to_image, array('image', 'small_image', 'thumbnail'), false, false);
                    //$newProduct->save();
                }

                echo 'saving image for product: '.$_productId."\n";
                foreach($newProduct->getData('media_gallery') as $each){
                    foreach($each as $image){

                        $_file=explode("/",$image['file']);
                        $_file_name=end($_file);
                        $i++;
                        $name=explode('.',$imagename);
                        if($this->name_in_file_name($_file_name,$name[0])){
                            $attributes = $newProduct->getTypeInstance(true)
                                ->getSetAttributes($newProduct);
                            $attributes['media_gallery']->getBackend()->updateImage($newProduct, $image['file'], $data=array('postion'=>$i,'label'=>$title));
                        }
                    }
                }
                $newProduct->save();
            }else{
                if(!file_exists($filepath_to_image))
                    $this->showdata('the file '.$filepath_to_image.' has not been downloaded');
            }
        }catch (Exception $e){ echo '3333';
            Zend_Debug::dump($e->getMessage());
        }
    }

	 public function empty_image_configurable_product(){

        $_productCollection1 = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id','configurable');
        foreach ($_productCollection1 as $product1) {
            $product1=Mage::getModel('catalog/product')->load($product1->getEntityId());
            $this->remove_images($product1);
        }
    }
    /**
     * @param $product
     */
    function remove_images($productId =null){
        $product = Mage::getModel('catalog/product')->load($productId);
        Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
        $this->showdata('Deleted images of product Id : '.$product->getEntityId());
        if ($product->getId()){
            $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
            $items = $mediaApi->items($product->getId());
            foreach($items as $item)
                $mediaApi->remove($product->getId(), $item['file']);
        }
        return;
    }
    public function name_in_file_name($media,$name){
        $find = strpos($media, $name);
        if ($find === false) {
            return false;
        }else return true;

    }
    /**
     *
     */
    public function checkImageExists($newProduct,$image_name){       
        foreach($newProduct->getData('media_gallery') as $each){
            foreach($each as $image){
                $file=$image['file'];
                $pos = strpos($file, $image_name);
                if ($pos === false) {
                    continue;
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $name name of product includes values of supper attributes.
     * @return bool|string
     */
    function findAtributte_csvLine_name($name) {
        if(empty($name)) return false;
        Mage::log($name, Zend_Log::DEBUG, 'bi_debug1aaaaaaaaaaa.log');
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
                Mage::log($attribute, Zend_Log::DEBUG, 'bi_debug1aaaaaaaaaaa.log');
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

    function add_enclose($data) {
        $result=array();
        foreach($data as $dt){
            $result[]="\"$dt\"";
        }
        return $result;
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

    /**
     *
     */
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
        return $label;
    }

}