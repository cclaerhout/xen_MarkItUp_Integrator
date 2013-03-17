<?php
class Sedo_MarkitUpIntegrator_ViewPublic_Editor_MiuToTiny extends XenForo_ViewPublic_Base
{
	public function renderJson()
	{
		$output = $this->_renderer->getDefaultOutputArray(get_class($this), $this->_params, $this->_templateName);
		$output['htmlContent'] = $this->_params['htmlContent'];
		return XenForo_ViewRenderer_Json::jsonEncodeForOutput($output);
	}
}