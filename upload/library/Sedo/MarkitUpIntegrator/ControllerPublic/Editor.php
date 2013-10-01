<?php

class Sedo_MarkitUpIntegrator_ControllerPublic_Editor extends XFCP_Sedo_MarkitUpIntegrator_ControllerPublic_Editor
{
	public function actionMiuToTiny()
	{
		if (!$this->_noRedirect())
		{
			return;
		}

		$bbCodeContent = $this->_input->filterSingle('bbCodeContent', XenForo_Input::STRING);

		$_editorId = $this->_input->filterSingle('editorId', XenForo_Input::STRING);
		$editorId = str_replace('_miu', '', $_editorId);

		$editorName = $this->_input->filterSingle('editorName', XenForo_Input::STRING);

		$params = array(
			'editorId' => $editorId,
			'editorName' => $editorName,
			'bbCodeContent' => $bbCodeContent
		);

		if(XenForo_Application::get('options')->get('currentVersionId') < 1020031)
		{
			$params['smilies'] = XenForo_ViewPublic_Helper_Editor::getEditorSmilies();
		}

		if(method_exists('BBM_Helper_Buttons', 'getConfig'))
		{
		 	//Ok, that's ugly but I don't see anyother solution to have the accurate previous datas (n-1)
			$controllerName = $this->_input->filterSingle('cName', XenForo_Input::STRING);
			$controllerAction = $this->_input->filterSingle('cAction', XenForo_Input::STRING);
			$viewName = $this->_input->filterSingle('vName', XenForo_Input::STRING);
				
			$params['bbmParams'] = BBM_Helper_Buttons::getConfig($controllerName, $controllerAction, $viewName);
		}

		return $this->responseView('Sedo_MarkitUpIntegrator_ViewPublic_Editor_MiuToTiny', 'editor', $params);

	}

	public function actionMiuDialog()
	{
		$dialog = $this->_input->filterSingle('dialog', XenForo_Input::STRING);
		$viewParams = array();

		if ($dialog == 'Media')
		{
			$viewParams['sites'] = $this->_getBbCodeModel()->getAllBbCodeMediaSites();
		}

		$viewParams['javaScriptSource'] = XenForo_Application::$javaScriptUrl;

		return $this->responseView('XenForo_ViewPublic_Editor_Dialog', 'MarkitUp_Tools_' . $dialog, $viewParams);	
	}
}

//	Zend_Debug::dump($abc);