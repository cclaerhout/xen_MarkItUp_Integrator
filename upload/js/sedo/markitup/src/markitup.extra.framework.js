// Framework for XenForo MarkItUp Integrator (Buttons & Overlays)
//By Cédric CLAERHOUT
!function($, window, document, _undefined)
{
	XenForo.MiuFramework =
	{
		init : function(e){
		
		},
		_overlay_callbacks : function(src, miu, ontrigger, onload) {
			var t = XenForo.MiuFramework;
			t.t = src;
			t.miu = miu;
			t.ontrigger = ontrigger;
			t.onload = onload;
		},
		_overlay_success: function(ajaxData)
		{
			var t = XenForo.MiuFramework.t;

			if (XenForo.hasResponseError(ajaxData) || typeof(ajaxData.templateHtml) == 'undefined')
			{
				return;
			}
	
		  		if (ajaxData.templateHtml)
		  		{
		  			new XenForo.ExtLoader(ajaxData, function()
		  			{
						var overlay, overtop, isTouch = XenForo.isTouchBrowser();
						if(isTouch) { overtop = '10%'; } else { overtop = 'center'; }

		  				overlay = $(ajaxData.templateHtml)
		  					.xfInsert('prependTo', 'body', 'xfFadeIn')
							.overlay({
								mask: {
							        	color: '#000000',
							        	zIndex: 10010,
								        loadSpeed: 200,
								        opacity: 0.6
								},
								onBeforeLoad: function(e) {
									$('.xenOverlay').css('opacity', '0.5');
								},
								onLoad: function(e) {
									$myOverlay = this.getOverlay().insertAfter('#exposeMask').addClass('miuLoaded');//@http://stackoverflow.com/questions/1939494/
									$trigger = $myOverlay.find('.miuTrigger');
									$focus =  $myOverlay.find('.miuFocus');
									$inputs = $myOverlay.find('input,textarea,select')


									//Hook onload
									if (typeof XenForo.MiuFramework.onload != 'undefined')
									{
										t[XenForo.MiuFramework.onload](XenForo.MiuFramework.miu, $myOverlay);
									}
									
										$inputs.not('textarea').bind('keypress', function(e){
											//Map "return" Key to the .miuTriger button
											if ( e.which == 13 )
											{
												e.preventDefault();
												$trigger.trigger('click');
											}
										});
										
										$trigger.one('click', function (e){
											/****
												Don't know why, but I need to use "stopImmediatePropagation" otherwhise when the overlay
												is closed with the close button, then reopened and closed after an MIU insertion, this insertion
												will be	executed the number of times the overlay would have been closed with the close button
											***/
											e.stopImmediatePropagation();
											var inputs = {};

											$inputs.each(function(index) {
												inputs[$(this).attr('name')] = $(this).val();
											});

											overlay.close();
											
											if (typeof XenForo.MiuFramework.ontrigger != 'undefined')
											{
												t[XenForo.MiuFramework.ontrigger](XenForo.MiuFramework.miu, inputs);
											}
		
											return false;
										});
						
										$focus.focus();
									},
      									onClose: function(e) {
      										this.getOverlay().remove();
      										$('.xenOverlay').css('opacity', '1').expose();
      									},
      									top: overtop,
      									left:0, //will be centered by css... better to avoid bugs
      							        	fixed:!isTouch,
      									closeOnClick: false,
      									oneInstance: false,
      									load: true,
      									api:true
      								}).load();

								return false;		  					
		  			});

		  		}
		},
		/* Use to get data from TinyMCE and send them inside a textarea or input - available options: space */
		_unescapeHtml : function(string, options) 
		{
			string = string
				.replace(/&amp;/g, "&")
				.replace(/&lt;/g, "<")
				.replace(/&gt;/g, ">")
				.replace(/&quot;/g, '"')
				.replace(/&#039;/g, "'");
				
			if(options == 'space')
			{
				string = string
					.replace(/    /g, '\t')
					.replace(/&nbsp;/g, '  ')
					.replace(/<\/p>\n<p>/g, '\n');
			}
	
			var regex_p = new RegExp("^<p>([\\s\\S]+)</p>$", "i"); //Memo: No /s flag in javascript => need to use [\s\S] but in RegExp need to escape 'character classes' backslash
			if(regex_p.test(string))
			{
				string = string.match(regex_p);
				string = string[1];
			}
				
			return string;
		},
		/* Use to get data from textarea/inputs and send inside TinyMCE - available options: space or onlyspace */
		_escapeHtml: function(string, options) 
		{
			//No need anymore with editor/to-html ?
			if( options != 'onlyspace' )
			{
				string = string
					.replace(/&/g, "&amp;")
					.replace(/</g, "&lt;")
					.replace(/>/g, "&gt;")
					.replace(/"/g, "&quot;")
					.replace(/'/g, "&#039;");
			}
			
			//Must be executed in second
			if(options == 'space' || options == 'onlyspace')
			{
				string = string
					.replace(/\t/g, '    ')
					.replace(/ /g, '&nbsp;')
					.replace(/\n/g, '</p>\n<p>');
			}
	
			return string;
		},
		_zen2han: function(str)
		{
			// ==========================================================================
			// Project:   SproutCore - JavaScript Application Framework
			// Copyright: ©2006-2011 Strobe Inc. and contributors.
			//            ©2008-2011 Apple Inc. All rights reserved.
			// License:   Licensed under MIT license (see license.js)
			// ==========================================================================
			var nChar, cString= '', j, jLen;
			//here we cycle through the characters in the current value
			for (j=0, jLen = str.length; j<jLen; j++)
			{
				nChar = str.charCodeAt(j);
			       //here we do the unicode conversion from zenkaku to hankaku roomaji
				nChar = ((nChar>=65281 && nChar<=65392)?nChar-65248:nChar);
		
				//MS IME seems to put this character in as the hyphen from keyboard but not numeric pad...
				nChar = ( nChar===12540?45:nChar) ;
				cString = cString + String.fromCharCode(nChar);
			}
			return cString;
		},
		isUrl: function(string)
		{
			var pattern = new RegExp('(?:(?:https?|ftp|file)://|www\.|ftp\.)[-A-Z0-9+&@#/%=~_|$?!:,.]*[A-Z0-9+&@#/%=~_|$]','i');

			if(XenForo.MiuFramework.hasUnicode(string))
			{
				//No way to check unicode with js regex... so let's do the minimum
				pattern = new RegExp('(?:(?:https?|ftp|file)://|www\.|ftp\.).*','i');
			}
	
			return pattern.test(string);
		},
		hasUnicode: function(string)
		{
			//@http://stackoverflow.com/questions/147824
			for(var i = 0, n = string.length; i < n; i++) 
			{
				if (string.charCodeAt(i) > 255) 
					return true; 
			}
			return false;
		}
	};
	
	 XenForo.register('.markItUpEditor', 'XenForo.MiuFramework.init');
}
(jQuery, this, document);	
	
	
	