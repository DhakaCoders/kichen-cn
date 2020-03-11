<div class="row">
	<div class="col-auto mb-4"><?php echo $config_button; ?></div>
	<?php echo $api_status_markup; ?>		
</div>
<div class="row api-required">
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'Account', 'wishlist-member' ); ?>',
			type : 'select',
			name : 'account',
			column : 'col-md-6',
			style : 'width: 100%',
			group_class : 'no-margin'
		}
	</template>

	<div class="col-md-12">
		<hr>
		<h3><?php _e( 'WishList Member API Information', 'wishlist-member' ); ?></h3>
		<br>
	</div>
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'WordPress URL', 'wishlist-member' ); ?>',
			name : '',
			column : 'col-md-6',
			value : '<?php echo admin_url(); ?>',
			class : 'copyable',
			readonly : 'readonly',
		}
	</template>
	<template class="wlm3-form-group">
		{
			label : '<?php _e( 'API Key', 'wishlist-member' ); ?>',
			name : '',
			column : 'col-md-6',
			value : '<?php echo $this->GetOption('WLMAPIKey'); ?>',
			class : 'copyable',
			readonly : 'readonly',
		}
	</template>

</div>