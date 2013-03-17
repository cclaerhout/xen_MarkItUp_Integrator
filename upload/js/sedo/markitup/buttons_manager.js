!function($, window, document, _undefined)
{
	XenForo.miubuttonManager = 
	{
		init: function(){
	 		//Make list sortable
		 	XenForo.miubuttonManager.MakeListSortable();
			XenForo.miubuttonManager.SaveUpdates(); //Prevent blank config on first install
			XenForo.miubuttonManager.configtype = $('#configtype').val();
	
		 	//Add new line function
		 	$('#AddNewLine').click(function() {

		 		var id = $('#container_select > ul').size();
	
				if(id > 5){
					alert("If you use more than 6 lines, you will need to create new background images for the editor")
				}
	
				$('#list_'+XenForo.miubuttonManager.configtype+'_'+id).after(
					'<div class="deleteme"><span>X</span></div><ul id="list_'+XenForo.miubuttonManager.configtype+'_'+(id+1)+'" class="selection connectedSortable connectedTools ui-sortable"></ul>'
				);
	
				XenForo.miubuttonManager.MakeListSortable();
		 	});
	
			//Delete line function (and put back button in buttons list)
			$('#container_select').delegate('.deleteme', 'click', function(){
				$(this).next('ul').children().appendTo('#ButtonsList');
				$(this).next('ul').remove();
				$(this).remove();
				XenForo.miubuttonManager.UpdateID();
				XenForo.miubuttonManager.SaveUpdates();
			});
	
			//Create a new separator
		 	$('#CreateSeparator').click(function() {
				$('#ButtonsList').append(
					'<li id="button_separator" class="separator"><span>|</span></li>'
				);
	
	 	 	});
 		},
		MakeListSortable: function() {
			$( ".connectedSortable" ).sortable({
				update: XenForo.miubuttonManager.SaveUpdates,
				helper: 'original',
				connectWith: ".connectedSortable"
			}).disableSelection();	
		},
	
		UpdateID: function() {
			var id = 1;
			$('#container_select ul').each(function(index) {
				$(this).attr("id","list_"+XenForo.miubuttonManager.configtype+"_"+id);
				id++;
			});
		},
		SaveUpdates: function ( event, ui ) {
			var update;
			$('#container_select ul').each(function(index) {
				if(update){
					update = update + ',#,' + $(this).sortable('toArray');
				}
				else{
					update = $(this).sortable('toArray');
				}
			});	
		
			$('#target').val(update);
		},
      		ajaxResponse: function($form)
      		{
      			$form.bind('AutoValidationComplete', function(e)
      			{
      				if (e.ajaxData.templateHtml)
      				{
      					e.preventDefault();
      
      						new XenForo.ExtLoader(e.ajaxData, function()
      						{
      							$(e.ajaxData.templateHtml).xfInsert('replaceAll', '#AjaxResponse');
      						});
      				}
      			});
      		}
	}

	XenForo.register('body', 'XenForo.miubuttonManager.init');
	XenForo.register('form.MarkitupButtons', 'XenForo.miubuttonManager.ajaxResponse');

}
(jQuery, this, document);

