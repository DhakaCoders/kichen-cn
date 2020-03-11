WLM3ThirdPartyIntegration.wpcourseware.fxn = {

	check_plugin : function() {
		var c = $('#thirdparty-provider-container-wpcourseware');
		c.find('.plugin-status').html('<div class="text-warning"><p><em>Checking WP Courseware plugin...</em></p></div>');
		$.post(
			WLM3VARS.ajaxurl,
			{
				action: 'wlm3_wpcourseware_check_plugin',
			},
			function(result) {
				if ( result.status ) {
					c.removeClass('api-fail');
					c.find('.plugin-status').html('<div class="text-success"><p>' +result.message +'</p></div>');

					WLM3ThirdPartyIntegration.wpcourseware.courses = result.courses;
					WLM3ThirdPartyIntegration.wpcourseware.fxn.set_options();
					c.find('.plugin-status').hide();
				} else {
					c.addClass('api-fail');
					c.find('.plugin-status').html('<div class="text-danger"><p>' + result.message + '</p></div>');
				}
			},
			'json'
		);
	},
	set_options : function() {
		var courses = WLM3ThirdPartyIntegration.wpcourseware.courses;
		var levels = all_levels.__levels__ ? all_levels.__levels__ : [];

		var selects = $('select.wpcourseware-courses-select');
		selects.empty();
		$.each( courses, function(index, course) {
			selects.append($('<option/>', {value : index, text : course}));
		});

		var selects = $('select.wpcourseware-levels-select');
		selects.empty();
		$.each( levels, function(index, lvl) {
			selects.append($('<option/>', {value : index, text : lvl.name}));
		});

		$('.modal-wpcourseware-actions').set_form_data(WLM3ThirdPartyIntegration.wpcourseware);
	}
}

integration_before_open['wpcourseware'] = function(obj) {
	var fxn = this;
	obj = $(obj);
	var $me = $('#thirdparty-provider-container-wpcourseware');

	fxn.save_keys = function(){
		WLM3ThirdPartyIntegration.wpcourseware.fxn.check_plugin();
	};

	$me.off('click', '.save-keys', fxn.save_keys);
	$me.on('click', '.save-keys', fxn.save_keys);

	$me.addClass('api-fail');
	$me.transformers();
}

integration_after_open['wpcourseware'] = function(obj) {
	var fxn = this;
	obj = $(obj);
	WLM3ThirdPartyIntegration.wpcourseware.fxn.check_plugin();
}