<?php

class Sedo_MarkitUpIntegrator_Tms_Mods
{
	public static function editor(&$templateText, &$applyCount, $styleId)
	{
		//Don't load "editor_js_setup"
		$search[] = '#<xen:include\s*?template="editor_js_setup"\s*?/>#ui';
		$replace[] = '<xen:if is="{$visitor.miu_rte_reverse}">
			<div class="miu_trigger" data-load="{xen:phrase loading}" data-stopsafe="{$xenOptions.markitup_integration_debug_dynamicmce_error}"><a href="javascript:">{xen:phrase use_rich_text_editor}</a></div>
		<xen:else />
        		$0
		</xen:if>';

		//Load bbcode message
		$search[] = '#<textarea name="{\$formCtrlNameHtml}".*?</textarea>#si';
		$replace[] =	'<xen:if is="{$visitor.miu_rte_reverse}">
					<textarea name="{$formCtrlNameHtml}" id="{$editorId}_html" class="textCtrl MessageEditor MiuRevert" style="display:none; {xen:if $height, \'height: {$height};\'}">{$messageHtml}</textarea>	
					<textarea id="{$editorId}_miu" rows="5" class="textCtrl MessageEditor MiuRevert MiuTarget" name="message" style="overflow: hidden;">{$message}</textarea>
				<xen:else />
					$0
				</xen:if>';
		
		//MarkItUp Tools (ie: color picker)
		$search[] = '#</xen:hook>#ui';
		$replace[] = "$0\n<xen:include template=\"MarkitUpIntegrator_Tools\" />";

		$templateText = preg_replace($search, $replace, $templateText, -1, $count);
		$applyCount = $count;	
	}
	
	public static function editorjs(&$templateText, &$applyCount, $styleId)
	{
		/*
			TinyMCE onInit: only clean way to execute a function AFTER TinyMCE is loaded in the DOM
			If another addon needs it, this part  might need to merged with its
		*/
		
			//Fix editor with on overlay with IE8 step 1 
			$search[] = "#([ \t]*?)theme:\s*?'xenforo',#i";
			$replace[] = "$0\n$1oninit : mceIsReady,";

			//Fix editor with on overlay with IE8 step 2
			$search[] = "#([ \t]*?)tinyMCE.init\(#i";
			$replace[] = "$1function mceIsReady() { 
						if ($.browser.msie && parseInt($.browser.version, 10) == 8 ){
							$('.xenOverlay').find('.mceLayout').removeClass('mceLayout').addClass('mceLayoutSafe');
						}
			} \n$0";		


		$templateText = preg_replace($search, $replace, $templateText, -1, $count);
		$applyCount = $count;	
	}
}