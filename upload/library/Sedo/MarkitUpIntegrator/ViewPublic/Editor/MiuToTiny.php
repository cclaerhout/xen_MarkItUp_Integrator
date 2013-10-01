<?php
class Sedo_MarkitUpIntegrator_ViewPublic_Editor_MiuToTiny extends XenForo_ViewPublic_Base
{
	public function renderJson()
	{
		$formCtrlName = $this->_params['editorName'];
		$bbCodeContent = $this->_params['bbCodeContent'];

		$editorOptions = array(
			'miu' => array(
				'loadRte' => true
			)
		);
		
		$editorTemplateObj = XenForo_ViewPublic_Helper_Editor::getEditorTemplate($this, $formCtrlName, $bbCodeContent, $editorOptions);
		$templateName =  $editorTemplateObj->getTemplateName();
		$templateParams = $editorTemplateObj->getParams();

		$output = $this->_renderer->getDefaultOutputArray(get_class($this), $templateParams, $templateName);

		if(!empty($output['js']))
		{
			//Let's start a dirty trick to be sure the custom bbcode will be loaded before the XenForo Redactor Framework
			foreach($output['js'] as $key => $jsFile)
			{
				if(strpos($jsFile, 'js/bbm/redactor/') === false)
				{
					continue;
				}
				unset($output['js'][$key]);
				array_unshift($output['js'], $jsFile);
			}
		}
		
		$output['formCtrlNameHtml'] = (!empty($templateParams['formCtrlNameHtml'])) ? $templateParams['formCtrlNameHtml'] : $editorId.'_html';

		return XenForo_ViewRenderer_Json::jsonEncodeForOutput($output);
	}
}