<div class="requireemailconfirmation -holder">
	<div class="row">
		<div class="col-md-12 ">
			<div class="pull-left form-inline">
				<template class="wlm3-form-group">{
					type : 'text',
					name: 'require_email_confirmation_start',
					addon_left : 'First Sent After',
					addon_right: 'Hours',
					style : 'width: 60px;',
					class : 'text-center',
				}</template>
				<template class="wlm3-form-group">{
					addon_left : 'Send Every',
					name: 'require_email_confirmation_send_every',
					addon_right: 'Hours',
					type : 'text',
					style : 'width: 60px;',
					class : 'text-center',
				}</template>
				<template class="wlm3-form-group">{
					type : 'text',
					name: 'require_email_confirmation_howmany',
					addon_left : 'Total Sent',
					style : 'width: 60px;',
					class : 'text-center',
				}</template>
			</div>
			<br style="clear:both"><hr style="margin-top:0">
		</div>
	</div>
	<div class="row">
		<div class="col-auto mb-2">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Use Global Default Sender Info', 'wishlist-member' ); ?>',
					name  : 'email_confirmation_default_sender',
					value : '1',
					uncheck_value : '0',
					type  : 'checkbox',
					class : 'modal-input -sender-default-toggle',
				}
			</template>
		</div>
	</div>
	<div class="row level-sender-info" id="email_confirmation_default_sender">
		<template class="wlm3-form-group">{
			addon_left: 'Sender Name',
			group_class: '-label-addon mb-2',
			type: 'text',
			name: 'require_email_confirmation_sender_name',
			column : 'col-md-6'
		}</template>
		<template class="wlm3-form-group">{
			addon_left: 'Sender Email',
			group_class: '-label-addon mb-2',
			type: 'text',
			name: 'require_email_confirmation_sender_email',
			column : 'col-md-6'
		}</template>
	</div>
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
			class : 'levels-richtext',
			type: 'textarea',
			column : 'col-md-12',
			group_class : 'mb-2'
		}</template>
		<div class="col-md-12">
			<button class="btn -default -condensed email-reset-button" data-target="require_email_confirmation">Reset to Global Default Message</button>
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
