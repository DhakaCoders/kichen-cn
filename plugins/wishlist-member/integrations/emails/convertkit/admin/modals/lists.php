<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="convertkit-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="convertkit-lists-modal-<?php echo $level->id; ?>"
	data-label="convertkit-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'List', 'wishlist-member' ); ?>',
					type : 'select',
					column : 'col-md-12 lists_column',
					name : 'ckformid[<?php echo $level->id; ?>]',
					'data-mirror-value' : '#convertkit-lists-<?php echo $level->id; ?>',
					style : 'width: 100%',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe if Removed from Level', 'wishlist-member' ); ?>',
					name  : 'ckOnRemCan[<?php echo $level->id; ?>]',
					value : 'unsub',
					uncheck_value : 'nothing',
					type  : 'checkbox',
					column : 'col-12',
					'data-mirror-value' : '#convertkit-unsubscribe-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>