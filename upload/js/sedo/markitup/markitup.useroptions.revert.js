!function($, window, document, _undefined)
{
	XenForo.miu_user_rte_revert = function()
	{
		if(!$('#ctrl_enable_rte').is(':checked')) {
			$('#ctrl_miu_rte_reverse').attr('checked', false).attr("disabled", 'disabled');
		}
		else {
			$('#ctrl_miu_rte_reverse').attr("disabled", false);
		}
		
		$('#ctrl_enable_rte').click(function() {
			XenForo.miu_user_rte_revert();
		});
	};
	
	XenForo.register('#ctrl_miu_rte_reverse', 'XenForo.miu_user_rte_revert');
}
(jQuery, this, document);