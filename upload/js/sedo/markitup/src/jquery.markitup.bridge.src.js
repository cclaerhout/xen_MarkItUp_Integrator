/****
*	---MIU ADDON FOR XENFORO by Sedo---
*
*	These functions will integrate the MarkItUp editor inside XenForo:
*	> XenForo.miu_rte is for the RTE EDITOR (TinyMCE => MIU)
*	> XenForo.miu_notrte is for the basic textarea (loads MIUE)
*	> XenForo.MiuToTiny is for the RTE EDITOR but will load MIU first and will load TinyMCE only on demand
*
****/
!function($, window, document, _undefined)
{	
	XenForo.miu_rte = function($element)
	{
		if($element.parent().hasClass('bbCodeEditorContainer'))
		{
			$element.markItUp(myBbcodeSettings);
		}
	};
	
	XenForo.miu_notrte = function($element)
	{
		if($element.hasClass('BbCodeWysiwygEditor'))
			return false;
		
		if(!$element.hasClass('MiuRevert') && !window.tinyMCE)
		{
			$element.markItUp(myBbcodeSettings);

			if(myBbcodeSettings.noResize !== 1)
			{
				$element.autosize();
			}
		}
	};

	XenForo.MiuToTiny = 
	{
		init: function($element)
		{
			//Init Miu & Autosize
			var self = XenForo.MiuToTiny;
			
			self.editor = $element.parents('form').find('textarea.MiuTarget')
				.markItUp(myBbcodeSettings)
				.show();

			if(myBbcodeSettings.noResize !== 1)
			{
				self.editor.autosize();
			}
			
			if(self.editor.length == 0)
				return false;
			//Check if editor has html content & if yes, parse it (avoid problems with TinyMCE autosave)
			XenForo.MiuToTiny.htmlContentCheck(self.editor);

			//Load TinyMCE on click				
			$element.click(function(e) {
				e.preventDefault();
				e.stopPropagation();//Needed for overlays

				$src = $(this);

				if ($.browser.msie && parseInt($.browser.version, 10) <= 7 && ($(this).attr('data-stopsafe') == 0)) {
					return alert('Not compatible with Internet Explorer '+parseInt($.browser.version, 10));
				}
			
				$miuEditor = $src
					.html($src.attr('data-load')+'...')
					.siblings('.bbcode')
					.find('textarea.MiuTarget');
					

				self.trigger = $src;
				self.activeMiuEditor = $miuEditor;

	  			XenForo.ajax(
					'index.php?editor/miu-to-tiny',	{
						editorId: $miuEditor.attr('id'),
						editorName: $miuEditor.attr('name'),
						bbCodeContent: $miuEditor.val()
					}, XenForo.MiuToTiny.ajaxSuccess
				);		
			});
		},
		
		ajaxSuccess: function(ajaxData)
		{
			var self = XenForo.MiuToTiny, un = 'undefined';
			
			if (	XenForo.hasResponseError(ajaxData) 
				|| typeof(ajaxData.templateHtml) == un
				|| !ajaxData.templateHtml
			)
			{
				return;
			}

			//Move all Miu Tools before doing anything else
			$form = self.trigger.parents('form');
			$('.miuTools').prependTo($form);

			//Remove the original editor
			self.activeMiuEditor.parents('.bbcode').remove();

			//Get template
	  		$templateHtml = $(ajaxData.templateHtml);
		  		
			//Get the elements that should be hidden - Why ? The ExtLoader or the xfInsert function show them all, I'm not sure why.
			$hideThis = $templateHtml.filter(function( index ) {
				if(this.style) {
					return $(this).css('display') == 'none';
				}
			})

		  	new XenForo.ExtLoader(ajaxData, function()
		  	{
	  			$templateHtml.xfInsert('replaceAll', self.trigger, 'xfFadeIn');
	  			$hideThis.hide();
			});
		},

		htmlContentCheck: function($editor)
		{		
			var content = $editor.val();

			if(content.length > 0 && content.match(/<(\w+)(?:[^>]+?)?>.*?<\/\1>/i))
			{
				console.info('Html content found - Loading XenForo BbCode Parser');

				XenForo.ajax(
					'index.php?editor/to-bb-code',
					{ html: content },
					XenForo.MiuToTiny.htmlToBbcode
				);
						
				return false;
			}
		},

		htmlToBbcode: function(ajaxData)
		{
			var self = XenForo.MiuToTiny;
			
			if (XenForo.hasResponseError(ajaxData) || typeof(ajaxData.bbCode) == 'undefined')
			{
				return false;
			}
			
			self.editor.val(ajaxData.bbCode);
		}				
	};

	 XenForo.register('textarea.textCtrl', 'XenForo.miu_rte');
	 XenForo.register('textarea.MessageEditor', 'XenForo.miu_notrte');
	 XenForo.register('.miu_trigger', 'XenForo.MiuToTiny.init');
	 	 
}
(jQuery, this, document);