<?php
class Tromvia_Nhapsanpham_Helper_Server extends Mage_Core_Helper_Data
{
	public function getAWSInternalIp()
	{
		$cmd = "GET http://169.254.169.254/latest/meta-data/local-ipv4";
		$ip = shell_exec($cmd);
		return $ip;
	}
	
	public function getAWSExternalIp()
	{
		$cmd = "GET http://169.254.169.254/latest/meta-data/public-ipv4";
		$ip = shell_exec($cmd);
		return $ip;
	}
	
	public function getInternalIp()
	{
		$ip = gethostbyname(trim(`hostname`));
		return $ip;
	}
}
