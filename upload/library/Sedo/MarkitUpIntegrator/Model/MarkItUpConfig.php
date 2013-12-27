<?php
class Sedo_MarkitUpIntegrator_Model_MarkItUpConfig extends XenForo_Model
{
	/**
	* Get configs by Id
	*/
	public function getMarkItUpConfigById($id)
	{
		return $this->_getDb()->fetchRow('
			SELECT *
			FROM sedo_markitup_config
			WHERE config_id = ?
		', $id);
	}

	/**
	* Get configs by type
	*/	
	public function getMarkItUpConfigByType($type)
	{
		return $this->_getDb()->fetchRow('
			SELECT *
			FROM sedo_markitup_config
			WHERE config_type = ?
		', $type);
	}
 
	/**
	* Get all Configs
	*/
	public function getAlltMarkItUpConfig()
	{
		return $this->fetchAllKeyed('
			SELECT * 
			FROM sedo_markitup_config
			ORDER BY config_type
		', 'config_id');
	}


	/**
	* Reset all Configs
	*/
	public function resetAllMarkItUpConfigs()
	{
		$db = XenForo_Application::get('db');
		$db->query("UPDATE sedo_markitup_config SET config_buttons_order = '', config_buttons_full = '' WHERE config_type = 'ltr';");
		$db->query("UPDATE sedo_markitup_config SET config_buttons_order = '', config_buttons_full = '' WHERE config_type = 'rtl';");
		XenForo_Model::create('XenForo_Model_DataRegistry')->delete('sedo_miu_config');			
	}
	
	/**
	* SimpleCache Functions (thanks to Jake Bunce ; http://xenforo.com/community/threads/what-is-the-best-way-to-add-datas-into-the-cache.30814/)
	*/
	
	public function InsertConfigInRegistry()
	{   
		$markitupoptions['Sedo_MarkItUp_Config'] = $this->getAlltMarkItUpConfig();
		
		//Put Config type (rtl or ltr) as key of the array (just to have a cleaner display)
		$i = 1;
		foreach ($markitupoptions['Sedo_MarkItUp_Config'] as $config)
		{
			$key = $config['config_type'];
			$markitupoptions['Sedo_MarkItUp_Config'][$key] = $markitupoptions['Sedo_MarkItUp_Config'][$i];
			unset($markitupoptions['Sedo_MarkItUp_Config'][$i]);
			$i++;
		}
		
		XenForo_Model::create('XenForo_Model_DataRegistry')->set('sedo_miu_config', $markitupoptions);
	}

	public function CleanConfigInRegistry()
	{	  
		XenForo_Model::create('XenForo_Model_DataRegistry')->delete('sedo_miu_config');	
	}
}