<?php
class Sedo_MarkitUpIntegrator_Model_MarkItUp extends XenForo_Model
{
	/**
	* Get only one button by Id
	*/
	public function getMarkItUpButtonsById($id)
	{
		return $this->_getDb()->fetchRow('
				SELECT *
				FROM sedo_markitup
				WHERE miu_button_id = ?
			', $id);
	}

	/**
	* Get only one button by Code Name
	*/    
	public function getMarkItUpButtonsByCode($code)
	{
		return $this->_getDb()->fetchRow('
			SELECT *
			FROM sedo_markitup
			WHERE button_code = ?
		', $code);
	}
 
	/**
	* Get all buttons
	*/
	public function getAlltMarkItUpButtons()
	{
		return $this->fetchAllKeyed('
	        		SELECT * 
	        		FROM sedo_markitup
	        		ORDER BY button_code
	        	', 'miu_button_id');
	}
  
    
	/**
	* Get usergroups and return selected ones
	*/    
	public function getUserGroupOptions($selectedUserGroupIds)
        {
		$userGroups = array();
		foreach ($this->getDbUserGroups() AS $userGroup)
		{
			$userGroups[] = array(
			'label' => $userGroup['title'],
			'value' => $userGroup['user_group_id'],
			'selected' => in_array($userGroup['user_group_id'], $selectedUserGroupIds)
			);
		}
		
		return $userGroups;
        }

	/**
	* Get all usergroups (works with the above function)
	*/ 
	public function getDbUserGroups()
        {
		return $this->_getDb()->fetchAll('
			SELECT user_group_id, title
			FROM xf_user_group
			WHERE user_group_id
			ORDER BY user_group_id
		');
        }
        

	/**
	*  Create/Export a XML file for 1 button
	*/ 
	public function getButtonXml(array $button)
	{
		/*
			http://www.php.net/manual/fr/domdocument.createelement.php
			http://www.php.net/manual/fr/domnode.appendchild.php
		*/
	
		$document = new DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;

		$rootNode = $document->createElement('miu_button');
		$document->appendChild($rootNode);

		$rootNode->appendChild($document->createElement('miu_button_id', $button['miu_button_id']));
		$rootNode->appendChild($document->createElement('button_code', $button['button_code']));
		$rootNode->appendChild($document->createElement('button_cmd', $button['button_cmd']));
		$rootNode->appendChild($document->createElement('button_maincss', $button['button_maincss']));
		$rootNode->appendChild($document->createElement('button_extracss', $button['button_extracss']));
		$rootNode->appendChild($document->createElement('button_has_usr', $button['button_has_usr']));
		$rootNode->appendChild($document->createElement('button_usr', $button['button_usr']));


		return $document;
	}
 
 

	/**
	*  Create/Export a XML file for all buttons
	*/ 
	public function getButtonsXml()
	{
		/*
			http://www.php.net/manual/fr/domdocument.createelement.php
			http://www.php.net/manual/fr/domnode.appendchild.php
		*/
	
		$buttons = $this->getAlltMarkItUpButtons();

		$document = new DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;
		$rootNode = $document->createElement('miu_allbuttons');
		$document->appendChild($rootNode);
		
		foreach($buttons as $button)
		{
			$buttonNode = $document->createElement('miu_button');
			$rootNode->appendChild($buttonNode);

			$buttonNode->appendChild($document->createElement('miu_button_id', $button['miu_button_id']));
			$buttonNode->appendChild($document->createElement('button_code', $button['button_code']));
			$buttonNode->appendChild($document->createElement('button_cmd', $button['button_cmd']));
			$buttonNode->appendChild($document->createElement('button_maincss', $button['button_maincss']));
			$buttonNode->appendChild($document->createElement('button_extracss', $button['button_extracss']));
			$buttonNode->appendChild($document->createElement('button_has_usr', $button['button_has_usr']));
			$buttonNode->appendChild($document->createElement('button_usr', $button['button_usr']));		
		}
		
		return $document;
	}
}