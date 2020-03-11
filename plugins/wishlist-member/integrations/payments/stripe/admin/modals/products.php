<?php
foreach($all_levels AS $levels):
	foreach($levels AS $level) :
		$level = (object) $level;
?>
<div
	data-process="modal"
	id="products-<?php echo $config['id']; ?>-<?php echo $level->id; ?>-template" 
	data-id="products-<?php echo $config['id']; ?>-<?php echo $level->id; ?>"
	data-label="products-<?php echo $config['id']; ?>-<?php echo $level->id; ?>"
	data-title="Editing <?php echo $config['name']; ?> Product for <?php echo $level->name; ?>"
	data-show-default-footer="1"
	style="display:none">
	<div class="body">
		<input type="hidden" name="stripeconnections[<?php echo $level->id; ?>][sku]" value="<?php echo $level->id; ?>">
		<input type="hidden" name="stripeconnections[<?php echo $level->id; ?>][membershiplevel]" value="<?php echo $level->name; ?>">
		<div class="row">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Stripe Plan', 'wishlist-member' ); ?>',
					type : 'radio',
					name : 'stripeconnections[<?php echo $level->id; ?>][subscription]',
					value : 1,
					column : 'col-12',
					checked : 'checked',
					class : 'stripe-plan-toggle',
					'data-target' : '.stripe-plan-<?php echo $level->id; ?>',
				}
			</template>
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'One Time Payment', 'wishlist-member' ); ?>',
					type : 'radio',
					name : 'stripeconnections[<?php echo $level->id; ?>][subscription]',
					value : 0,
					column : 'col-12',
					class : 'stripe-plan-toggle',
					'data-target' : '.stripe-onetime-<?php echo $level->id; ?>',
				}
			</template>
		</div>
		<div style="display:none;" class="row stripe-onetime-<?php echo $level->id; ?>">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Amount', 'wishlist-member' ); ?>',
					type : 'text',
					name : 'stripeconnections[<?php echo $level->id; ?>][amount]',
					class : '-amount',
					placeholder : '<?php _e( 'Enter Amount', 'wishlist-member' ); ?>',
					'data-mirror-value' : '#stripe-product-amount-<?php echo $level->id; ?>',
					column : 'col-12',
				}
			</template>
		</div>
		<div class="row stripe-plan-<?php echo $level->id; ?>">
			<template class="wlm3-form-group">
				{
					label : '<?php _e( 'Stripe Plan', 'wishlist-member' ); ?>',
					type : 'select',
					name : 'stripeconnections[<?php echo $level->id; ?>][plan]',
					style : 'width: 100%',
					'data-mirror-value' : '#stripe-product-plan-<?php echo $level->id; ?>',
					'data-placeholder' : '<?php _e( 'Choose a Stripe Plan', 'wishlist-member' ); ?>',
					'data-allow-clear' : 'true',
					options : WLM3ThirdPartyIntegration.stripe.plan_options,
					column : 'col-12',
				}
			</template>
		</div>
	</div>
</div>
<?php
	endforeach;
endforeach;
?>