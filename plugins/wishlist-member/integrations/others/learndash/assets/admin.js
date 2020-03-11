WLM3ThirdPartyIntegration.learndash.fxn = {

	check_plugin : function() {
		var c = $('#thirdparty-provider-container-learndash');
		c.find('.plugin-status').html('<div class="text-warning"><p><em>Checking LearnDash LMS...</em></p></div>');
		$.post(
			WLM3VARS.ajaxurl,
			{
				action: 'wlm3_learndash_check_plugin',
			},
			function(result) {
				if ( result.status ) {
					c.removeClass('api-fail');
					c.find('.plugin-status').html('<div class="text-success"><p>' +result.message +'</p></div>');

					WLM3ThirdPartyIntegration.learndash.courses = result.courses;
					WLM3ThirdPartyIntegration.learndash.groups = result.groups;
					WLM3ThirdPartyIntegration.learndash.fxn.set_options();
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
		var courses = WLM3ThirdPartyIntegration.learndash.courses;
		var groups = WLM3ThirdPartyIntegration.learndash.groups;
		var levels = all_levels.__levels__ ? all_levels.__levels__ : [];

		var selects = $('select.learndash-groups-select');
		selects.empty();
		$.each( groups, function(index, group) {
			selects.append($('<option/>', {value : index, text : group}));
		});

		var selects = $('select.learndash-courses-select');
		selects.empty();
		$.each( courses, function(index, course) {
			selects.append($('<option/>', {value : index, text : course}));
		});

		var selects = $('select.learndash-levels-select');
		selects.empty();
		$.each( levels, function(index, lvl) {
			selects.append($('<option/>', {value : index, text : lvl.name}));
		});

		$('.modal-learndash-actions').set_form_data(WLM3ThirdPartyIntegration.learndash);
	}
}

integration_before_open['learndash'] = function(obj) {
	var fxn = this;
	obj = $(obj);
	var $me = $('#thirdparty-provider-container-learndash');

	fxn.save_keys = function(){
		WLM3ThirdPartyIntegration.learndash.fxn.check_plugin();
	};

	$me.off('click', '.save-keys', fxn.save_keys);
	$me.on('click', '.save-keys', fxn.save_keys);

	$me.addClass('api-fail');
	$me.transformers();
}

integration_after_open['learndash'] = function(obj) {
	var fxn = this;
	obj = $(obj);
	WLM3ThirdPartyIntegration.learndash.fxn.check_plugin();
}