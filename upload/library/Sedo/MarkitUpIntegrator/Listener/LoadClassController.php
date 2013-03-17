<?php

class Sedo_MarkitUpIntegrator_Listener_LoadClassController
{
	public static function extendAccountController($class, array &$extend)
	{
		if ($class == 'XenForo_ControllerPublic_Account')
		{
			$extend[] = 'Sedo_MarkitUpIntegrator_ControllerPublic_MiuRteReverse';
		}

		if ($class == 'XenForo_ControllerPublic_Editor')
		{
			$extend[] = 'Sedo_MarkitUpIntegrator_ControllerPublic_Editor';
		}		
	}
}