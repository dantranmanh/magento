<?php
class Tromvia_Nhapsanpham_Helper_Data extends Mage_Core_Helper_Data
{
	const XML_PATH_DEPLOYMENT_GIT_ENABLED = 'balance_deployment/git/enabled';
	const XML_PATH_DEPLOYMENT_VARNISH_ENABLED = 'balance_deployment/varnish/enabled';
	const XML_PATH_DEPLOYMENT_APC_ENABLED = 'balance_deployment/apc/enabled';
	const XML_PATH_DEPLOYMENT_MEMCACHED_ENABLED = 'balance_deployment/memcached/enabled';
	const XML_PATH_DEPLOYMENT_SERVER_USER = 'balance_deployment/about/user';
	
	public function _getUploadedFiles(){
		$_list=array();		
		$files = scandir($this->_getUploadFolder());
		foreach($files as $file){
			if($file != "." && $file !=".."){
				$_list[] = $file;
			}
		}
		return $_list;
	}
	public function _getUploadFolder(){
		return Mage::getBaseDir('media') . DS . 'import' . DS."uploaded";
	}
	public function _getGeneratedFolder(){
		return Mage::getBaseDir('var') . DS . 'import';
	}
	public function getImageImportedFolder(){
		return Mage::getBaseDir('media') . DS . 'import'. DS .'image'.DS;
	}

    public function removeSpeciaCharacter($str){
		if(empty($str)) return false;
		$str=str_replace(",","",$str);
		$str=str_replace(".","",$str);
		$str=str_replace(":","",$str);
		$str=str_replace("*","",$str);
		$str=str_replace("?","",$str);
		$str=str_replace("<","",$str);
		$str=str_replace(">","",$str);
		$str=str_replace("\\","",$str);
		$str=str_replace("/","",$str);
		$str=str_replace("\"","",$str);
		$str=str_replace("'","",$str);
		$str=str_replace("|","",$str);
		return $str;
	}
    public function generateNameInVietnam($name){
		if(empty($name)) return false;
		$name=$this->removeSpeciaCharacter($name);		
		$utf=mb_substr( $name, 0, null,'UTF8');
		return $utf;
	}
    public function generateSkuByNameFirstChar($name){	
		if(empty($name)) return false;
		$name=$this->removeSpeciaCharacter($name);
				
        $sku=null;		
		$utf=$this->convertVietToEnglish(mb_substr( $name, 0, null,'UTF8'));
		 
        $nameArray = explode(' ', $name);
        if ( ! $nameArray ) {
            return false;
        }
        $result = '';
        foreach ( $nameArray as $word ) 		
		$sku .= $this->convertVietToEnglish(mb_substr( $word, 0, 1,'UTF8'));	
        if(!$sku) return false;
		$sku=$sku."-".substr($utf, -1);
        return strtoupper($sku);
    }
	public function convertVietToEnglish($str){
		 $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		  $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		  $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		  $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		  $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		  $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		  $str = preg_replace("/(đ)/", 'd', $str);
		  $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
		  $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
		  $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
		  $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
		  $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
		  $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
		  $str = preg_replace("/(Đ)/", 'D', $str );		  
		  return $str;
	}
}
