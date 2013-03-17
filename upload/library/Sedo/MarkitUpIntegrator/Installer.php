<?php
class Sedo_MarkitUpIntegrator_Installer
{
	public static function install($addon)
	{
		$db = XenForo_Application::get('db');
		
		if(empty($addon)) //Check addon ID
		{
			$db->query("CREATE TABLE IF NOT EXISTS sedo_markitup (             
			        		miu_button_id INT NOT NULL AUTO_INCREMENT,
						button_code VARCHAR(30) NOT NULL,
						button_cmd text NOT NULL,
						button_maincss text DEFAULT NULL,
						button_extracss text DEFAULT NULL,
						button_has_usr int(1) NOT NULL default '0',
						button_usr text DEFAULT NULL,						
						PRIMARY KEY (miu_button_id)
					)
		                	ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;"
			);

			$db->query("CREATE TABLE IF NOT EXISTS sedo_markitup_config (             
			        		config_id INT(200) NOT NULL AUTO_INCREMENT,
						config_type mediumtext NOT NULL,
						config_buttons_order mediumtext NOT NULL,
						config_buttons_full mediumtext NOT NULL,
						PRIMARY KEY (config_id)
					)
		                	ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;"
			);

			$db->query("INSERT INTO sedo_markitup_config (config_id, config_type, config_buttons_order, config_buttons_full) VALUES (1, 'ltr', '', ''), (2, 'rtl', '', '');");						
		}
		
		
		if($addon['version_id'] < 2)
		{
			 $db->query("ALTER TABLE xf_user_option ADD miu_rte_reverse TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0'");
		}

		if($addon['version_id'] < 5)
		{
			XenForo_Application::setSimpleCacheData('sedo_miu_config', false);
			$db->query("DELETE FROM sedo_markitup");
			$db->query("UPDATE sedo_markitup_config SET config_buttons_order = '', config_buttons_full = '' WHERE config_type = 'ltr';");
			$db->query("UPDATE sedo_markitup_config SET config_buttons_order = '', config_buttons_full = '' WHERE config_type = 'rtl';");
		}		
	}
 
	public static function uninstall()
	{
		XenForo_Application::get('db')->query("DROP TABLE sedo_markitup");
		XenForo_Application::get('db')->query("DROP TABLE sedo_markitup_config");
		XenForo_Application::get('db')->query("ALTER TABLE xf_user_option DROP miu_rte_reverse");	

		XenForo_Model::create('XenForo_Model_DataRegistry')->delete('sedo_miu_config');		
	}
}
?>

