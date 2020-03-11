<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="maropost-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="maropost-lists-modal-<?php echo $level->id; ?>"
	data-label="maropost-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Lists', 'wishlist-member' ); ?>',
					type : 'select',
					class : 'maropost-lists',
					style : 'width: 100%',
					name : 'maps[<?php echo $level->id; ?>][]',
					multiple : 'multiple',
					column : 'col-12',
					'data-mirror-value' : '#maropost-lists-<?php echo $level->id; ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe if Removed from Level', 'wishlist-member' ); ?>',
					name  : '<?php echo $level->id; ?>[autoremove]',
					value : '1',
					uncheck_value : '',
					type  : 'checkbox',
					column : 'col-12',
					'data-mirror-value' : '#maropost-unsubscribe-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
