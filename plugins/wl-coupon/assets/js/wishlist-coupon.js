jQuery(function($) {
	function get_coupon_id(id) {
		var parts = id.split('-');
		return parts[parts.length - 1];
	}


	function process_coupon(id, callback) {
		var data = {
			'action': 'wishlist_coupon_apply',
			'id': id,
			'code': $('#wishlist-coupon-code-'+id).val()
		};

		$.post(ajaxurl, data, function(res) {
			var res = JSON.parse(res);
			callback(res, data);
		});
	}
	$('.wishlist-coupon-btn-apply').on('click', function() {
		var id = get_coupon_id($(this).attr('id'));

		$('#wishlist-coupon-alert-'+id).html('');
		$('#wishlist-coupon-success-'+id).html('');

		process_coupon(id, function(res, data) {
			var info_el = $('#wishlist-coupon-alert-'+id);
			if(res.status == true) {
				info_el = $('#wishlist-coupon-success-'+id);
			}
			info_el.html(res.msg);
		});

	});
	$('.wishlist-coupon-btn-pay').on('click', function() {
		var id = get_coupon_id($(this).attr('id'));

		$('#wishlist-coupon-alert-'+id).html('');
		$('#wishlist-coupon-success-'+id).html('');
		process_coupon(id, function(res, data) {
			$(document).attr('location', wishlist_coupon_redirect_to + '&id=' + id + '&code=' + data.code );
		});
	});
});