<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="getresponseapi-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="getresponseapi-lists-modal-<?php echo $level->id; ?>"
	data-label="getresponseapi-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'List Name', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'campaign[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#getresponseapi-lists-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'The GetResponse List Name', 'wishlist-member' ); ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe if Removed from Level', 'wishlist-member' ); ?>',
					name  : 'grUnsub[<?php echo $level->id; ?>]',
					value : '1',
					uncheck_value : '',
					type  : 'checkbox',
					column : 'col-12',
					'data-mirror-value' : '#getresponseapi-unsubscribe-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
