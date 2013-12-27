<?php
class Sedo_MarkitUpIntegrator_DataWriter_MarkItUp extends XenForo_DataWriter
{
	protected function _getFields() {
		return array(
			'sedo_markitup' => array(
				'miu_button_id' 	=> array(
						'type' => self::TYPE_UINT,
				                'autoIncrement' => true
				),
				'button_code' 	=> array(
						'type' => self::TYPE_STRING, 
						'required' => true, 
						'maxLength' => 20,
						'requiredError' => 'sedo_markitup_ButtonCodeRequiredDesc',
						'verification' => array('$this', '_verifyMarkItUpButtonCode'),						
				),
				'button_cmd' 	=> array(
						'type' => self::TYPE_STRING, 
						'required' => true, 
						'requiredError' => 'sedo_markitup_ButtonCommandRequiredDesc'				
				),
				'button_maincss' => array(
						'type' => self::TYPE_STRING, 
						'default' => ''
				),
				'button_extracss' => array(
						'type' => self::TYPE_STRING, 
						'default' => ''
				),
				'button_has_usr' 	=> array(
						'type' => self::TYPE_UINT,
						'default' => '0'
				),
				'button_usr' 	=> array(
						'type' => self::TYPE_STRING, 
						'default' => ''						
				),				
			)
		);
	}
	
	protected function _getExistingData($data)
	{
		if (!$id = $this->_getExistingPrimaryKey($data, 'miu_button_id'))
		{
			return false;
		}
		return array('sedo_markitup' => $this->_getMarkItUpModel()->getMarkItUpButtonsById($id));
	}
	
	protected function _getUpdateCondition($tableName)
	{
		return 'miu_button_id = ' . $this->_db->quote($this->getExisting('miu_button_id'));
	}

	protected function _getMarkItUpModel()
	{
		return $this->getModelFromCache ( 'Sedo_MarkitUpIntegrator_Model_MarkItUp' );
	}

	protected function _verifyMarkItUpButtonCode($codename)
	{
		//Check if the button name respects the standards
		if (preg_match('/[^a-zA-Z0-9_]/', $codename))
		{
			$this->error(new XenForo_Phrase('miu_configuration_OnlyAlphaNumericCharacters'), 'miu_button_id');
			return false;
		}

		return true;
	}
	
	
}