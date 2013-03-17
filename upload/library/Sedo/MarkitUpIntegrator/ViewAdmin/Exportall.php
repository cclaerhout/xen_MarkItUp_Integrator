<?php

class Sedo_MarkitUpIntegrator_ViewAdmin_Exportall extends XenForo_ViewAdmin_Base
{
	public function renderXml()
	{
		XenForo_Application::autoload('Zend_Debug');
		$date = new DateTime();
	
		$this->setDownloadFileName('miu_allbuttons_' . $date->getTimestamp() . '.xml');
		
		return $this->_params['xml']->saveXml();
	}
}