<?php
class Sedo_MarkitUpIntegrator_Listener_ControllerPreDispatch
{
	public static function Diktat(XenForo_Controller $controller, $action)
	{
		$options = XenForo_Application::get('options');
		
		if(!empty($options->markitup_integration_isactivated) AND !empty($options->markitup_integration_force_normal_editor))
		{
			$visitor = XenForo_Visitor::getInstance();
			$visitor->enable_rte = 0;
		}
	}
}