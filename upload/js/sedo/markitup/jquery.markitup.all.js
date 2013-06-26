// Autosize 1.14 - jQuery plugin for textareas (c) 2012 Jack Moore - jacklmoore.com License: www.opensource.org/licenses/mit-license.php Src: http://www.jacklmoore.com/autosize
(function(e){var t={className:"autosizejs",append:"",callback:!1},n="hidden",r="border-box",i="lineHeight",s='<textarea tabindex="-1" style="position:absolute; top:-9999px; left:-9999px; right:auto; bottom:auto; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden;"/>',o=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent"],u="oninput",a="onpropertychange",f=e(s)[0];f.setAttribute(u,"return"),e.isFunction(f[u])||a in f?(e(f).css(i,"99px"),e(f).css(i)==="99px"&&o.push(i),e.fn.autosize=function(i){return i=e.extend({},t,i||{}),this.each(function(){function b(){var e,r,s;p||(p=!0,l.value=t.value+i.append,l.style.overflowY=t.style.overflowY,s=parseInt(t.style.height,10),l.style.width=f.css("width"),l.scrollTop=0,l.scrollTop=9e4,e=l.scrollTop,r=n,e>h?(e=h,r="scroll"):e<c&&(e=c),e+=m,t.style.overflowY=r,s!==e&&(t.style.height=e+"px",y&&i.callback.call(t)),setTimeout(function(){p=!1},1))}var t=this,f=e(t),l,c=f.height(),h=parseInt(f.css("maxHeight"),10),p,d=o.length,v,m=0,g=t.value,y=e.isFunction(i.callback);if(f.css("box-sizing")===r||f.css("-moz-box-sizing")===r||f.css("-webkit-box-sizing")===r)m=f.outerHeight()-f.height();if(f.data("mirror")||f.data("ismirror"))return;l=e(s).data("ismirror",!0).addClass(i.className)[0],v=f.css("resize")==="none"?"none":"horizontal",f.data("mirror",e(l)).css({overflow:n,overflowY:n,wordWrap:"break-word",resize:v}),h=h&&h>0?h:9e4;while(d--)l.style[o[d]]=f.css(o[d]);e("body").append(l),a in t?u in t?t[u]=t.onkeyup=b:t[a]=b:(t[u]=b,t.value="",t.value=g),e(window).resize(b),f.bind("autosize",b),b()})}):e.fn.autosize=function(e){return this}})(jQuery);

// markItUp! Universal MarkUp Engine, JQuery plugin v 1.1.x , Dual licensed under the MIT and GPL licenses. Copyright (C) 2007-2012 Jay Salvat, http://markitup.jaysalvat.com 
// Miu 2.0 - Modified version for XenForo by C�dric CLAERHOUT
(function($){$.fn.markItUp=function(settings,extraSettings){var method,params,options,ctrlKey,shiftKey,altKey;return ctrlKey=shiftKey=altKey=!1,typeof settings=="string"&&(method=settings,params=extraSettings),options={id:"",nameSpace:"",root:"",previewHandler:!1,previewInWindow:"",previewInElement:"",previewAutoRefresh:!0,previewPosition:"after",previewTemplatePath:"~/templates/preview.html",previewParser:!1,previewParserPath:"",previewParserVar:"data",resizeHandle:!0,beforeInsert:"",afterInsert:"",onEnter:{},onShiftEnter:{},onCtrlEnter:{},onTab:{},markupSet:[{}]},$.extend(options,settings,extraSettings),options.root||$("script").each(function(n,t){miuScript=$(t).get(0).src.match(/(.*)jquery\.markitup(\.pack)?\.js$/),miuScript!==null&&(options.root=miuScript[1])}),this.each(function(){function localize(n,t){return t?n.replace(/("|')~\//g,"$1"+options.root):n.replace(/^~\//,options.root)}function init(){id="",nameSpace="",options.id?id='id="'+options.id+'"':$$.attr("id")&&(id='id="markItUp'+$$.attr("id").substr(0,1).toUpperCase()+$$.attr("id").substr(1)+'"'),options.nameSpace&&(nameSpace='class="'+options.nameSpace+'"'),$$.wrap("<div "+nameSpace+"></div>"),$$.wrap("<div "+id+' class="markItUp"></div>'),$$.wrap('<div class="markItUpContainer"></div>'),$$.addClass("markItUpEditor"),header=$('<div class="markItUpHeader"></div>').insertBefore($$),$(dropMenus(options.markupSet)).appendTo(header);var n=99;$(".markItUpHeader > ul").children("li.markItUpButton,li.markItUpSeparator").wrapAll('<li class="markItUpLine" />').end().children(".markItUpLine").each(function(t){$(this).css("z-index",n-t),$(this).children().wrapAll('<ul class="markItUpParent"/>'),$(this).find(".hasMenu").children("ul").addClass("markItUpChild")}),footer=$('<div class="markItUpFooter"></div>').insertAfter($$),options.resizeHandle===!0&&$.browser.safari!==!0&&(resizeHandle=$('<div class="markItUpResizeHandle"></div>').insertAfter($$).bind("mousedown.markItUp",function(n){var u=$$.height(),r=n.clientY,t,i;t=function(n){return $$.css("height",Math.max(20,n.clientY+u-r)+"px"),!1},i=function(){return $("html").unbind("mousemove.markItUp",t).unbind("mouseup.markItUp",i),!1},$("html").bind("mousemove.markItUp",t).bind("mouseup.markItUp",i)}),footer.append(resizeHandle)),$$.bind("keydown.markItUp",keyPressed).bind("keyup",keyPressed),$$.bind("insertion.markItUp",function(n,t){t.target!==!1&&get(),textarea===$.markItUp.focused&&markup(t)}),$$.bind("focus.markItUp",function(){$.markItUp.focused=this}),options.previewInElement&&refreshPreview()}function dropMenus(markupSet){var ul=$("<ul></ul>"),i=0;return $("li:hover > ul",ul).css("display","block"),$.each(markupSet,function(){var button=this,t="",title,li,j;if(title=button.key?(button.name||"")+" [Ctrl+"+button.key+"]":button.name||"",key=button.key?'accesskey="'+button.key+'"':"",button.separator)li=$('<li class="markItUpSeparator"><span>'+(button.separator||"")+"</span></li>").appendTo(ul);else if(button.breaker)$(ul).children("li.markItUpButton,li.markItUpSeparator").wrapAll('<li class="markItUpLine" />');else{for(i++,j=levels.length-1;j>=0;j--)t+=levels[j]+"-";button.img&&(button.img='<img src="'+button.img+'" alt="'+button.name+'">'),li=$('<li class="markItUpButton markItUpButton'+t+i+" "+(button.className||"")+'"><a href="" '+key+' title="'+title+'">'+(button.insertBeforeName||"")+(button.img||button.name||"")+(button.insertAfterName||"")+"</a></li>").bind("contextmenu.markItUp",function(){return!1}).bind("focusin.markItUp",function(){$$.focus()}).bind("mouseup",function(){return button.call&&eval(button.call)(),setTimeout(function(){markup(button)},1),!1}).bind("click.markItUp",function(n){n.preventDefault(),$("> ul",this).is(":hidden")?($("> ul",this).show(),$("> ul",this).add(this).bind("mouseleave",function(){$(".markItUpChild",this).hide()})):$("> ul",this).hide()}).appendTo(ul),button.dropMenu&&(levels.push(i),$(li).prepend('<div class="markItUpDropMenu"></div>').addClass("hasMenu").append(dropMenus(button.dropMenu)))}}),levels.pop(),ul}function magicMarkups(n){return n?(n=n.toString(),n=n.replace(/\(\!\(([\s\S]*?)\)\!\)/g,function(n,t){var i=t.split("|!|");return altKey===!0?i[1]!==undefined?i[1]:i[0]:i[1]===undefined?"":i[0]}),n=n.replace(/\[\!\[([\s\S]*?)\]\!\]/g,function(n,t){var i=t.split(":!:");return abort===!0?!1:(value=prompt(i[0],i[1]?i[1]:""),value===null&&(abort=!0),value)})):""}function prepare(n){return $.isFunction(n)&&(n=n(hash)),magicMarkups(n)}function build(n){var i=prepare(clicked.openWith),o=prepare(clicked.placeHolder),e=prepare(clicked.replaceWith),t=prepare(clicked.closeWith),h=prepare(clicked.openBlockWith),l=prepare(clicked.closeBlockWith),c=clicked.multiline,f,r,u,s;if(e!=="")block=i+e+t;else if(selection===""&&o!=="")block=i+o+t;else{for(n=n||selection,f=[n],r=[],c===!0&&(f=n.split(/\r?\n/)),u=0;u<f.length;u++)line=f[u],(s=line.match(/ *$/))?r.push(i+line.replace(/ *$/g,"")+t+s):r.push(i+line+t);block=r.join("\n")}return block=h+block+l,{block:block,openWith:i,replaceWith:e,placeHolder:o,closeWith:t}}function markup(n){var t,u,r,i;if(hash=clicked=n,$.browser.msie&&n.caretPositionIE&&n.selectionIE&&set(n.caretPositionIE,n.selectionIE.length),get(),$.extend(hash,{line:"",root:options.root,textarea:textarea,selection:selection||"",caretPosition:caretPosition,ctrlKey:ctrlKey,shiftKey:shiftKey,altKey:altKey}),prepare(options.beforeInsert),prepare(clicked.beforeInsert),(ctrlKey===!0&&shiftKey===!0||n.multiline===!0)&&prepare(clicked.beforeMultiInsert),$.extend(hash,{line:1}),ctrlKey===!0&&shiftKey===!0){for(lines=selection.split(/\r?\n/),u=0,r=lines.length,i=0;i<r;i++)$.trim(lines[i])!==""?($.extend(hash,{line:++u,selection:lines[i]}),lines[i]=build(lines[i]).block):lines[i]="";string={block:lines.join("\n")},start=caretPosition,t=string.block.length+($.browser.opera?r-1:0)}else ctrlKey===!0?(string=build(selection),start=caretPosition+string.openWith.length,t=string.block.length-string.openWith.length-string.closeWith.length,t=t-(string.block.match(/ $/)?1:0),t-=fixIeBug(string.block)):shiftKey===!0?(string=build(selection),start=caretPosition,t=string.block.length,t-=fixIeBug(string.block)):(string=build(selection),start=caretPosition+string.block.length,t=0,start-=fixIeBug(string.block));selection===""&&string.replaceWith===""&&(caretOffset+=fixOperaBug(string.block),start=caretPosition+string.openWith.length,t=string.block.length-string.openWith.length-string.closeWith.length,caretOffset=$$.val().substring(caretPosition,$$.val().length).length,caretOffset-=fixOperaBug($$.val().substring(0,caretPosition))),$.extend(hash,{caretPosition:caretPosition,scrollPosition:scrollPosition}),string.block!==selection&&abort===!1?(insert(string.block),set(start,t)):caretOffset=-1,get(),$.extend(hash,{line:"",selection:selection}),(ctrlKey===!0&&shiftKey===!0||n.multiline===!0)&&prepare(clicked.afterMultiInsert),prepare(clicked.afterInsert),prepare(options.afterInsert),previewWindow&&options.previewAutoRefresh&&refreshPreview(),shiftKey=altKey=ctrlKey=abort=!1}function fixOperaBug(n){return $.browser.opera?n.length-n.replace(/\n*/g,"").length:0}function fixIeBug(n){return $.browser.msie?n.length-n.replace(/\r*/g,"").length:0}function insert(n){if(document.selection){var t=document.selection.createRange();t.text=n}else textarea.value=textarea.value.substring(0,caretPosition)+n+textarea.value.substring(caretPosition+selection.length,textarea.value.length)}function set(n,t){if(textarea.createTextRange){if($.browser.opera&&$.browser.version>=9.5&&t==0)return!1;range=textarea.createTextRange(),range.collapse(!0),range.moveStart("character",n),range.moveEnd("character",t),range.select()}else textarea.setSelectionRange&&textarea.setSelectionRange(n,n+t);textarea.scrollTop=scrollPosition,textarea.focus()}function get(){if(textarea.focus(),scrollPosition=textarea.scrollTop,document.selection)if(selection=document.selection.createRange().text,$.browser.msie){var t=document.selection.createRange(),n=t.duplicate();for(n.moveToElementText(textarea),caretPosition=-1;n.inRange(t);)n.moveStart("character"),caretPosition++}else caretPosition=textarea.selectionStart;else caretPosition=textarea.selectionStart,selection=textarea.value.substring(caretPosition,textarea.selectionEnd);return selection}function refreshPreview(){renderPreview()}function renderPreview(){var t,n;return options.previewHandler&&typeof options.previewHandler=="function"?options.previewHandler($$.val()):options.previewParser&&typeof options.previewParser=="function"?(n=options.previewParser($$.val()),writeInPreview(localize(n,1))):options.previewParserPath!==""?$.ajax({type:"POST",dataType:"text",global:!1,url:options.previewParserPath,data:options.previewParserVar+"="+encodeURIComponent($$.val()),success:function(n){writeInPreview(localize(n,1))}}):template||$.ajax({url:options.previewTemplatePath,dataType:"text",global:!1,success:function(n){writeInPreview(localize(n,1).replace(/<!-- content -->/g,$$.val()))}}),!1}function writeInPreview(n){if(options.previewInElement)$(options.previewInElement).html(n);else if(previewWindow&&previewWindow.document){try{sp=previewWindow.document.documentElement.scrollTop}catch(t){sp=0}previewWindow.document.open(),previewWindow.document.write(n),previewWindow.document.close(),previewWindow.document.documentElement.scrollTop=sp}}function keyPressed(n){if(shiftKey=n.shiftKey,altKey=n.altKey,ctrlKey=n.altKey&&n.ctrlKey?!1:n.ctrlKey||n.metaKey,n.type==="keydown"){if(ctrlKey===!0&&(li=$('a[accesskey="'+(n.keyCode==13?"\\n":String.fromCharCode(n.keyCode))+'"]',header).parent("li"),li.length!==0))return ctrlKey=!1,setTimeout(function(){li.triggerHandler("mouseup")},1),!1;if(n.keyCode===13||n.keyCode===10)return ctrlKey===!0?(ctrlKey=!1,markup(options.onCtrlEnter),options.onCtrlEnter.keepDefault):shiftKey===!0?(shiftKey=!1,markup(options.onShiftEnter),options.onShiftEnter.keepDefault):(markup(options.onEnter),options.onEnter.keepDefault);if(n.keyCode===9)return shiftKey==!0||ctrlKey==!0||altKey==!0?!1:caretOffset!==-1?(get(),caretOffset=$$.val().length-caretOffset,set(caretOffset,0),caretOffset=-1,!1):(markup(options.onTab),options.onTab.keepDefault)}}function remove(){$$.unbind(".markItUp").removeClass("markItUpEditor"),$$.parent("div").parent("div.markItUp").parent("div").replaceWith($$),$$.data("markItUp",null)}var $$,textarea,levels,scrollPosition,caretPosition,caretOffset,clicked,hash,header,footer,previewWindow,template,iFrame,abort;if($$=$(this),textarea=this,levels=[],abort=!1,scrollPosition=caretPosition=0,caretOffset=-1,options.previewParserPath=localize(options.previewParserPath),options.previewTemplatePath=localize(options.previewTemplatePath),method){switch(method){case"remove":remove();break;case"insert":markup(params);break;default:$.error("Method "+method+" does not exist on jQuery.markItUp")}return}init()})},$.fn.markItUpRemove=function(){return this.each(function(){$(this).markItUp("remove")})},$.markItUp=function(n){var t={target:!1};if($.extend(t,n),t.target)return $(t.target).each(function(){$(this).focus(),$(this).trigger("insertion",[t])});$("textarea").trigger("insertion",[t])}})(jQuery);

// Miu 2.2 XenForo Bridge by C�dric CLAERHOUT
!function(n,t){XenForo.miu_rte=function(n){n.parent().hasClass("bbCodeEditorContainer")&&n.markItUp(myBbcodeSettings)},XenForo.miu_notrte=function(n){if(n.hasClass("BbCodeWysiwygEditor"))return!1;n.hasClass("MiuRevert")||t.tinyMCE||(n.markItUp(myBbcodeSettings),myBbcodeSettings.noResize!==1&&n.autosize())},XenForo.MiuToTiny={init:function(t){var i=XenForo.MiuToTiny;i.editor=t.siblings("textarea.MiuTarget").markItUp(myBbcodeSettings).show(),myBbcodeSettings.noResize!==1&&i.editor.autosize(),XenForo.MiuToTiny.htmlContentCheck(i.editor),t.click(function(t){if(t.preventDefault(),n.browser.msie&&parseInt(n.browser.version,10)<=7&&n(this).attr("data-stopsafe")==0)return alert("Not compatible with Internet Explorer "+parseInt(n.browser.version,10));$miuEditor=n(this).html(n(this).attr("data-load")+"...").siblings(".bbcode").find("textarea.MiuTarget"),i.trigger=n(this),i.activeMiuEditor=$miuEditor,i.activeMiuEditorId=$miuEditor.attr("id").replace(/_miu/g,""),i.activeMiuEditorVal=$miuEditor.val(),XenForo.ajax("index.php?editor/miu-to-tiny",{editorId:i.activeMiuEditorId,bbCodeContent:i.activeMiuEditorVal},XenForo.MiuToTiny.ajaxSuccess)})},ajaxSuccess:function(t){if(!XenForo.hasResponseError(t)&&typeof t.templateHtml!="undefined"&&t.templateHtml){var i=XenForo.MiuToTiny,r="undefined";$activeEditor=n("#"+i.activeMiuEditorId+"_html"),$form=i.trigger.parents("form"),n(".miuTools").prependTo($form),$activeEditor.val(t.htmlContent),i.activeMiuEditor.parents(".bbcode").remove(),new XenForo.ExtLoader(t,function(){n(t.templateHtml).xfInsert("replaceAll",i.trigger,"xfFadeIn"),typeof XenForo.BbmCustomEditor!==r&&new XenForo.BbmCustomEditor($activeEditor),typeof XenForo.BbCodeWysiwygEditor!==r&&new XenForo.BbCodeWysiwygEditor($activeEditor)})}},htmlContentCheck:function(n){var t=n.val();if(t.length>0&&t.match(/<(\w+)(?:[^>]+?)?>.*?<\/\1>/i))return console.info("Html content found - Loading XenForo BbCode Parser"),XenForo.ajax("index.php?editor/to-bb-code",{html:t},XenForo.MiuToTiny.htmlToBbcode),!1},htmlToBbcode:function(n){var t=XenForo.MiuToTiny;if(XenForo.hasResponseError(n)||typeof n.bbCode=="undefined")return!1;t.editor.val(n.bbCode)}},XenForo.register("textarea.textCtrl","XenForo.miu_rte"),XenForo.register("textarea.MessageEditor","XenForo.miu_notrte"),XenForo.register(".miu_trigger","XenForo.MiuToTiny.init")}(jQuery,this,document);

// Miu 2.1 XenForo Framework for overlays by C�dric CLAERHOUT
!function(n){XenForo.MiuFramework={init:function(){},_overlay_callbacks:function(n,t,i,r){var u=XenForo.MiuFramework;u.t=n,u.miu=t,u.ontrigger=i,u.onload=r},_overlay_success:function(t){var i=XenForo.MiuFramework.t;XenForo.hasResponseError(t)||typeof t.templateHtml=="undefined"||t.templateHtml&&new XenForo.ExtLoader(t,function(){var f,r,u=XenForo.isTouchBrowser();return r=u?"10%":"center",f=n(t.templateHtml).xfInsert("prependTo","body","xfFadeIn").overlay({mask:{color:"#000000",zIndex:10010,loadSpeed:200,opacity:.6},onBeforeLoad:function(){n(".xenOverlay").css("opacity","0.5")},onLoad:function(){$myOverlay=this.getOverlay().insertAfter("#exposeMask").addClass("miuLoaded"),$trigger=$myOverlay.find(".miuTrigger"),$focus=$myOverlay.find(".miuFocus"),$inputs=$myOverlay.find("input,textarea,select"),typeof XenForo.MiuFramework.onload!="undefined"&&i[XenForo.MiuFramework.onload](XenForo.MiuFramework.miu,$myOverlay),$inputs.not("textarea").bind("keypress",function(n){n.which==13&&(n.preventDefault(),$trigger.trigger("click"))});$trigger.one("click",function(t){t.stopImmediatePropagation();var r={};return $inputs.each(function(){r[n(this).attr("name")]=n(this).val()}),f.close(),typeof XenForo.MiuFramework.ontrigger!="undefined"&&i[XenForo.MiuFramework.ontrigger](XenForo.MiuFramework.miu,r),!1});$focus.focus()},onClose:function(){this.getOverlay().remove(),n(".xenOverlay").css("opacity","1").expose()},top:r,left:0,fixed:!u,closeOnClick:!1,oneInstance:!1,load:!0,api:!0}).load(),!1})},_unescapeHtml:function(n,t){n=n.replace(/&amp;/g,"&").replace(/&lt;/g,"<").replace(/&gt;/g,">").replace(/&quot;/g,'"').replace(/&#039;/g,"'"),t=="space"&&(n=n.replace(/    /g,"\t").replace(/&nbsp;/g,"  ").replace(/<\/p>\n<p>/g,"\n"));var i=new RegExp("^<p>([\\s\\S]+)</p>$","i");return i.test(n)&&(n=n.match(i),n=n[1]),n},_escapeHtml:function(n,t){return t!="onlyspace"&&(n=n.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;")),(t=="space"||t=="onlyspace")&&(n=n.replace(/\t/g,"    ").replace(/ /g,"&nbsp;").replace(/\n/g,"</p>\n<p>")),n},_zen2han:function(n){for(var t,r="",i=0,u=n.length;i<u;i++)t=n.charCodeAt(i),t=t>=65281&&t<=65392?t-65248:t,t=t===12540?45:t,r=r+String.fromCharCode(t);return r},isUrl:function(n){var t=new RegExp("(?:(?:https?|ftp|file)://|www.|ftp.)[-A-Z0-9+&@#/%=~_|$?!:,.]*[A-Z0-9+&@#/%=~_|$]","i");return XenForo.MiuFramework.hasUnicode(n)&&(t=new RegExp("(?:(?:https?|ftp|file)://|www.|ftp.).*","i")),t.test(n)},hasUnicode:function(n){for(var t=0,i=n.length;t<i;t++)if(n.charCodeAt(t)>255)return!0;return!1}},XenForo.register(".markItUpEditor","XenForo.MiuFramework.init")}(jQuery,this,document);

// Miu 2.1 XenForo Extra Set by C�dric CLAERHOUT
(function(n){XenForo.miuExtra={colorPicker:function(t,i){try{n.farbtastic("#miuSelectColor").linkTo("#miuGetColor")}catch(f){this.colorPicker_init(t,i)}$editor=n(t.textarea).parents(".markItUpContainer"),$editor.find(".customColors").parent().addClass("miuBasicColorPicker").parent().addClass("miuColorButton"),n(".markItUpLine").css({position:"static"});var r=$editor.find(".miuColorButton").position(),u=$editor.find(".miuColorButton").height();return n("#miuAdvColorPicker").prependTo($editor).css({top:r.top+u,left:r.left}).show(),n(".markItUpLine").css({position:"relative"}),!1},colorPicker_init:function(t,i){n.getScript(i+"/sedo/markitup/farbtastic.js").done(function(){n("html").bind("click",function(){n("#miuAdvColorPicker").hide()}),n("#miuAdvColorPicker").bind("click",function(n){n.stopPropagation()}),n.farbtastic("#miuSelectColor").linkTo("#miuGetColor"),n("#miuSendColor").bind("click",function(){var i=n("#miuGetColor").val();return n("#miuAdvColorPicker").hide(),n.markItUp({openWith:"[color="+i+"]",closeWith:"[/color]",caretPositionIE:t.caretPosition,selectionIE:t.selection}),$form=n(this).closest("form"),n(this).closest("#miuAdvColorPicker").prependTo($form),!1})}).fail(function(){console.info("Error: Farbtastic was NOT Loaded");var f=n("#miuAdvColorPicker").attr("data-fail");return n.markItUp({openWith:"[color=#[!["+f+"]!]]",closeWith:"[/color]",caretPositionIE:t.caretPosition,selectionIE:t.selection}),!1})},linkManager:function(n){XenForo.MiuFramework._overlay_callbacks(XenForo.miuExtra,n,"linkManager_ontrigger","linkManager_onload"),XenForo.ajax("index.php?editor/miu-dialog",{dialog:"Url"},XenForo.MiuFramework._overlay_success)},linkManager_onload:function(n,t){$element_url=t.find(".miuLinkUrl"),$element_text=t.find(".miuLinkText"),XenForo.MiuFramework.isUrl(n.selection)?($element_url.val(n.selection),$element_text.focus()):n.selection.length>0?($element_text.val(n.selection),$element_url.focus()):$element_url.focus()},linkManager_ontrigger:function(t,i){if(!i.miulinkurl)return!1;i.miulinktext||(i.miulinktext=i.miulinkurl),n.markItUp({replaceWith:"[url="+i.miulinkurl+"]"+i.miulinktext+"[/url]",caretPositionIE:XenForo.MiuFramework.miu.caretPosition,selectionIE:XenForo.MiuFramework.miu.selection})},mediaManager:function(n){XenForo.MiuFramework._overlay_callbacks(XenForo.miuExtra,n,"mediaManager_ontrigger"),XenForo.ajax("index.php?editor/miu-dialog",{dialog:"Media"},XenForo.MiuFramework._overlay_success)},mediaManager_ontrigger:function(n,t){if(!t.miulinkmedia)return!1;XenForo.ajax("index.php?editor/media",{url:t.miulinkmedia},this.mediaManager_ajax);return},mediaManager_ajax:function(t){if(XenForo.hasResponseError(t))return!1;t.matchBbCode?n.markItUp({replaceWith:t.matchBbCode,caretPositionIE:XenForo.MiuFramework.miu.caretPosition,selectionIE:XenForo.MiuFramework.miu.selection}):t.noMatch&&alert(t.noMatch)},imgManager:function(n){XenForo.MiuFramework._overlay_callbacks(XenForo.miuExtra,n,"imgManager_ontrigger","imgManager_onload"),XenForo.ajax("index.php?editor/miu-dialog",{dialog:"Img"},XenForo.MiuFramework._overlay_success)},imgManager_onload:function(n,t){n.selection.length>0&&t.find(".miuLinkImg").val(n.selection)},imgManager_ontrigger:function(t,i){if(!i.miulinkimg)return!1;n.markItUp({replaceWith:"[img]"+i.miulinkimg+"[/img]",caretPositionIE:XenForo.MiuFramework.miu.caretPosition,selectionIE:XenForo.MiuFramework.miu.selection})},indent:function(n){var u,f,i,r,e,t;if(u=/(\[indent=)(\d{1,2})?(\])(.*?\[\/indent\])/gi,f=/(\[indent=)(\d{1,2})?(\])(.*?\[\/indent\])/i,i=n.selection,u.test(n.selection)){r=n.selection.match(u);for(e in r)t=f.exec(r[e]),t&&(t[2]++,i=i.replace(t[0],t[1]+t[2]+t[3]+t[4]));return i}return"[indent=1]"+i+"[/indent]"},outdent:function(n){var u,f,i,r,e,t;if(u=/(\[indent=)(\d{1,2})?(\])(.*?\[\/indent\])/gi,f=/(\[indent=)(\d{1,2})?(\])(.*?\[\/indent\])/i,i=n.selection,u.test(n.selection)){r=n.selection.match(u);for(e in r)t=f.exec(r[e]),t&&(t[2]--,i=t[2]==0?i.replace(t[0],t[1]+"1"+t[3]+t[4]):i.replace(t[0],t[1]+t[2]+t[3]+t[4]));return i}return!1}}})(jQuery);
