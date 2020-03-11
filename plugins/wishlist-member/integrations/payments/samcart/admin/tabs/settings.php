<form>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'Blog URL', 'wishlist-member' ); ?>',
				name : '',
				column : 'col-md-12',
				help_block : '<?php _e( 'Copy the Blog URL and paste it into SamCart in the following section: <strong>Settings > Integrations > New Integration > WishList Member</strong>.', 'wishlist-member' ); ?>',
				value : '<?php echo admin_url(); ?>',
				readonly : 'readonly',
				class : 'copyable',
			}
		</template>
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'API Key', 'wishlist-member' ); ?>',
				name : '',
				column : 'col-md-12',
				help_block : '<?php _e( 'Copy the API Key and paste it into SamCart in the following section: <strong>Settings > Integrations > New Integration > WishList Member</strong>.', 'wishlist-member' ); ?>',
				value : '<?php echo $this->GetOption('WLMAPIKey'); ?>',
				readonly : 'readonly',
				class : 'copyable',
				tooltip : '<?php _e( 'Note: The API Key can be changed if needed in WishList Member in the following section: Advanced Options > API.', 'wishlist-member' ); ?>',
				tooltip_size : 'md',
			}
		</template>
	</div>
</form>
