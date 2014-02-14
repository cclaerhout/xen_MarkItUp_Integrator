<?php

class Sedo_MarkitUpIntegrator_ViewAdmin_Export extends XenForo_ViewAdmin_Base
{
	public function renderXml()
	{
		XenForo_Application::autoload('Zend_Debug');
		$this->setDownloadFileName('button_' . $this->_params['button']['button_code'] . '.xml');
		
		return $this->_params['xml']->saveXml();
	}
}