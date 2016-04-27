<?php
class Sedo_MarkitUpIntegrator_Listener_Templates
{
	public static $smilies;
	public static $ie8fix = false;

	public static function template_hook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
	{
		switch ($hookName) 
		{
			case 'editor':
				$options = XenForo_Application::get('options');

	      			if(empty($options->markitup_integration_isactivated))
	      			{
	      				break;
	      			}

				$visitor = XenForo_Visitor::getInstance();
		       	        $visitorUserGroupIds = array_merge(array((string)$visitor['user_group_id']), (explode(',', $visitor['secondary_group_ids'])));

				$style_session = $template->getParam('visitorStyle');
				$style_id = $style_session['style_id'];
      			        $lang_id = (!empty($visitor['language_id'])) ? $visitor['language_id'] : $options->defaultLanguageId;		       	        

				// INIT VARIABLES
				$params = array(
					'xenSet' => '', 
					'miuCss' => '',
					'stylesPath' => 'styles'
				);
				
				// INIT PATH... thank you IE
				if($visitor->getBrowser)
				{
					//Addon: http://xenforo.com/community/resources/browser-detection-mobile-msie.1098/
					if($visitor->getBrowser['isIE'])
					{
						$params['stylesPath'] = $options->boardUrl . '/styles';
						self::$ie8fix = $options->boardUrl;
					}
				}
				else
				{
					//Helper with extra regex
					if(Sedo_MarkitUpIntegrator_Helper_Tools::isBadIE('all'))
					{
						$params['stylesPath'] = $options->boardUrl . '/styles';					
						self::$ie8fix = $options->boardUrl;
					}
				}
				
				// GET BUTTONS
				$IsRtl = $template->getParam('pageIsRtl');
				$textDirection = ($IsRtl === true) ? 'rtl' : 'ltr';
				
				$globalMiuConfigs = Sedo_MarkitUpIntegrator_Helper_Miu::getMiuConfig();
				
				if(!empty($options->markitup_integration_debug_DisplayCacheData) && $visitor['user_group_id'] == 3)
				{
					Zend_Debug::dump($globalMiuConfigs);
				}

				$buttons_fulldata = unserialize($globalMiuConfigs["Sedo_MarkItUp_Config"][$textDirection]['config_buttons_full']);
				$buttons_simpledata = $globalMiuConfigs["Sedo_MarkItUp_Config"][$textDirection]['config_buttons_order'];

				if(empty($buttons_fulldata) AND !is_array($buttons_fulldata))
				{
					break;
				}
				
      				//MULTILINE CSS BUILDER
      				$params['lines_number'] = substr_count($buttons_simpledata, '#') + 1; //Use for the dynamic background
      				$dropDownWidth =  filter_var(XenForo_Template_Helper_Core::styleProperty('miu_public_buttons_drop.width'), FILTER_SANITIZE_NUMBER_INT);
      				$defaultWith = filter_var(XenForo_Template_Helper_Core::styleProperty('miu_public_buttons.width'), FILTER_SANITIZE_NUMBER_INT) + $dropDownWidth . 'px';
      
      				/**
      				 *	BUTTONS CSS & SET BUILDER
      				 *	Starts a loop by button ; the variable $i is only use for the css creation, 
      				 *	it will help to create the "MarkItUp button id" 
      				 *	MarkItUp places buttons in the editor according to their ID so ID1 is button 1,
      				 *	ID2 is button 2, etc. BUT separators and carriage must not be include
      				 **/
      				$i = 1;
      
      				foreach ($buttons_fulldata as $button)
      				{
      					if(empty($button))
      					{
      						continue;
      					}

      					//Usergroups Management
      					if (!empty($button['button_has_usr']))
      					{
      						$granted_usergroups = unserialize($button['button_usr']);

      						if(!empty($granted_usergroups) && !array_intersect($visitorUserGroupIds, $granted_usergroups))
      						{
							continue;
      						}
      					}

      					/* CSS CREATOR*/
					if( !in_array($button['miu_button_id'], array('separator', 'carriage')) )
      					{ 
      						$MiuID = $i;
     						$ieFIX = 9999 - $i; //Should not be used anymore

      						if(!empty($button['button_maincss']))
      						{
	      						$params['miuCss'] .= str_replace(
      								array('{$id}', '@stylesPath'),
      								array($MiuID, $params['stylesPath']), 
      								$button['button_maincss']
      							);
      						}
      						
      						if(!empty($button['button_extracss']))
      						{
							$params['miuCss'] .= str_replace(
								array('{$id}', '{$ieFIX}', '@default', '@stylesPath'), 
								array($MiuID, $ieFIX, $defaultWith, $params['stylesPath']), 
								$button['button_extracss']
							);
						}
      							
           					if(strtolower($button['button_code']) == 'smilies')
      						{
      							self::bakeSmilies($button, $i);
	      						$params['miuCss'] .= self::$smilies['autoCss'];
      						}

      						$i++;
      					}

      					/*BUTTONS SET CREATOR*/
      					if($button['miu_button_id'] == 'separator')
      					{
      						$params['xenSet'] .= "{separator:'---------------' },";
      					}
      					elseif($button['miu_button_id'] == 'carriage')
      					{
      						$params['xenSet'] .= "{breaker:'####' },";
      					}
      					elseif(strtolower($button['button_code']) == 'smilies')
      					{
      						$params['xenSet'] .= self::$smilies['cmd'];
      					}
      					else
      					{
      						if(!empty($button['button_cmd']))
      						{
	      						$params['xenSet'] .= str_replace(
      								array('{$javaScriptSource}'), 
      								array(XenForo_Application::$javaScriptUrl),
      								$button['button_cmd']
      							);
      						}
      					}

      					/*CHECK IF BUTTON SET HAS A ENDING COMA*/
      					if(!preg_match('#,(?:\s+)?$#', $params['xenSet'])) 
      					{
      						$params['xenSet'] .= ','; //Add a last coma to prevent a js error on IE
      					}
      				}

				//Delete the last coma to prevent an additionnal empty & useless button on IE
				$params['xenSet'] = substr($params['xenSet'], 0, -1);

				
      				//EMBEDDED CSS BUILDER
      					//Basic Phrase tags builder {phrase:......} ; phrases must been global to avoid db request
      					if(preg_match_all('#{phrase:(.+?)}#i', $params['miuCss'], $captures, PREG_SET_ORDER))
      					{
      						foreach ($captures as $capture)
      						{
      							$phrasebuilder = new XenForo_Phrase($capture[1]);
      							$params['miuCss'] = str_replace($capture[0], $phrasebuilder, $params['miuCss']);
      						}
      						unset($captures, $capture);
      					}
      
      					/*css minify*/
      					if(empty($options->markitup_integration_debug_disable_minify_css))
      					{
      						$params['miuCss'] = self::minifyCSS($params['miuCss']);
      					}

      				//BUTTON SET BUILDER
      					/*js minify*/
      					if(empty($options->markitup_integration_debug_disable_minify_js))
      					{
      						require_once('jsmin.php');
      						$params['xenSet'] = JSMin::minify($params['xenSet']);
      					}
      
      					/***
      					*	Activate XenCode - Thanks to Infis for his solution! 
      					*	Ref: http://xenforo.com/community/threads/how-activate-xen-tags-not-include-in-template-when-injecting-code-inside-templates.30999/#post-353948
      					***/
           					$params['xenSet'] = preg_replace_callback(
      							'#(?:{)?\$xenOptions\.([0-9a-z_]+)(?:\.)?([0-9a-z_]+)?(?:})?#i',
      							array('Sedo_MarkitUpIntegrator_Listener_Templates', 'activateXenOptions'),
      							$params['xenSet']
      						);
					
      					$compiler = new XenForo_Template_Compiler($params['xenSet']);
      					$segments = $compiler->lexAndParse();
      					$phrasesUsed =  $compiler->identifyPhrasesInParsedTemplate($segments);
      					$translatedPhrases = array();

					foreach($phrasesUsed as $phraseKey)
					{
						$translatedPhrases[$phraseKey] = new XenForo_Phrase($phraseKey); //require globally cached phrases
					}

      					$fakePhraseCache = array($lang_id => $translatedPhrases);
      					$compiler->mergePhraseCache($fakePhraseCache);
      					$compiled = $compiler->compileParsed($segments, 'MarkitUpIntegrator', $style_id, $lang_id);

      					eval($compiled);
      					$params['xenSet'] = html_entity_decode($__output);

      				$mergedParams = array_merge($template->getParams(), $hookParams);
      				$mergedParams = array_merge($mergedParams, $params);

      				$contents .= $template->create('MarkitUpIntegrator', $mergedParams);
			break;
        	}
	}
	
	public static function minifyCSS($string)
	{
		//Source: http://kitmacallister.com/2011/minify-css-with-php/
		$string = preg_replace('!/\*.*?\*/!s','', $string);
		$string = preg_replace('/\n\s*\n/',"\n", $string);
		$string = preg_replace('/[\n\r \t]/',' ', $string);
		$string = preg_replace('/ +/',' ', $string);
		$string = preg_replace('/ ?([,:;{}]) ?/','$1', $string);
		$string = preg_replace('/;}/','}', $string);

		return $string;
	}
	
	public static function activateXenOptions($matches)
	{
		$options = XenForo_Application::get('options');
		
		if (isset($matches[2]))
		{
			return $options->$matches[1][$matches[2]];
		}
		else
		{
			return $options->$matches[1];		
		}
	}
	
	public static function bakeSmilies($button, $buttonid)
	{	
		//@http://xenforo.com/community/threads/scope-problem-in-xenforo_viewpublic_helper_editor.10423/
		if (XenForo_Application::isRegistered('smilies'))
		{
			$smilies = XenForo_Application::get('smilies');
		}
		else
		{
			$smilies = XenForo_Model::create('XenForo_Model_Smilie')->getAllSmiliesForCache();
			XenForo_Application::set('smilies', $smilies);
		}

		$path = (self::$ie8fix != false) ? self::$ie8fix . '/' : '';
		$cmd = "{name:'{xen:jsescape {xen:phrase miu_button_smilies}, single}', className: 'miu_smilies', dropMenu: [\n";
		$css = '';
		$i=1;

		foreach ($smilies as $smilie)
		{
			$limit =  filter_var(XenForo_Template_Helper_Core::styleProperty('miu_public_smilies_number'), FILTER_SANITIZE_NUMBER_INT);
			$smilieid = $smilie['smilie_id'];
			$title = addslashes($smilie['title']);
			$code = addslashes($smilie["smilieText"][0]);
			$url = $path . $smilie['image_url'];

			if(isset($smilie['sprite_params']))
			{
				$x = $smilie['sprite_params']['x'] . 'px';
				$y = $smilie['sprite_params']['y'] . 'px';
				$width = $smilie['sprite_params']['w'] . 'px';
				$height = $smilie['sprite_params']['h'] . 'px';

				$cmd .= "{name:'$title', openWith:'$code', className:\"hasSprite s_$smilieid\" },\n";
				$css .= ".markItUpButton$buttonid-$i a{ background:url('$url') no-repeat $x $y; width: $width; height: $height }\n";
			}
			else
			{
				/*
				if(strpos($url, 'http://') === false)
				{
					$boardUrl = XenForo_Application::get('options')->get('boardUrl');
					$url = (strpos($url, '/') === 0) ? $boardUrl.$url : $boardUrl.'/'.$url;
				}
				*/

				$cmd .= "{name:'$title', openWith:'$code', className:\"noSprite s_$smilieid\", img:'$url' },\n";
			}

			if($i == $limit)
			{
				break;
			}
			
			$i++;
		}

		//Delete the last coma for IE...
		$cmd = substr($cmd, 0, -1);
		$cmd .= "]},";

		self::$smilies = array('cmd' => $cmd, 'autoCss' => $css);
	}
}
//Zend_Debug::dump($contents);