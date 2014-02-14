<?php

class Sedo_MarkitUpIntegrator_Listener_LoadClassDatawriter
{
	public static function extendUserDataWriter($class, array &$extend)
	{
		if ($class == 'XenForo_DataWriter_User')
		{
			$extend[] = 'Sedo_MarkitUpIntegrator_DataWriter_MiuRteReverse';
		}
	}
}