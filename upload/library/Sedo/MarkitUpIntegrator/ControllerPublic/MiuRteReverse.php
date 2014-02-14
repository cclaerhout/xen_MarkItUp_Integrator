<?php
class Sedo_MarkitUpIntegrator_ControllerPublic_MiuRteReverse extends XFCP_Sedo_MarkitUpIntegrator_ControllerPublic_MiuRteReverse
{
	public function actionPreferencesSave()
	{
		$this->_assertPostOnly();

		$enable_rte_reverse = $this->_input->filterSingle('miu_rte_reverse', XenForo_Input::UINT);

		$dw = XenForo_DataWriter::create('XenForo_DataWriter_User');
		$dw->setExistingData(XenForo_Visitor::getUserId());
		$dw->set('miu_rte_reverse', $enable_rte_reverse);
		$dw->preSave();
		
		if ($dwErrors = $dw->getErrors())
		{
			return $this->responseError($dwErrors);
		}

		$dw->save();

		return parent::actionPreferencesSave();
	}
}