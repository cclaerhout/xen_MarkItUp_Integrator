
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
			
			self.editor = $element.siblings('textarea.MiuTarget')
				.markItUp(myBbcodeSettings)
				.show();

			if(myBbcodeSettings.noResize !== 1)
			{
				self.editor.autosize();
			}
				
			//Check if editor has html content & if yes, parse it (avoid problems with TinyMCE autosave)
			XenForo.MiuToTiny.htmlContentCheck(self.editor);

			//Load TinyMCE on click				
			$element.click(function(e) {
				e.preventDefault();

				if(jQuery.fn.jquery !== '1.5.2'){
					alert('This function has been disabled');
					return false;// BUGS WITH XEN 1.2
				}

				if ($.browser.msie && parseInt($.browser.version, 10) <= 7 && ($(this).attr('data-stopsafe') == 0)) {
					return alert('Not compatible with Internet Explorer '+parseInt($.browser.version, 10));
				}
			
				$miuEditor = $(this)
					.html($(this).attr('data-load')+'...')
					.siblings('.bbcode')
					.find('textarea.MiuTarget');
					
				var src = ($(this).data('src') == 'mce4') ? 'mce4' : 'xen',
				cName = $(this).data('cName'), cAction = $(this).data('cAction'), vName = $(this).data('vName');
				
				self.isMCE4 = (src == 'mce4') ? true : false;

				self.trigger = $(this);
				self.activeMiuEditor = $miuEditor;
				self.activeMiuEditorId = $miuEditor.attr('id').replace(/_miu/g, ''); //needed for editor_js_setup template
				self.activeMiuEditorVal = $miuEditor.val();

	  			XenForo.ajax(
					'index.php?editor/miu-to-tiny',	{
						editorId: self.activeMiuEditorId,
						bbCodeContent: self.activeMiuEditorVal,
						src: src,
						cName: cName,
						cAction: cAction,
						vName: vName
					}, XenForo.MiuToTiny.ajaxSuccess
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
					var self = XenForo.MiuToTiny, un = 'undefined';
					$activeEditor = $('#'+self.activeMiuEditorId+'_html');
					
					//Move all Miu Tools before doing anything else
					$form = self.trigger.parents('form');
					$('.miuTools').prependTo($form);

		  			//Replace bbCode content with html content, remove Miu, and hide editor
		  			$activeEditor.val(ajaxData.htmlContent); //Xen 1.2 editors don't care of this and init the editors
		  			self.activeMiuEditor.parents('.bbcode').remove();

		  			new XenForo.ExtLoader(ajaxData, function()
		  			{
		  				//Load editor_js_setup template
		  				$(ajaxData.templateHtml).xfInsert('replaceAll', self.trigger, 'xfFadeIn');
		  				
		  				if(typeof XenForo.BbmCustomEditor !== un && !self.isMCE4)
		  				{
		  					new XenForo.BbmCustomEditor($activeEditor);
		  				}
		  				
		  				if(typeof XenForo.BbCodeWysiwygEditor !== un && !self.isMCE4)
		  				{
			  				new XenForo.BbCodeWysiwygEditor($activeEditor);
			  			}
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