<div class="content-wrapper">
	<div class="row">
		<?php $option_val = $this->GetOption('login_limit_notify') ?>
		<div class="col-md-6">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Notify Admin of Exceeded Logins', 'wishlist-member' ); ?>',
					name  : 'login_limit_notify',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip: '<?php _e( 'An email will be sent to the site Admin if a Member exceeds the Daily Login Limit if this setting is enabled.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('enable_login_redirect_override') ?>
		<div class="col-md-7">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Allow WishList Member to Handle Login Redirect', 'wishlist-member' ); ?>',
					name  : 'enable_login_redirect_override',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip: '<?php _e( 'WishList Member will override all Login Redirects from other plugins, themes, shortcodes, etc. if this setting is enabled.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('enable_logout_redirect_override') ?>
		<div class="col-md-7">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Allow WishList Member to Handle Logout Redirect', 'wishlist-member' ); ?>',
					name  : 'enable_logout_redirect_override',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip: '<?php _e( 'WishList Member will override all Logout Redirects from other plugins, themes, shortcodes, etc. if this setting is enabled.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
			<br>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<label for="">
				Default Login Limit:
				<?php $this->tooltip(__('This is the default number of times a user can login from a different IP address in a single day. <br><br>To permit an unlimited number of logins per user from different IP address simply leave the field blank.<br><br>Note: Daily Login Limits can be set for individual Members in the Members > Manage > Username > Advanced section.', 'wishlist-member'), "lg"); ?>
			</label>
			<div class="row">
				<div class="col-sm-6 col-md-3 col-xxxl-2 col-xxl-3 no-margin">
					<template class="wlm3-form-group">
						{
							name  : 'login_limit',
							type  : 'number',
							min   : '0',
							value : '<?php echo $this->GetOption('login_limit') + 0; ?>',
							addon_right : 'IPs per day',
							group_class : 'no-margin',
							'data-initial' : '<?php echo $this->GetOption('login_limit') + 0; ?>',
							class : 'text-center login-limit-apply',
							help_block : '<?php _e( 'Set the field to 0 to disable.', 'wishlist-member' ); ?>',
						}
					</template>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-12">
			<label for="">
				Login Limit Message
				<?php $this->tooltip(__('The Login Limit Message will appear to Members on the login page if they reach the set Daily Login Limit.', 'wishlist-member')); ?>
			</label>
			<div class="row">
				<div class="col-md-6 no-margin">
					<template class="wlm3-form-group">
						{
							name  : 'login_limit_error',
							value : '<?php echo $this->GetOption('login_limit_error'); ?>',
							group_class : 'no-margin',
							'data-initial' : '<?php echo $this->GetOption('login_limit_error'); ?>',
							class : 'login-limit-error-apply',
						}
					</template>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<?php $option_val = $this->GetOption('auto_login_after_confirm') ?>
		<div class="col-md-12">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Auto Login Member After Clicking Confirmation Link', 'wishlist-member' ); ?>',
					name  : 'auto_login_after_confirm',
					value : '1',
					checked_value : '<?php echo $option_val; ?>',
					uncheck_value : '0',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip: '<?php _e( 'Members will be automatically logged in after clicking the confirmation link if this setting is enabled.', 'wishlist-member' ); ?>'
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Disable WordPress Admin Bar for Members when Logged In', 'wishlist-member' ); ?>',
					name  : 'show_wp_admin_bar',
					value : '0',
					uncheck_value : '1',
					checked_value : '<?php echo $this->GetOption('show_wp_admin_bar'); ?>',
					class : 'wlm_toggle-switch notification-switch',
					type  : 'checkbox',
					tooltip: '<?php _e( 'The WordPress Admin bar will be hidden from logged in Members if this setting is enabled.', 'wishlist-member' ); ?>',
				}
			</template>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save" />
		</div>
	</div>
</div>