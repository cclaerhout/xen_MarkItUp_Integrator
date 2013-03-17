<?php
class Sedo_MarkitUpIntegrator_DataWriter_MarkItUpConfig extends XenForo_DataWriter
{
	protected function _getFields() {
		return array(
			'sedo_markitup_config' => array(
				'config_id' 	=> array(
						'type' => self::TYPE_UINT,
				                'autoIncrement' => true
				),
				'config_type' 	=> array(
						'type' => self::TYPE_STRING, 
						'default' => ''
				),
				'config_buttons_order' 	=> array(
						'type' => self::TYPE_STRING, 
						'default' => ''
				),
				'config_buttons_full' => array(
						'type' => self::TYPE_STRING, 
						'default' => ''
				)
				
			)

		);
	}
	
	protected function _getExistingData($data)
	{
		if (!$id = $this->_getExistingPrimaryKey($data, 'config_id'))
		{
			return false;
		}
		return array('sedo_markitup_config' => $this->_getMarkItUpConfigModel()->getMarkItUpConfigById($id));
	}
	
	protected function _getUpdateCondition($tableName)
	{
		return 'config_id = ' . $this->_db->quote($this->getExisting('config_id'));
	}

	protected function _getMarkItUpConfigModel()
	{
		return $this->getModelFromCache ( 'Sedo_MarkitUpIntegrator_Model_MarkItUpConfig' );
	}
}
    
?>