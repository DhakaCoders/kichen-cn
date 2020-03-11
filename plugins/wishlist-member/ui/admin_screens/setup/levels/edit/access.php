<div role="tabpanel" class="tab-pane active" id="" data-id="levels_access">
	<div class="content-wrapper">
		
		<div id="expire_options" class="save-section">
			<input type="hidden" name="noexpire" />
			<div class="row expire_settings" data-initial="">
				<div class="col-md-12">
					<label class="mb-3"><?php _e( 'Expiration Options', 'wishlist-member' ); ?></label>
					<?php $this->tooltip(__('<p>Ongoing: An ongoing membership will give access with no specific expiration. A member\'s access will be cancelled if a payment tied to an integration fails to process successfully.</p><p>Fixed Term: When using a fixed term expiration a level can be scheduled to automatically expire after a certain amount of time. It can be a specified number of Days, Weeks, Months or Years.</p><p>Specific Date: When using a Specific Date expiration a level can be scheduled to automatically expire on a specific date. A date is chosen from a calendar and all members will expire on the same date.</p>', 'wishlist-member'), 'xxl'); ?>
				</div>
				<template class="wlm3-form-group">
					{
						column : 'col-md-3 col-sm-4 col-xs-6',
						name  : 'expire_option',
						value : '0',
						type  : 'select',
						style : 'width: 100%',
						options : [
							{value : 0, text : 'Ongoing'},
							{value : 1, text : 'Fixed Term'},
							{value : 2, text : 'Specific Date'},
						],
					}
				</template>
				<div class="col-md-3 col-sm-4 col-xs-6 expire_option expire_fixed_term" style="display:none">
					<div class="form-inline -combo-form">
						<div>
							<label class="sr-only" for=""><?php _e( 'Fixed Term','wishlist-member' ); ?></label>
							<div class="input-group">
								<input type="number" style="width: 35%" min="1" name="expire" class="form-control text-center">
								<select class="form-control wlm-select" name="calendar" style="width: 65%;">
									<option value="Days"><?php _e( 'Day(s)','wishlist-member' ); ?></option>
									<option value="Weeks"><?php _e( 'Week(s)','wishlist-member' ); ?></option>
									<option value="Months"><?php _e( 'Month(s)','wishlist-member' ); ?></option>
									<option value="Years"><?php _e( 'Year(s)','wishlist-member' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xxxl-2 col-md-3 col-sm-4 col-xs-6 expire_option expire_specific_date" style="display:none">
					<div class="date-ranger">
						<label class="sr-only" for=""><?php _e( 'Specific Date', 'wishlist-member' ); ?></label>
						<div class="date-ranger-container">
							<input id="DateRangePicker" type="text" name="expire_date" class="form-control" placeholder="" style="max-width: 250px">
							<i class="wlm-icons">date_range</i>
						</div>
					</div>
				</div>
				<div class="col-md-auto col-sm-4 expire_notification" style="display:none">
					<button data-toggle="modal" data-target="#email-notification-settings" class="btn -primary -condensed" data-notif-setting="expiring" data-notif-title="Expiring Email Notifications">
						<i class="wlm-icons">settings</i>
						<span class="text"><?php _e( 'Edit Notifications', 'wishlist-member' ); ?></span>
					</button>
				</div>
				<div class="col-md-auto expire_apply" style="display:none">
					<button class="btn -success -condensed"><?php _e( 'Apply', 'wishlist-member' ); ?></button>
					<button class="btn -bare -condensed"><?php _e( 'Cancel', 'wishlist-member' ); ?></button>
				</div>
			</div>
			<br>
		</div>
		<div class="row">
			<div class="col-xxxl-4 col-md-5">
				<label><?php _e( 'Access To', 'wishlist-member' ); ?></label>
				<br>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'All Posts', 'wishlist-member' ); ?>',
						name  : 'allposts',
						value : 'on',
						uncheck_value : '',
						type  : 'toggle-switch',
						column : 'col-md-12 no-padding'
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'All Pages', 'wishlist-member' ); ?>',
						name  : 'allpages',
						value : 'on',
						uncheck_value : '',
						type  : 'toggle-switch',
						column : 'col-md-12 no-padding'
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'All Comments', 'wishlist-member' ); ?>',
						name  : 'allcomments',
						value : 'on',
						uncheck_value : '',
						type  : 'toggle-switch',
						column : 'col-md-12 no-padding'
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'All Categories', 'wishlist-member' ); ?>',
						name  : 'allcategories',
						value : 'on',
						uncheck_value : '',
						type  : 'toggle-switch',
						column : 'col-md-12 no-padding'
					}
				</template>
			</div>
			<div class="col-md-7" id="levels_addremove_from">
				<template class="wlm3-form-group">
					{
						group_class : 'no-margin',
						label : '<?php _e( 'Remove From', 'wishlist-member' ); ?>',
						name  : 'removeFromLevel',
						value : '',
						type  : 'select',
						style : 'width: 100%',
						multiple : 1,
						column : 'col-md-12 no-padding',
						tooltip : '<?php _e( 'This option will automatically remove a member from a selected level when they are added to the current level. For example: remove from a free level when upgrading to a paid level.', 'wishlist-member' ); ?>'
					}
				</template>
				<div class="col-md-12"><br></div>
				<template class="wlm3-form-group">
					{
						group_class : 'no-margin',
						label : '<?php _e( 'Add To', 'wishlist-member' ); ?>',
						name  : 'addToLevel',
						value : '',
						type  : 'select',
						style : 'width: 100%',
						multiple : 1,
						column : 'col-md-12 no-padding',
						tooltip : '<?php _e( 'This option will automatically add a member to a selected level when they are added to the current level. For example: add to a bonus level when registering for a paid level.', 'wishlist-member' ); ?>'
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Inherit Level Status', 'wishlist-member' ); ?>',
						name  : 'inheritparent',
						value : '1',
						uncheck_value : '',
						type  : 'toggle-switch',
						tooltip_size: 'lg',
						tooltip : '<?php _e( '<p>If enabled, the levels selected in the Add To section will inherit the status of the current level when a member is registered. This means if someone is cancelled from the current level and they were automatically added to another level, and this setting was enabled, they would also be cancelled from the level they were automatically added to.</p><p>This must be turned on prior to member registration. This will not work retroactively.</p>', 'wishlist-member' ); ?>',
						column : 'col-md-12 no-padding'
					}
				</template>
			</div>
		</div>
		<div class="panel-footer -content-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<?php echo $tab_footer; ?>
				</div>
			</div>
		</div>
	</div>
</div>
