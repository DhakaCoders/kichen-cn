function wlm3_random_password() {
	var chars = ['0123456789', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz', '~!@#$%^&*()_-+={[}]<>?'];
	var randomstring = '';
	for(var c = 0; c < chars.length; c++) {
		for (var i=0; i<5; i++) {
			var rnum = Math.floor(Math.random() * Math.floor(chars[c].length)/5);
			randomstring += chars[c].substring(rnum,rnum+1);
		}	
	}
	return randomstring.split('').sort(function(){return 0.5-Math.random()}).join('');	
}

function wlm3_password_size(hash) {
	var $buttons = jQuery('#wlm3-password-generator-buttons' + hash);
	$buttons.show();
	var w = $buttons.width() + 10;
	jQuery('#wlm3-password-field' + hash).css('width', 'calc(100% - ' + w + 'px');
	jQuery('#wlm3-password-generator-strength' + hash).show().css('width', 'calc(100% - ' + w + 'px');

}

function wlm3_password_strength(el, hash) {
	wlm3_password_size(hash);
	var strengthResult = jQuery('#wlm3-password-generator-strength' + hash);
	strengthResult.removeClass('bad good strong short');

	if(wlm3_password_strength_check(el.value)) {
		strengthResult.addClass('strong').html( 'Strong' );
	} else {
		strengthResult.addClass('bad').html( 'Weak' );
	}
}

function wlm3_generate_password(hash) {
	var fld = jQuery('#wlm3-password-field' + hash);
	fld.show();
	fld.val(wlm3_random_password());
	fld.attr('type', 'text');
	jQuery('#wlm3-password-generator-toggle' + hash).text('Hide');
	jQuery('#wlm3-password-generator-buttons' + hash).show();

	jQuery('#wlm3-password-generator-button' + hash).hide();
	wlm3_password_size(hash);
	fld.trigger('keyup');
}

function wlm3_generate_password_toggle(el, hash) {
	var fld = jQuery('#wlm3-password-field' + hash)[0];
	if(fld.type == 'password') {
		fld.type = 'text';
		el.innerText = jQuery(el).data('hide');
	} else {
		fld.type = 'password';
		el.innerText = jQuery(el).data('show');
	}

	var w = jQuery('#wlm3-password-generator-buttons' + hash).width() + 10;
	wlm3_password_size(hash);
}

function wlm3_generate_password_hide(hash) {
	var fld = jQuery('#wlm3-password-field' + hash);
	fld.hide();
	fld.val('');
	jQuery('#wlm3-password-generator-buttons' + hash).hide();
	jQuery('#wlm3-password-generator-button' + hash).show();
	jQuery('#wlm3-password-generator-strength' + hash).hide();
}

function wlm3_password_strength_check(password) {
		if(password.length < 12) return false;
		if(!password.match(/[A-Z]/g)) return false;
		if(!password.match(/[a-z]/g)) return false;
		if(!password.match(/[0-9]/g)) return false;
		if(!password.match(/[`~!@#$%^&*()-_=+\[{\]}|;:",<\.>\'\?]/g)) return false;
		return true;
}

function wlm3_register_disable_prefill() {
	if(jQuery('input[name="mergewith"]').val() == null) {
		return;
	} 
	if (jQuery('input[name="orig_firstname"]').val() != "") {
	  jQuery('input[name="firstname"]').attr("readonly", "readonly");
	}
	if (jQuery('input[name="orig_lastname"]').val() != "") {
	 jQuery('input[name="lastname"]').attr("readonly", "readonly");
	}
	if (jQuery('input[name="orig_email"]').val() != "") {
	 jQuery('input[name="email"]').attr("readonly", "readonly");
	}
}

jQuery(function($) {
	var fancys = jQuery('a.wlm3-tos-fancybox')
	if(fancys.length) {
		fancys.fancybox({
			baseClass : 'wlm3-fancybox'
		});
	}

	// front end media uploader
	var wlm3_file_frame; // wp.media wlm3_file_frame
	
	$( '.wlm3-frontend-media-uploader' ).on( 'click', function( event ) {
		event.preventDefault();
		var $upload_button = $( this );
		var $container = $upload_button.closest('.wlm3-profile-photo-container');

		// reuse wlm3_file_frame if it's already et
		if ( wlm3_file_frame ) {
			wlm3_file_frame.open();
			return;
		} 

		wlm3_file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
			button: {
				text: $( this ).data( 'uploader_button_text' ),
			},
			multiple: false
		});

		wlm3_file_frame.on( 'select', function() {
			attachment = wlm3_file_frame.state().get('selection').first().toJSON();
			
			// update the values
			$container.find( 'input[type="hidden"]' ).val( attachment.url );
			$container.find( 'img' ).attr( 'src', attachment.url );
		});

		wlm3_file_frame.open();
	});

	$( '.wlm3-frontend-media-clear' ).on( 'click', function ( event ) {
		event.preventDefault();
		var $container = $( this ).closest( '.wlm3-profile-photo-container' );
		$container.find( 'input[type="hidden"]' ).val( '' );
		$container.find( 'img' ).attr( 'src', WLM3VARS.pluginurl + '/assets/images/grey.png' );

	} );
});

