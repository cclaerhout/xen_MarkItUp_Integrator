***********************************
* MarkItUp Integrator  v.2.1      *
* by Cédric CLAERHOUT             *
***********************************

**********************************
*      Addon Presentation        *
**********************************
This Addon will integrate markItUp! Editor to XenForo. MarkItUp is NOT a WySiWyG  (What you see is what you get) editor. It means if you put a text in bold, you will not see it in bold but with tags ([ b ] This is a bold text [ /b ]). This editor has been written using jQuery. Its author is the French Jay Salvat. Many scripts are using its editor because it is extremely "Lightweight, powerful and flexible". Here is the Editor website: http://markitup.jaysalvat.com/home/


#What's new in that version 2.0?
	All Javascript have been rewritten
		> The MarkItUp JS file has been modified to really manage multi-lines buttons (nothing to do with the previous version)
		> The MarkItUp JS file has also been modified to make it easier to use on mobile devices (for ie, menus don't open on hover but on click, easy to say, not necessary easy to do)
		> Some MarkItUp buttons are using the jQuery Tools (provided with XenForo) to manage overlay popup to enter Url, Media, Img links
		> The Color button is using the farbtastic javascript tool to select some customized colors - its javascript file will only load on demand
		> The media button is now available like on TinyMCE
		> The smilie button is using XenForo Smilie system
		> The MarkItUp integration is far more cleaner than version 1.x - it's using the XenForo JS system
		> The option to display first the MarkItUp editor, then TinyMCE is now a "real" option. Meaning it will load dynamically TinyMCE (not directly compatible with Internet Explorer 7/XenForo - problem of relative path)

	All templates and Css have been rewritten
		> Previous buttons are not compatible anymore
		> The editor display can be now customized with the styles properties... with many options - see screenshots

	Most of php have been rewritten
		> All the modifications (php+templates + JS) will only load when the editor template is called (again... easy to say, not necessary easy to do)
		> Many bugs have been fixed
		> New multi buttons import system
		
Developped and Tested on the following Browsers:
- Firefox
- Chrome
- Opera
- Safari/Dophin on IPAD
- IE7-IE9



#List of available buttons

	Bold,Center,Clean,Code,Colors,Fonts,Indent,Italic,Left,Link,Listbullet,Listitem,Listnumeric,Outdent,Picture,Quotes,Right,Size,Smilies,Strike,Underline,Media, Global_BiUs, Global_LcRj, Global_LIO, justify  & Keytouch

	> Global_BiUs: a menu button with inside the following options: Bold, Italic, Underline, Strike
	> Global_LcRj: a menu button with inside the following options: Left, Center, Right, Justify
	> Global_LIO: a menu button with inside the following options: Listbullet, Listnumeric, Listitem; Indent; Outdent
	> Justify: full justify text-align; you will need to create a bbcode to make this one work

Notes:
	- Those defaults buttons can be adapted per langage (the French buttons icons are for example available)
	  To do this, you just have to modify the original psd icons file (psd files are included) then save the new image with png format
	  Then just modify the following phrase: "miu_icon_default" to put your file name (without extension) in your translation language




**********************************
*    Installation/Update         *
**********************************
IMPORTANT: To be able to use this addon, you need to install first the Template Modification system of Guiltar
Source: http://xenforo.com/community/resources/template-modification-system-tms.293/

This addon is compatible with the Auto installer fo Chris. If you don't use it, just upload the files in your forum directory and import the addon xml file
Source: http://xenforo.com/community/resources/automatic-add-on-installer-and-upgrader.960

You will have to import buttons first, then configure the addon with the MarkItUp Button Manager before to see it working


**********************************
*        Configuration           *
**********************************
Configure addon in 
> ADMIN->HOME->MarkItUp Editor (global options)
> ADMIN->Appearance->Style Properties->MarkItUp Editor - Public View

NOTE: 	Don't Change any visual options in ADMIN->Appearance->Style Properties->Markitup Xenforo Integration (ADMIN VIEW)
	Those visual properties are needed for the Editor Visual Management Tool


**********************************
*           Credits              *
**********************************
> Jay Salvat for his MarkItup Editor - source: http://markitup.jaysalvat.com/home/
> Mark James for his great icons "Silk icon set 1.3" - source: http://www.famfamfam.com/lab/icons/silk/
> Steven Wittens for his color wheel selector 
> Jack Moore for his autosize plugin - source: http://www.jacklmoore.com/autosize
> Jake Bunce Infis for his help on XenForo


**********************************
*           Licenses              *
**********************************
The MarkItup Editor Editor is under the MIT and GPL licenses, which means it is "free of charge" and can be used "without restriction". 
My Integration to XenForo is under the Creative Commons BY 3.0 license (http://creativecommons.org/licenses/by/3.0/).