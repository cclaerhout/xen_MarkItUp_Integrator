<?php
class Sedo_MarkitUpIntegrator_DataWriter_MiuRteReverse extends XFCP_Sedo_MarkitUpIntegrator_DataWriter_MiuRteReverse
{
	protected function _getFields()
	{
		$parent = parent::_getFields();
		$parent['xf_user_option']['miu_rte_reverse'] = array(
				'type' => self::TYPE_BOOLEAN, 
				'default' => 0
		);

		return $parent;
	}
}