<?php 
foreach($wpm_levels AS $lid => $level):
	$level = (object) $level;
	$level->id = $lid;
?>
<div
	data-process="modal"
	id="arpreach-lists-modal-<?php echo $level->id; ?>-template" 
	data-id="arpreach-lists-modal-<?php echo $level->id; ?>"
	data-label="arpreach-lists-modal-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Settings for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Autoresponder Subscription Form Post URL', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'postURL[<?php echo $level->id; ?>]',
					column : 'col-12',
					'data-mirror-value' : '#arpreach-lists-<?php echo $level->id; ?>',
					tooltip : '<?php _e( 'The Post URL of the Subscription Form in your autoresponder.', 'wishlist-member' ); ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Unsubscribe if Removed from Level', 'wishlist-member' ); ?>',
					name  : 'arUnsub[<?php echo $level->id; ?>]',
					value : '1',
					uncheck_value : '',
					type  : 'checkbox',
					column : 'col-12',
					'data-mirror-value' : '#arpreach-unsubscribe-<?php echo $level->id; ?>',
				}
			</template>
		</div>
	</div>
</div>
<?php
endforeach;
?>
