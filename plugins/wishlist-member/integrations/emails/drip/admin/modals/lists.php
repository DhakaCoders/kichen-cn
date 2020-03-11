<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="driplegacy-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="driplegacy-lists-modal-<?php echo $level->id; ?>"
	data-label="driplegacy-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'drip-campaigns-select',
					style : 'width: 100%',
					name : 'campaign[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#driplegacy-lists-<?php echo $level->id; ?>',
				}
			</template>
			<div class="col-md-12">
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Double Opt-in', 'wishlist-member' ); ?>',
						name  : 'double[<?php echo $level->id; ?>]',
						value : '1',
						uncheck_value : '',
						type  : 'checkbox',
						'data-mirror-value' : '#driplegacy-double-<?php echo $level->id; ?>',
						tooltip : '<?php _e( 'Check to enable double opt-in', 'wishlist-member' ); ?>',
					}
				</template>
				<template class="wlm3-form-group">
					{
						label : '<?php _e( 'Unsubscribe if Removed from Level', 'wishlist-member' ); ?>',
						name  : 'unsub[<?php echo $level->id; ?>]',
						value : '1',
						uncheck_value : '',
						type  : 'checkbox',
						'data-mirror-value' : '#driplegacy-unsubscribe-<?php echo $level->id; ?>',
					}
				</template>
			</div>
		</div>
	</div>
</div>
<?php
endforeach;
?>
