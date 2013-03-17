//Extra tools for XenForo MarkItUp Integrator
//By Cédric CLAERHOUT
(function($) {
	XenForo.miuExtra = {
		colorPicker: function(markItUp, jsRoot) {
			try {
				$.farbtastic('#miuSelectColor').linkTo('#miuGetColor');
			}
			catch(e) {
				this.colorPicker_init(markItUp, jsRoot);
			}
	
			$editor = $(markItUp.textarea).parents('.markItUpContainer');
			$editor.find('.customColors').parent().addClass('miuBasicColorPicker').parent().addClass('miuColorButton');
			
			//Dynamic display
			$('.markItUpLine').css({ position: 'static'}); //needed to get the correct position (easiest way to do it I think)
			var pos = $editor.find('.miuColorButton').position(),
			buttonHeight = $editor.find('.miuColorButton').height();
			$('#miuAdvColorPicker').prependTo($editor).css({ top: pos.top + buttonHeight, left:pos.left}).show();
			$('.markItUpLine').css({ position: 'relative'});//toggle line position to relative (zindex is needed on lines because of IE7)
	
			return false;
		},
		colorPicker_init: function(markItUp, jsRoot) {
			$.getScript(jsRoot + "/sedo/markitup/farbtastic.js")
				.done(function(script, textStatus) {
					//Bind this cmd: Close Color Picker if click outside
					$('html').bind('click', function() {
						$('#miuAdvColorPicker').hide();
	      				});
	
			      		$('#miuAdvColorPicker').bind('click', function(e){
	      					e.stopPropagation();
	      				});
					
					//Activate color picker
					$.farbtastic('#miuSelectColor').linkTo('#miuGetColor');
					
					//Send colors to the editor
					$('#miuSendColor').bind('click', function() {
						var color = $('#miuGetColor').val();
						$('#miuAdvColorPicker').hide();

						$.markItUp({ 
				      			openWith: '[color='+color+']', 
				      			closeWith: '[/color]',
							caretPositionIE: markItUp.caretPosition,
							selectionIE: markItUp.selection			      			
				      		});

						//Move tool to avoid to be deleted when TinyMCE is loading
						$form = $(this).closest('form');
						$(this).closest('#miuAdvColorPicker').prependTo($form);
					
						return false;
					}); 				
				})
				.fail(function(jqxhr, settings, exception) {
		  			console.info('Error: Farbtastic was NOT Loaded');
		  			var phrase = $('#miuAdvColorPicker').attr('data-fail');
	
			      		$.markItUp({ 
			      				openWith:"[color=#[!["+phrase+"]!]]",
			      				closeWith:"[/color]",
							caretPositionIE: markItUp.caretPosition,
							selectionIE: markItUp.selection	 
			      		} );
			      		
			      		return false;
				}); 
		},
		linkManager: function(miu, jsRoot)
		{
			XenForo.MiuFramework._overlay_callbacks(XenForo.miuExtra, miu, 'linkManager_ontrigger', 'linkManager_onload');
			XenForo.ajax('index.php?editor/miu-dialog',  { dialog: 'Url'}, XenForo.MiuFramework._overlay_success);
		},
		linkManager_onload: function(miu, $overlay)
		{
			$element_url = $overlay.find('.miuLinkUrl');
			$element_text = $overlay.find('.miuLinkText');
			
			if(XenForo.MiuFramework.isUrl(miu.selection))
			{
				$element_url.val(miu.selection);
				$element_text.focus();
			}
			else if(miu.selection.length > 0)
			{
				$element_text.val(miu.selection);
				$element_url.focus();
			}
			else
			{
				$element_url.focus();			
			}	
		},
		linkManager_ontrigger: function(miu, inputs)
		{
			if(!inputs.miulinkurl){ 
				return false;
			}
			
			if(!inputs.miulinktext){ 
				inputs.miulinktext = inputs.miulinkurl;
			};

			$.markItUp({ 
	      			replaceWith: '[url='+inputs.miulinkurl+']'+inputs.miulinktext+'[/url]', 
				caretPositionIE: XenForo.MiuFramework.miu.caretPosition,
				selectionIE: XenForo.MiuFramework.miu.selection				      			
	      		});		
		},
		mediaManager: function (miu, jsRoot)
		{
			XenForo.MiuFramework._overlay_callbacks(XenForo.miuExtra, miu, 'mediaManager_ontrigger');
			XenForo.ajax('index.php?editor/miu-dialog',  { dialog: 'Media'}, XenForo.MiuFramework._overlay_success);
		},
		mediaManager_ontrigger: function(miu, inputs)
		{
			if(!inputs.miulinkmedia){ 
				return false;
			}

			XenForo.ajax(
				'index.php?editor/media',
				{ url: inputs.miulinkmedia },
				this.mediaManager_ajax
			);
			return;				
		},
		mediaManager_ajax: function(ajaxData)
		{
			if (XenForo.hasResponseError(ajaxData))
			{
				return false;
			}

			if (ajaxData.matchBbCode)
			{
				$.markItUp({ 
		      			replaceWith: ajaxData.matchBbCode, 
					caretPositionIE: XenForo.MiuFramework.miu.caretPosition,
					selectionIE: XenForo.MiuFramework.miu.selection			      			
		      		});
			}
			else if (ajaxData.noMatch)
			{
				alert(ajaxData.noMatch);
			}
		},
		imgManager: function (miu, jsRoot)
		{
			XenForo.MiuFramework._overlay_callbacks(XenForo.miuExtra, miu, 'imgManager_ontrigger', 'imgManager_onload');
			XenForo.ajax('index.php?editor/miu-dialog',  { dialog: 'Img'}, XenForo.MiuFramework._overlay_success);
		},
		imgManager_onload: function(miu, $overlay)
		{
			if(miu.selection.length > 0)
			{
				$overlay.find('.miuLinkImg').val(miu.selection);
			}		
		},
		imgManager_ontrigger: function(miu, inputs)
		{
			if(!inputs.miulinkimg){ 
				return false;
			}

			$.markItUp({ 
	      			replaceWith: '[img]'+inputs.miulinkimg+'[/img]', 
				caretPositionIE: XenForo.MiuFramework.miu.caretPosition,
				selectionIE: XenForo.MiuFramework.miu.selection			      			
	      		});	
		
		},		
		indent: function(miu)
		{
			var regex1, regex2, builder, matches, match, captures;
	
			regex1 = /(\[indent=)(\d{1,2})?(\])(.*?\[\/indent\])/gi;
			regex2 = /(\[indent=)(\d{1,2})?(\])(.*?\[\/indent\])/i;
			builder = miu.selection;	
	
			if(regex1.test(miu.selection)) { 
				matches = miu.selection.match(regex1); 
	
				for(match in matches) {
					captures = regex2.exec(matches[match]);
					if(captures){
						captures[2]++;
						builder = builder.replace(captures[0], captures[1]+captures[2]+captures[3]+captures[4]);
					}
				} 
				return builder;
			}
			else{
				return '[indent=1]'+builder+'[/indent]';
			}
		
		},
		outdent: function(miu)
		{
			var regex1, regex2, builder, matches, match, captures;
		
			regex1 = /(\[indent=)(\d{1,2})?(\])(.*?\[\/indent\])/gi;
			regex2 = /(\[indent=)(\d{1,2})?(\])(.*?\[\/indent\])/i;
			builder = miu.selection;	
	
			if(regex1.test(miu.selection)) { 
				matches = miu.selection.match(regex1); 
	
				for(match in matches) {
					captures = regex2.exec(matches[match]);
					if(captures){
						captures[2]--;
						if (captures[2] == 0){
							builder = builder.replace(captures[0], captures[1]+'1'+captures[3]+captures[4]);
						}
						else{
							builder = builder.replace(captures[0], captures[1]+captures[2]+captures[3]+captures[4]);
						}
					}
				} 
	
				return builder;
			}
			else{
				return false;
			}
		}
	};
})(jQuery);