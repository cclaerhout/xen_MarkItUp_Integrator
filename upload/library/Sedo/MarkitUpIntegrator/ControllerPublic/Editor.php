<?php

class Sedo_MarkitUpIntegrator_ControllerPublic_Editor extends XFCP_Sedo_MarkitUpIntegrator_ControllerPublic_Editor
{
	public function actionMiuToTiny()
	{
		if ($this->_noRedirect())
		{
			$bbCodeContent = $this->_input->filterSingle('bbCodeContent', XenForo_Input::STRING);
			$bbCodeParser = new XenForo_BbCode_Parser(XenForo_BbCode_Formatter_Base::create('Wysiwyg'));
			$htmlContent = $bbCodeParser->render($bbCodeContent);

			$editorSource = $this->_input->filterSingle('src', XenForo_Input::STRING);

			$params = array(
				'editorId' => $this->_input->filterSingle('editorId', XenForo_Input::STRING),
				'htmlContent' => $htmlContent
			);

			if(XenForo_Application::get('options')->get('currentVersionId') < 1020031)
			{
				$params['smilies'] = XenForo_ViewPublic_Helper_Editor::getEditorSmilies();
			}
			else
			{
				//Ok, that's ugly but I don't see anyother solution to have the accurate previous datas (n-1)
				$controllerName = $this->_input->filterSingle('cName', XenForo_Input::STRING);
				$controllerAction = $this->_input->filterSingle('cAction', XenForo_Input::STRING);
				$viewName = $this->_input->filterSingle('vName', XenForo_Input::STRING);
				
				$extraParams = BBM_Helper_Buttons::getConfig($controllerName, $controllerAction, $viewName);
				$params = $extraParams+$params; //array + operator: first params overrides the second - is said faster than array_merge
			}

			if($editorSource == 'mce4')
			{
				$params['quattroIntegration'] = true;
				return $this->responseView('Sedo_MarkitUpIntegrator_ViewPublic_Editor_MiuToQuattro', 'tiny_quattro_js_setup', $params);			
			}
			else
			{
				return $this->responseView('Sedo_MarkitUpIntegrator_ViewPublic_Editor_MiuToRte', 'editor_js_setup', $params);
			}
		}
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