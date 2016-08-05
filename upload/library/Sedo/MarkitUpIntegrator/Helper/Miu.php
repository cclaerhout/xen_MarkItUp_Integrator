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

	public static function callbackChecker($class, $method)
	{
		if(!empty($method))
		{
			return (class_exists($class) && method_exists($class, $method));
		}
		
		return class_exists($class);
	}

	public static function scanXmlFile($xmlFile)
	{
		if(self::callbackChecker('XenForo_Helper_DevelopmentXml', 'scanFile'))
		{
			//Protected method
			$file = XenForo_Helper_DevelopmentXml::scanFile($xmlFile);
		}
		else
		{
			//Classic PHP method
			$file = new SimpleXMLElement($xmlFile, null, true);
		}
		
		return $file;
	}

	public static function scanXmlString($xmlString)
	{
		if(self::callbackChecker('Zend_Xml_Security', 'scan'))
		{
			//Protected method
			$xmlObj = Zend_Xml_Security::scan($xmlString);

			if (!$xmlObj)
			{
				throw new XenForo_Exception("Invalid XML in $xmlObj");
			}
		}
		else
		{
			//Classic PHP method
			$xmlObj = simplexml_load_string($xmlString);
		}
		
		return $xmlObj;
	}
}