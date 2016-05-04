<?php
class Tromvia_Nhapsanpham_Helper_Varnish extends Mage_Core_Helper_Data
{
	/**
	 * purge all varnish cache
	 * @return string $output
	 */
	public function purgeAll()
	{
		$paramObject = new Varien_Object();
		$paramObject->cmd = 'clear';
		$service = 'varnish';
		
		$outputRenderer = Mage::getModel("deployment/itemrenderer_output");
		//init output
		$output = '';
		//get service object
		$serviceObject = Mage::getModel('deployment/service_varnish');
		//get admin server object
		$admin = Mage::getModel('deployment/server_admin');
		$output .= $admin->setOutputRenderer($outputRenderer)->executeService($serviceObject, $paramObject);
		//get web servers object
		$ips = Mage::getStoreConfig('balance_deployment/'.$service.'/ips');
		//@todo check ips
		if(strpos($ips, ';')){
			$ips = explode(';',$ips);
			foreach($ips as $ip){
				if(strlen($ip)){
					$web = Mage::getModel('deployment/server_web', $ip);
					$output .= $web->setOutputRenderer($outputRenderer)->executeService($serviceObject, $paramObject);
				}
			}
		}
		return strip_tags($output);
	}
}
