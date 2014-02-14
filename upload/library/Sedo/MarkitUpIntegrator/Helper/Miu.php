<?php
class Sedo_MarkitUpIntegrator_Helper_Miu
{
	public static function getMiuConfig()
	{
		if (XenForo_Application::isRegistered('miu_config'))
		{
			$miu_config = XenForo_Application::get('miu_config');
		}
		else
		{
			$miu_config = XenForo_Model::create('XenForo_Model_DataRegistry')->get('sedo_miu_config');
			XenForo_Application::set('miu_config', $miu_config);
		}
		
		return 	$miu_config;
	}
}