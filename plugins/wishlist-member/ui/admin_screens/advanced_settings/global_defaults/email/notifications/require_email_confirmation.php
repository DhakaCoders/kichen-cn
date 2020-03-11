<div class="requireemailconfirmation -holder">
	<div class="row">
		<template class="wlm3-form-group">{
			addon_left: 'Subject',
			group_class: '-label-addon mb-2',
			type: 'text',
			name: 'require_email_confirmation_subject',
			column : 'col-md-12',
			class: 'email-subject'
		}</template>
		<template class="wlm3-form-group">{
			name: 'require_email_confirmation_message',
			class : 'richtextx',
			type: 'textarea',
			column : 'col-md-12',
			group_class : 'mb-2'
		}</template>
		<div class="col-md-12">
			<button class="btn -default -condensed email-reset-button" data-target="require_email_confirmation">Reset to Original Message</button>
			<template class="wlm3-form-group">{
				type : 'select',
				column : 'col-md-5 pull-right no-padding no-margin',
				'data-placeholder' : '<?php _e( 'Insert Merge Codes', 'wishlist-member' ); ?>',
				group_class : 'shortcode_inserter mb-0',
				style : 'width: 100%',
				options : get_merge_codes([{value : '[confirmurl]', text : 'Confirmation URL'}, {value : '[password]', text : 'Password'}]),
				grouped: true,
				class : 'insert_text_at_caret',
				'data-target' : '[name=require_email_confirmation_message]'
			}</template>
		</div>

	</div>
</div>
