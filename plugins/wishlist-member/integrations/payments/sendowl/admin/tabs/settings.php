<form>
	<div class="row">
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'API Endpoint', 'wishlist-member' ); ?>',
				name : '',
				column : 'col-md-12',
				value : '<?php echo admin_url() ."?/wlmapi/2.0/"; ?>',
				readonly : 'readonly',
				class : 'copyable',
			}
		</template>
		<template class="wlm3-form-group">
			{
				label : '<?php _e( 'API Key', 'wishlist-member' ); ?>',
				name : '',
				column : 'col-md-12',
				value : '<?php echo $this->GetOption('WLMAPIKey'); ?>',
				readonly : 'readonly',
				class : 'copyable',
				tooltip : '<?php _e( 'Note: The API Key can be changed if needed in WishList Member in the following section: Advanced Options > API.', 'wishlist-member' ); ?>',
				tooltip_size : 'md',
			}
		</template>
	</div>
</form>
