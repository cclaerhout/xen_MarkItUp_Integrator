<?php

class Sedo_MarkitUpIntegrator_ControllerAdmin_MarkItUp extends XenForo_ControllerAdmin_Abstract
{
	/***
	*	
	*	INDEX ACTION FUNCTION
	*
	**/

	public function actionIndex()
	{
		$viewParams = array();

		return $this->responseView('XenForo_ViewAdmin_MarkItUp_Homepage', 'markitup_homepage', $viewParams);
	}

	//Admin Perms
	protected function _preDispatch($action)
	{
		$this->assertAdminPermission('manageMarkItUp');
	}

	/***
	*	
	*	BUTTONS CONFIG ACTION FUNCTIONS
	*
	**/


	public function actionButtonsList()
	{
		$viewParams = array(
			'codes' => $this->_getMarkItUpModel()->getAlltMarkItUpButtons(),
			'permissions' => XenForo_Visitor::getInstance()->hasAdminPermission('manageMarkItUp')
 		);
		return $this->responseView('XenForo_ViewAdmin_MarkItUp_Buttons_List', 'markitup_buttons_list', $viewParams);
	}

	public function actionAdd()
	{
		return $this->_getMarkItUpAddEditResponse(array());
	}

	public function actionEdit()
	{
		$miu_button_id = $this->_input->filterSingle('miu_button_id', XenForo_Input::UINT);
		$code = $this->_checkMarkItUpButton($miu_button_id);
				
		return $this->_getMarkItUpAddEditResponse($code);
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		$miu_button_id = $this->_input->filterSingle('miu_button_id', XenForo_Input::UINT);

		$dwInput = $this->_input->filter(array(
				'miu_button_id' => XenForo_Input::UINT,
				'button_code' => XenForo_Input::STRING,
				'button_cmd' => XenForo_Input::STRING,
				'button_maincss' => XenForo_Input::STRING,
				'button_extracss' => XenForo_Input::STRING,
				'button_has_usr' => XenForo_Input::UINT,
		));

		//Array_keys is the only trick I've found to get the usergroups id selected... Associated template code => name="button_usr[{$list.value}]"
		$dwInput['button_usr'] = serialize(array_keys($this->_input->filterSingle('button_usr', array(XenForo_Input::STRING, 'array' => true))));
		
		$dw = XenForo_DataWriter::create('Sedo_MarkitUpIntegrator_DataWriter_MarkItUp');
		if ($this->_getMarkItUpModel()->getMarkItUpButtonsById($miu_button_id))
		{
			$dw->setExistingData($miu_button_id);
			$this->_UpdateConfigsAfterButtonEditOrDelete($miu_button_id, $dwInput);
		}
		else
		{
			//It's a new button (new id) but check if the name is already taken (Well, although buttons main key is id, let's do it anyway to have a cleaner view)
			$button_code = $this->_input->filterSingle('button_code', XenForo_Input::STRING);
			
			if ($this->_getMarkItUpModel()->getMarkItUpButtonsByCode($button_code))
			{
				return $this->responseError(new XenForo_Phrase('miu_configuration_ButtonCodeAlreadyUsed'));
			}
		}
		$dw->set('miu_button_id', $miu_button_id);
		$dw->bulkSet($dwInput);
		$dw->save();

		/***
			Return Manager
		***/
		$src = $this->_input->filterSingle('miu_lang_dir', XenForo_Input::STRING);
		
		if ($this->_input->filterSingle('reload', XenForo_Input::STRING))
		{
			//Save button
			$redirection = 'markitup-editor/edit';

			if(!empty($src))
			{
				$redirection = 'markitup-editor/editorconfig' . $src;
			}
			
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED,
				XenForo_Link::buildAdminLink($redirection)
			);
			
			/* For debug
			return $this->responseMessage($button_usr);
			*/
		}
		else 
		{	
			//Save & Exit button (will not appear on overlay - set inside template)
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('markitup-editor/buttonslist')
			);
		}
	}

	public function actionDelete()
	{
		if ($this->isConfirmedPost()) //When user click on confirm popup
		{
			//Get button id
			$miu_button_id = $this->_input->filterSingle('miu_button_id', XenForo_Input::STRING);
			$this->_UpdateConfigsAfterButtonEditOrDelete($miu_button_id);
			
			return $this->_deleteData(
				'Sedo_MarkitUpIntegrator_DataWriter_MarkItUp', 'miu_button_id',
				XenForo_Link::buildAdminLink('markitup-editor/buttonslist')
			);

		}
		else //When the user click on delete at first
		{
			
			$miu_button_id = $this->_input->filterSingle('miu_button_id', XenForo_Input::STRING);
			$code = $this->_checkMarkItUpButton($miu_button_id);

			$viewParams = array(
				'code' => $code
			);
			return $this->responseView('XenForo_ViewAdmin_MarkItUp_Button_Delete', 'markitup_button_delete', $viewParams);
		}
	}

	/**
	 * EXPORT FCT
	 *
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionExport()
	{
		$miu_button_id = $this->_input->filterSingle('miu_button_id', XenForo_Input::STRING);
		$button = $this->_checkMarkItUpButton($miu_button_id);
	
		$this->_routeMatch->setResponseType('xml');

		$viewParams = array(
			'button' => $button,
			'xml' => $this->_getMarkItUpModel()->getButtonXml($button)
		);

		return $this->responseView('Sedo_MarkitUpIntegrator_ViewAdmin_Export', '', $viewParams);
	}

	public function actionExportall()
	{
		$this->_routeMatch->setResponseType('xml');

		$viewParams = array(
			'xml' => $this->_getMarkItUpModel()->getButtonsXml()
		);

		return $this->responseView('Sedo_MarkitUpIntegrator_ViewAdmin_Exportall', '', $viewParams);
	}


	public function actionImportallUploader()
	{
		//The Sedo_MarkitUpIntegrator_ViewAdmin_Import file doesn't exist. It's "creating" here.
		return $this->responseView('Sedo_MarkitUpIntegrator_ViewAdmin_Import', 'markitup_button_importall');
	}

	public function actionImportall()
	{

		$this->_assertPostOnly();
		
		$fileTransfer = new Zend_File_Transfer_Adapter_Http();
		if ($fileTransfer->isUploaded('upload_file'))
		{
			$fileInfo = $fileTransfer->getFileInfo('upload_file');
			$fileName = $fileInfo['upload_file']['tmp_name'];
		}
		else
		{
			$fileName = $this->_input->filterSingle('server_file', XenForo_Input::STRING);
		}
		
		if (!file_exists($fileName) || !is_readable($fileName))
		{
			throw new XenForo_Exception(new XenForo_Phrase('please_enter_valid_file_name_requested_file_not_read'), true);
		}
		
		$file = new SimpleXMLElement($fileName, null, true);
		
		if($file->getName() == 'miu_button')
		{
			throw new XenForo_Exception(new XenForo_Phrase('miu_error_issinglebuttonxml'), true);
		}
		elseif($file->getName() != 'miu_allbuttons')
		{
		
			throw new XenForo_Exception(new XenForo_Phrase('miu_error_notmultibuttonxml'), true);		
		}

		$buttons = $file->children();
		$forceupdate = $this->_input->filterSingle('forceupdate', XenForo_Input::STRING);
		$updated = array();
		$notupdated = array();
		
		foreach($buttons as $button)
		{
			$dw = XenForo_DataWriter::create('Sedo_MarkitUpIntegrator_DataWriter_MarkItUp');
			$miu_button_id = (string)$button->miu_button_id;
	
			$dwInput = array(
				'button_code' => (string)$button->button_code,
				'button_cmd' => (string)$button->button_cmd,
				'button_maincss' => (string)$button->button_maincss,
				'button_extracss' => (string)$button->button_extracss,
				'button_has_usr' => (string)$button->button_has_usr,
				'button_usr' => (string)$button->button_usr
			);
	
			$IDinDB = $this->_getMarkItUpModel()->getMarkItUpButtonsById($miu_button_id);
			$CODEinDB = $this->_getMarkItUpModel()->getMarkItUpButtonsByCode((string)$button->button_code);
	
			// ID EXISTS AND THE BUTTON WITH THAT ID HAS THE SAME CODE THAN THE ONE BEING IMPORTED => UPDATE || TEST OK
			if ( is_array($IDinDB) AND ($IDinDB['button_code'] == (string)$button->button_code) )
			{
				if($forceupdate)
				{
					$dw->setExistingData($miu_button_id);
					$this->_UpdateConfigsAfterButtonEditOrDelete($miu_button_id, $dwInput);
					$dw->bulkSet($dwInput);

					$updated[] = (string)$button->button_code;

				}
				else
				{
					$notupdated[] = (string)$button->button_code;
					continue;				
				}
			}
			
			//ID EXISTS AND BUTTON EXIST BUT BOTH NOT CORRESPONDING => CREATE A NEW BUTTON OR REPLACE THE EXISTING BUTTON || TEST OK
			elseif ( is_array($IDinDB) AND is_array($CODEinDB) AND ($IDinDB['button_code'] != (string)$button->button_code))
			{
				if($forceupdate)
				{
					$miu_button_id = $CODEinDB['miu_button_id'];
					
					$dw->setExistingData($miu_button_id);
					$this->_UpdateConfigsAfterButtonEditOrDelete($miu_button_id, $dwInput);
	
					$dw->bulkSet($dwInput);
					$updated[] = (string)$button->button_code;					
				}
				else
				{
					$notupdated[] = (string)$button->button_code;
					continue;
				}
			}
			//ID DOESN'T EXIST BUT BUTTON CODE HAS BEEN FOUND => CREATE A NEW BUTTON OR REPLACE THE EXISTING BUTTON || TEST OK
			elseif ( (!$IDinDB OR !is_array($IDinDB)) AND is_array($CODEinDB))
			{
	
				if($forceupdate)
				{
					$miu_button_id = $CODEinDB['miu_button_id'];
	
					$dw->setExistingData($miu_button_id);
					$this->_UpdateConfigsAfterButtonEditOrDelete($miu_button_id, $dwInput);
	
					$dw->bulkSet($dwInput);
					$updated[] = (string)$button->button_code;							
				}
				else
				{
					$notupdated[] = (string)$button->button_code;
					continue;
				}
			}
			//[ID IS NEW, BUTTON CODE IS NEW] OR [ID exists but button code doesn't exist] => CREATE A NEW ONE || TEST OK
			else
			{
				$dw->bulkSet($dwInput);
				$updated[] = (string)$button->button_code;
			}
			
			$dw->save();
		}

		$viewParams = array(
				'updated' => $updated,
				'notupdated' => $notupdated
		);

		return $this->responseView('XenForo_ViewAdmin_MarkItUp_Button_Edit', 'markitup_button_importall_notupdated', $viewParams);
	}
	
	public function actionImportPopup()
	{
		//The Sedo_MarkitUpIntegrator_ViewAdmin_Import file doesn't exist. It's "creating" here.
		return $this->responseView('Sedo_MarkitUpIntegrator_ViewAdmin_Import', 'markitup_button_import');
	}

	public function actionImport()
	{

		$this->_assertPostOnly();
		
		$fileTransfer = new Zend_File_Transfer_Adapter_Http();
		if ($fileTransfer->isUploaded('upload_file'))
		{
			$fileInfo = $fileTransfer->getFileInfo('upload_file');
			$fileName = $fileInfo['upload_file']['tmp_name'];
		}
		else
		{
			$fileName = $this->_input->filterSingle('server_file', XenForo_Input::STRING);
		}
		
		if (!file_exists($fileName) || !is_readable($fileName))
		{
			throw new XenForo_Exception(new XenForo_Phrase('please_enter_valid_file_name_requested_file_not_read'), true);
		}
		
		$file = new SimpleXMLElement($fileName, null, true);
		
		if($file->getName() == 'miu_allbuttons')
		{
			throw new XenForo_Exception(new XenForo_Phrase('miu_error_ismultibuttonxml'), true);		
		}
		elseif($file->getName() != 'miu_button')
		{
			throw new XenForo_Exception(new XenForo_Phrase('miu_error_notsinglebuttonxml'), true);
		}
		
		$dw = XenForo_DataWriter::create('Sedo_MarkitUpIntegrator_DataWriter_MarkItUp');
		

		$miu_button_id = $file->miu_button_id;

		$dwInput = array(
			'button_code' => $file->button_code,
			'button_cmd' => $file->button_cmd,
			'button_maincss' => $file->button_maincss,
			'button_extracss' => $file->button_extracss,
			'button_has_usr' => $file->button_has_usr,
			'button_usr' => $file->button_usr
		);

		$forceupdate = $this->_input->filterSingle('forceupdate', XenForo_Input::STRING);


		$IDinDB = $this->_getMarkItUpModel()->getMarkItUpButtonsById($miu_button_id);
		$CODEinDB = $this->_getMarkItUpModel()->getMarkItUpButtonsByCode($file->button_code);


		// ID EXISTS AND THE BUTTON WITH THAT ID HAS THE SAME CODE THAN THE ONE BEING IMPORTED => UPDATE || TEST OK
		if ( is_array($IDinDB) AND ($IDinDB['button_code'] == $file->button_code) )
		{
			$dw->setExistingData($miu_button_id);
			$this->_UpdateConfigsAfterButtonEditOrDelete($miu_button_id, $dwInput);

			$dw->bulkSet($dwInput);
		}
		
		//ID EXISTS AND BUTTON EXIST BUT BOTH NOT CORRESPONDING => CREATE A NEW BUTTON OR REPLACE THE EXISTING BUTTON || TEST OK
		elseif ( is_array($IDinDB) AND is_array($CODEinDB) AND ($IDinDB['button_code'] != $file->button_code))
		{
			if($forceupdate)
			{
				$miu_button_id = $CODEinDB['miu_button_id'];
				
				$dw->setExistingData($miu_button_id);
				$this->_UpdateConfigsAfterButtonEditOrDelete($miu_button_id, $dwInput);

				$dw->bulkSet($dwInput);			
			}
			else
			{
				throw new XenForo_Exception(new XenForo_Phrase('miu_configuration_Import_ButtonCodeAlreadyUsed_ByPassSolution'), true);
			}
		}
		//ID DOESN'T EXIST BUT BUTTON CODE HAS BEEN FOUND => CREATE A NEW BUTTON OR REPLACE THE EXISTING BUTTON || TEST OK
		elseif ( (!$IDinDB OR !is_array($IDinDB)) AND is_array($CODEinDB))
		{

			if($forceupdate)
			{
				$miu_button_id = $CODEinDB['miu_button_id'];

				$dw->setExistingData($miu_button_id);
				$this->_UpdateConfigsAfterButtonEditOrDelete($miu_button_id, $dwInput);

				$dw->bulkSet($dwInput);			
			}
			else
			{
				throw new XenForo_Exception(new XenForo_Phrase('miu_configuration_Import_ButtonCodeAlreadyUsed_ByPassSolution'), true);
			}
		}
		//[ID IS NEW, BUTTON CODE IS NEW] OR [ID exists but button code doesn't exist] => CREATE A NEW ONE || TEST OK
		else
		{
			$dw->bulkSet($dwInput);
		}

		$dw->save();
		
		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildAdminLink('markitup-editor/buttonslist')
		);

	}	
	//SETUP FUNCTIONS
	protected function _getMarkItUpAddEditResponse(array $code)
	{
		//Check if the edit is made from the button manager
		if (isset($_GET['langdir']))
		{
			$code['langdir'] = $_GET['langdir'];
		}

		if(isset($code['button_usr']) AND !empty($code['button_usr']))
		{
			//Usergroups Management: unserialize datas from db
			$code['button_usr'] = unserialize($code['button_usr']);
		}
		else
		{
			//If the config was blank, create an array
			$code['button_usr'] = array();
		}

		//Get the model to fetch datas and create a new variable for the "foreach in template"
		$code['usr_list'] = $this->_getMarkItUpModel()->getUserGroupOptions($code['button_usr'] );


		$viewParams = array(
			'code' => $code
		);
		return $this->responseView('XenForo_ViewAdmin_MarkItUp_Button_Edit', 'markitup_button_edit', $viewParams);
	}	

	protected function _checkMarkItUpButton($miu_button_id)
	{
		$check = $this->_getMarkItUpModel()->getMarkItUpButtonsById($miu_button_id);

		if (!$check)
		{
			throw $this->responseException($this->responseError(new XenForo_Phrase('miu_configuration_ButtonDoesNotExist'), 404));
		}

		return $check;
	}

	protected function _getMarkItUpModel()
	{
		return $this->getModelFromCache('Sedo_MarkitUpIntegrator_Model_MarkItUp');
	}


	/*****
	*	THE BRIDGE BETWEEN BUTTONS & CONFIG DATAS
	*	
	*	This function is a bridge from the buttons modifications (save or edit) to the config datas
	*	@$miu_button_id is the button id
	*	@$new_values is only use when edit a previous button to give him a new value
	***/
	protected function _UpdateConfigsAfterButtonEditOrDelete($miu_button_id, array $new_values = null)
	{
		//Get all configs (rtl/ltr) to check if that button was used
		$config_all =  $this->_getMarkItUpConfigModel()->getAlltMarkItUpConfig();
			
		foreach ($config_all as $config_id => $config)
		{
			//Only continue if the config wasn't empty (for ie: user delete a default button before to have set a config)

			if(isset($config['config_buttons_full']) AND !empty($config['config_buttons_full']))
			{

				//Get back buttons full array
				$config_buttons_full = unserialize($config['config_buttons_full']);
			
				//Get the sub-array key (button key inside buttons config)
				foreach ($config_buttons_full as $key => $selectedbutton)
				{
					//if ($selectedbutton['miu_button_id'] == $miu_button_id) => should work too - keep for reference
					if (array_search($miu_button_id, $selectedbutton))
					{
						$target = $key;
					}
				}
			
				//If the button has been found let's unset if the instruction is coming from the delete function OR update if it's only an update of an existing button
				if(isset($target))
				{
					if(isset($new_values))
					{
						//Merge Button id to new values !!!
						$new_values['miu_button_id'] = $miu_button_id;
						
						//UPDATE VALUES !!!
						$config_buttons_full[$target] = $new_values;
					}
					else
					{
						//UNSET !!! 
						unset($config_buttons_full[$target]);
					}
				
					//Let's serialize back the config
					$config_buttons_full = serialize($config_buttons_full);
				
					//Before to write in the Database, let's also take back the button from the config_buttons_order table (string)
					//If update, no need to change, the id remains the same
					$config_buttons_order = $config['config_buttons_order'];
					$config_buttons_order_array = explode(',', $config_buttons_order);
					$target_key = array_search($miu_button_id, $config_buttons_order_array);

					if(!isset($new_values))
					{
						//UNSET !!! 
						unset($config_buttons_order_array[$target_key]);
					}

					$config_buttons_order = implode(',', $config_buttons_order_array);


					//Let's write new config in the database
					$dw = XenForo_DataWriter::create('Sedo_MarkitUpIntegrator_DataWriter_MarkItUpConfig');
					if ($this->_getMarkItUpConfigModel()->getMarkItUpConfigById($config_id))
					{
						$dw->setExistingData($config_id);
					}

					$dw->set('config_buttons_order', $config_buttons_order);
					$dw->set('config_buttons_full', $config_buttons_full);
					$dw->save();
	
					//Let's update the Simple Cache
					$this->_getMarkItUpConfigModel()->InsertConfigInRegistry();	
				}
			}
		}	
	}

	
	/***
	*	
	*	EDITOR CONFIG ACTION FUNCTIONS
	*
	**/

	public function actionEditorConfigltr()
	{
		return $this->EditorConfig('ltr');
	}

	public function actionEditorConfigrtl()
	{
		return $this->EditorConfig('rtl');	
	}


	public function EditorConfig($languedirection)
	{
		//Get config and all buttons
		$config =  $this->_getMarkItUpConfigModel()->getMarkItUpConfigByType($languedirection);
		$buttons = $this->_getMarkItUpModel()->getAlltMarkItUpButtons();

		//Look inside config which buttons are already inside the editor and take them back from the buttons list
		if(!empty($config['config_buttons_full']))
		{
			$config['config_buttons_full'] = unserialize($config['config_buttons_full']);

			foreach($config['config_buttons_full'] as $key => $selectedbutton)
			{
				if((!in_array($selectedbutton['miu_button_id'], array('separator', 'carriage'))) AND !isset($buttons[$selectedbutton['miu_button_id']]))
				{
					//If a button has been deleted from database, hide it from the the selected button list (It shoudn't happen due to actionDelete function)
					unset($config['config_buttons_full'][$key]);
				}
				else
				{
					//Hide all buttons who are already used	from the available buttons list
					unset($buttons[$selectedbutton['miu_button_id']]);
				}
			}
		
			//Create a new array with the line ID as main key 
			$lines = array();
			$line_id = 1;
			foreach($config['config_buttons_full'] as $button)
			{
				if($button['miu_button_id'] == 'carriage')
				{
					$line_id++;
				}
				else
				{
					$lines[$line_id][] = $button;
				}
			}
		}

		if(empty($lines))
		{
			//If the config is blank let's put a separator button to avoid problems with conditional templates and js incrementation and sortable function 
			//May be not the best solution, but the easiest
			$lines[1][0] = array('miu_button_id' => 'separator', 'button_code' => '|'); 
		}

		$viewParams = array(
			'codes' => $buttons,
			'languedirection' => $languedirection,
			'config' => $config,
			'lines' => $lines,		
			'permissions' => XenForo_Visitor::getInstance()->hasAdminPermission('manageMarkItUp')
 		);
		return $this->responseView('XenForo_ViewAdmin_MarkItUp_editor_Config', 'markitup_editor_config', $viewParams);
	}


	public function actionResetConfigs()
	{
		//To do before public implementation: confirm
		
		$this->_getMarkItUpConfigModel()->resetAllMarkItUpConfigs();
			
		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('markitup_editor_config')
		);
	}

	public function actionPostConfig()
	{
		$this->_assertPostOnly();

		// fetch and clean the message text from input
		$config_id = $this->_input->filterSingle('config_id', XenForo_Input::STRING);
		$config_type = $this->_input->filterSingle('config_type', XenForo_Input::STRING);		
		$config_buttons_order = $this->_input->filterSingle('config_buttons_order', XenForo_Input::STRING);
		$config_buttons_order = str_replace('button_', '', $config_buttons_order); // 'buttons_' prefix was only use for pretty css		


		//Build final data: config_buttons_full
		$config_buttons_full = array();
		
		if (!empty($config_buttons_order))		
		{
			//Get buttons
			$buttons = $this->_getMarkItUpModel()->getAlltMarkItUpButtons();
			//Get selected buttons from user configuration and place them in an array
			$selected_buttons =  explode(',', $config_buttons_order);
			//Create the final data array
			$config_buttons_full = array();
		
			foreach ($selected_buttons as $selected_button)
			{
				if(!empty($selected_button))
				{
					//to prevent last 'else' can't find any index, id must be: id array = id db = id js (id being separator)
					if($selected_button == 'separator')
					{
						$config_buttons_full[] = array('miu_button_id' => 'separator', 'button_code' => '|');
					}
					elseif($selected_button == '#')
					{
						$config_buttons_full[] = array('miu_button_id' => 'carriage', 'button_code' => '#');
					}
					else
					{
						if(isset($buttons[$selected_button])) //Check if the button hasn't been deleted
						{
							$config_buttons_full[] = $buttons[$selected_button];
						}
					}
				}
			}
		}


		//Choose what to display in the ajax response
		$ajaxresponse =  str_replace('separator', '|', $config_buttons_order); // <= Just  for a nicer display

		//Save in Database		
		$config_buttons_full = serialize($config_buttons_full);

		$dw = XenForo_DataWriter::create('Sedo_MarkitUpIntegrator_DataWriter_MarkItUpConfig');
		if ($this->_getMarkItUpConfigModel()->getMarkItUpConfigById($config_id))
		{
			$dw->setExistingData($config_id);
		}

		$dw->set('config_buttons_order', $config_buttons_order);
		$dw->set('config_buttons_full', $config_buttons_full);

		$dw->save();
		
		//Save in Simple Cache
		$this->_getMarkItUpConfigModel()->InsertConfigInRegistry();

		$options = XenForo_Application::get('options');
		
		if(isset($options->markitup_integration_debug_ajaxresponse) AND !empty($options->markitup_integration_debug_ajaxresponse))
		{
			// Ajax response ("only run this code if the action has been loaded via XenForo.ajax()")
			if ($this->_noRedirect())
			{

				$viewParams = array(
					'ajaxresponse' => $ajaxresponse,
				);

				return $this->responseView(
					'Sedo_MarkitUpIntegrator_ViewAdmin_MarkItUp',
					'markitup_editor_response',
					$viewParams
				);
			}
		}
		
		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('markitup_editor_config')
		);
	}

	//SETUP FUNCTIONS
	protected function _getMarkItUpConfigModel()
	{
		return $this->getModelFromCache('Sedo_MarkitUpIntegrator_Model_MarkItUpConfig');
	}	
	
}
//Zend_Debug::dump($contents);