<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="constantcontact-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="constantcontact-lists-modal-<?php echo $level->id; ?>"
	data-label="constantcontact-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'List', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'cc-lists',
					style : 'width: 100%',
					name : 'ccID[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#constantcontact-lists-<?php echo $level->id; ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe if Removed from Level', 'wishlist-member' ); ?>',
					name  : 'ccUnsub[<?php echo $level->id; ?>]',
					value : '1',
					uncheck_value : '',
					type  : 'checkbox',
					column : 'col-12',
					'data-mirror-value' : '#constantcontact-unsubscribe-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
