<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="moosend-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="moosend-lists-modal-<?php echo $level->id; ?>"
	data-label="moosend-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'List Name', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'moosend-lists-select',
					style : 'width: 100%',
					name : 'lists[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#moosend-lists-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'The Moosend List Name', 'wishlist-member' ); ?>',
					'data-placeholder' : '<?php _e( 'Select a List', 'wishlist-member' ); ?>',
					'data-allow-clear' : 1,
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe if Removed from Level', 'wishlist-member' ); ?>',
					name  : 'unsubscribe[<?php echo $level->id; ?>]',
					value : '1',
					uncheck_value : '',
					type  : 'checkbox',
					column : 'col-12',
					'data-mirror-value' : '#moosend-unsubscribe-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
