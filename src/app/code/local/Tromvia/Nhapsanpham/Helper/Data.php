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
}
