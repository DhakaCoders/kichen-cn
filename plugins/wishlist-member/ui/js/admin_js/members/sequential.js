jQuery(function($){
	$('.wlm-datetimepicker').daterangepicker({
		opens: 'center',
		singleDatePicker: true,
		timePicker: true,
		timePickerIncrement: 15,
		showCustomRangeLabel: false,
		startDate: moment(),
		buttonClasses: "btn -default",
		applyClass: "-condensed -success",
		cancelClass: "-condensed -link",
		autoUpdateInput: false,
		locale: {
			format: "MM/DD/YYYY hh:mm a"
		}
	});
	$('.wlm-datetimepicker').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format("MM/DD/YYYY hh:mm a"));
	});

	$('.save-settings').click(save_settings);
	$('.toggle-radio-sched').click(toggle_radio_sched);
	$('.upgrade-method').change(toggle_radio_method);
});

var toggle_radio_sched = function() {
	var tr = $(this).closest('tr');
	var value = $(this).val();
	tr.find('.sched-hidden').val(value);
	tr.removeClass('schedule-ondate schedule-after');
	tr.addClass('schedule-' + value.toLowerCase());
}

var toggle_radio_method = function() {
	var tr = $(this).closest('tr');
	var value = $(this).val();
	tr.removeClass('method-add method-move method-remove method-inactive');
	tr.addClass('method-' + value.toLowerCase());
}

var save_settings = function() {
	$this_button = $(this);
	$this_button.closest(".row").save_settings({
	    on_init: function( $me, $data) {
	    	$this_button.disable_button({disable:true, icon:"update"});
	    },
	    on_success: function( $me, $result) {
	    	if ( $result.success ) {
	    		$(".wlm-message-holder").show_message({message:wlm.translate( 'Sequential Upgrade settings saved.' ), type:$result.msg_type, icon:$result.msg_type});
	    	} else {
	    		$(".wlm-message-holder").show_message({message:$result.msg, type:$result.msg_type, icon:$result.msg_type});
	    	}
	    },
	    on_fail: function( $me, $data) {
	    	alert(WLM3VARS.request_failed);
	    },
	    on_error: function( $me, $error_fields) {
	    	$.each( $error_fields, function( key, obj ) {
  				obj.parent().addClass('has-error');
			});
	    	$this_button.disable_button( {disable:false, icon:"save"} );
	    },
	    on_done: function( $me, $data) {
	    	$this_button.disable_button( {disable:false, icon:"save"} );
	    }
	});
	return false;
}