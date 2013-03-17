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
			
			return $this->responseView('Sedo_MarkitUpIntegrator_ViewPublic_Editor_MiuToTiny', 'editor_js_setup', array(
				'editorId' => $this->_input->filterSingle('editorId', XenForo_Input::STRING),
				'smilies' => XenForo_ViewPublic_Helper_Editor::getEditorSmilies(),
				'htmlContent' => $htmlContent
			));
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