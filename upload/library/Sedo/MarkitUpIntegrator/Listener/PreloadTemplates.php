<?php
class Sedo_MarkitUpIntegrator_Listener_PreloadTemplates
{
	public static function template_preload($templateName, array &$params, XenForo_Template_Abstract $template)
	{
		if ($templateName == 'account_preferences')
		{
			$template->preloadTemplate('MarkitUpIntegrator_RteReverse');
		}

		if ($templateName == 'page_container_head')
		{
			$template->preloadTemplate('MarkitUpIntegrator');
		}		
	}
}