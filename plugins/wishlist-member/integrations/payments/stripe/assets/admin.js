WLM3ThirdPartyIntegration.stripe.fxn = {
	test_keys : function(x) {
		var c = $('#thirdparty-provider-container-stripe'); 
		c.find('.api-status').html('<div class="text-warning"><p><em>Checking...</em></p></div>');
		var b = c.find('.save-keys').first();
		if(x.save) {
			b.text(b.data('saving'));
		}
		b.addClass('disabled');
		$.post(
			WLM3VARS.ajaxurl,
			{
				action: 'wlm3_stripe_test_keys',
				data: x
			},
			function(result) {
				if(result.status) {
					c.removeClass('api-fail');
					c.find('.api-status').html('<div class="text-success"><p>' + get_integration_api_message(1, 'Stripe') + '</p></div>');
					WLM3ThirdPartyIntegration.stripe = $.extend( {}, WLM3ThirdPartyIntegration.stripe, result.data );
					var plans = result.data.plan_options;
					plans.unshift({id : '', text : ''});
					$('select[name^=stripeconnections]').select2({data : plans}, true);
					$('#thirdparty-provider-container-stripe').set_form_data(WLM3ThirdPartyIntegration.stripe);
					$('select[name^=stripeconnections]').trigger('change');
				} else {
					c.addClass('api-fail');
					var msg = (x.stripeapikey.trim() && x.stripepublishablekey.trim()) ? get_integration_api_message(2, result.message) : get_integration_api_message(3);
					c.find('.api-status').html('<div class="text-danger"><p>' + msg + '</p></div>');
				}
				if(x.save) {
					b.text(b.data('saved'));
				}
				b.removeClass('disabled');
			},
			'json'
		);
	},
	get_keys : function(obj) {
		var $me = $('#thirdparty-provider-container-stripe');
// 		if(!$me.hasClass('api-fail')) {
// 			obj.find('.-integration-keys :input').val('');
// 		}
		var x = {};
		obj.find('.-integration-keys :input').each(function(i,v) {
			x[v.name] = v.value;
		});
		return x;
	}
}
integration_before_open['stripe'] = function(obj) {
	var fxn = this;
	obj = $(obj);
	var $me = $('#thirdparty-provider-container-stripe');

	fxn.save_keys = function(){
		var x = $.extend({save : true},WLM3ThirdPartyIntegration.stripe.fxn.get_keys(obj));
		WLM3ThirdPartyIntegration.stripe.fxn.test_keys(x);
	};

	$me.on('click', '.save-keys', fxn.save_keys);

	$('body').on('change', '.stripe-plan-toggle', function() {
		var target = $($(this).data('target'));
		if($(this).is(':checked')) {
			target.show();
		} else {
			target.hide();
		}
	});

	$('#stripe-products-table').empty();
	$.each(all_levels, function(k, v) {
		if(!Object.keys(v).length) return true;
		var data = {
			type : k,
			label : post_types[k].labels.name,
			levels : v
		}
		var tmpl = _.template($('script#stripe-products-template').html(), {variable: 'data'});
		var html = tmpl(data);
		$('#stripe-products-table').append(html);
	});

	$me.addClass('api-fail'); 
}
integration_after_open['stripe'] = function(obj) {
	var fxn = this;
	obj = $(obj);

	$('#stripe-products-table .stripe-plan-toggle').trigger('change');

	WLM3ThirdPartyIntegration.stripe.fxn.test_keys(WLM3ThirdPartyIntegration.stripe.fxn.get_keys(obj));


}