$(function() {
	$('body').off('.wlm3-idevaffiliate');
	$('body').on('change.wlm3-idevaffiliate', 'input.-commission-type', function () {
		$('.-commission-fixed, .-commission-idev').hide();
		var idev = $('.-commission-idev-' + $(this).data('level'));
		var fixed = $('.-commission-fixed-' + $(this).data('level'));
		if($(this).is(':checked')) {
			idev.hide();
			fixed.show();
		} else {
			idev.show();
			fixed.hide();
		}
	});
	$('input.-commission-type').trigger('change.wlm3-idevaffiliate');
	
	$('body').on('change.wlm3-idevaffiliate', 'input.-numeric', function() {
		if(!this.value.trim()) return;
		var v = (parseFloat($(this).val()) || 0).toLocaleString('en-US', {minimumFractionDigits : 2});
		$(this).val(v);
	});
});