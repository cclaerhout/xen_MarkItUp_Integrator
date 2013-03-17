
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
			var t = XenForo.MiuToTiny;
			
			t.editor = $element.siblings('textarea.MiuTarget')
				.markItUp(myBbcodeSettings)
				.show();

			if(myBbcodeSettings.noResize !== 1)
			{
				t.editor.autosize();
			}
				
			//Check if editor has html content & if yes, parse it (avoid problems with TinyMCE autosave)
			XenForo.MiuToTiny.htmlContentCheck(t.editor);

			//Load TinyMCE on click				
			$element.click(function(e) {
				e.preventDefault();

				if ($.browser.msie && parseInt($.browser.version, 10) <= 7 && ($(this).attr('data-stopsafe') == 0)) {
					return alert('Not compatible with Internet Explorer '+parseInt($.browser.version, 10));
				}
			
				$miuEditor = $(this)
					.html($(this).attr('data-load')+'...')
					.siblings('.bbcode')
					.find('textarea.MiuTarget');
								
				t.trigger = $(this);
				t.activeMiuEditor = $miuEditor;
				t.activeMiuEditorId = $miuEditor.attr('id').replace(/_miu/g, ''); //needed for editor_js_setup template
				t.activeMiuEditorVal = $miuEditor.val();
				
	  			XenForo.ajax(
					'index.php?editor/miu-to-tiny',
					{ editorId: t.activeMiuEditorId, bbCodeContent: t.activeMiuEditorVal },
		  				XenForo.MiuToTiny.ajaxSuccess
				);		
			});
		},
		
		ajaxSuccess: function(ajaxData)
		{
			if (XenForo.hasResponseError(ajaxData) || typeof(ajaxData.templateHtml) == 'undefined')
			{
				return;
			}
	
		  		if (ajaxData.templateHtml)
		  		{
					var t = XenForo.MiuToTiny;
					
					//Move all Miu Tools before doing anything else
					$form = t.trigger.parents('form');
					$('.miuTools').prependTo($form);

		  			//Replace bbCode content with html content, remove Miu, and hide editor
		  			$('#'+t.activeMiuEditorId+'_html').val(ajaxData.htmlContent);
		  			t.activeMiuEditor.parents('.bbcode').remove();

		  			new XenForo.ExtLoader(ajaxData, function()
		  			{
		  				//Load editor_js_setup template
		  				$(ajaxData.templateHtml).xfInsert('replaceAll', t.trigger, 'xfFadeIn');
		  			});
		  		}
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
			var t = XenForo.MiuToTiny;
			
			if (XenForo.hasResponseError(ajaxData) || typeof(ajaxData.bbCode) == 'undefined')
			{
				return false;
			}
			
			t.editor.val(ajaxData.bbCode);
		}				
	};

	 XenForo.register('textarea.textCtrl', 'XenForo.miu_rte');
	 XenForo.register('textarea.MessageEditor', 'XenForo.miu_notrte');
	 XenForo.register('.miu_trigger', 'XenForo.MiuToTiny.init');
	 	 
}
(jQuery, this, document);