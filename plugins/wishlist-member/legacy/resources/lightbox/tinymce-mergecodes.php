<?php
// levels
$wpm_levels = $this->GetOption('wpm_levels');

$paypalec_spb =  (array)$this->GetOption('paypalec_spb');
$paypalec_spb =  json_encode($paypalec_spb);

// paypal products
$wlm_paypal_products = json_encode(array(
	'paypalpsproducts'  => $this->GetOption('paypalpsproducts'),
	'paypalecproducts'  => $this->GetOption('paypalecproducts'),
	'paypalproproducts' => $this->GetOption('paypalproproducts'),
	'paypalpayflowproducts' => $this->GetOption('paypalpayflowproducts'),
));


echo <<<STRING
<script>
var wlm_paypal_products = {$wlm_paypal_products};
var paypalec_spb = {$paypalec_spb};
var paypalec_spb_default_set = false;
</script>
STRING;
?>
<script type="text/javascript" src="https://www.paypalobjects.com/api/checkout.min.js"></script>

<!-- paypal -->
<div style='display: none !important;' class='wlmtnmcelbox' id="wlmtnmce-paypal-lightbox">
	<div class="media-modal wp-core-ui" style="display: none !important;">
		<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span></button>
		<div class="media-frame-title"><h1><?php _e('PayPal Shortcode Builder', 'wishlist-member'); ?></h1></div>
		<div class="media-modal-content">
			<!-- Main Contend Starts -->
			<div class="wlmtnmcelbox-content">

				<!-- Options -->
				<div class="options-holder">
					<table width="100%" height="100%" cellspacing="4" cellpadding="0">
						<tr height="1%" valign="top">
							<td width="50%">
								<p class="modal-field-label"><?php _e('Select Product', 'wishlist-member'); ?>:</p>
								<select class="wlmtnmcelbox-products shortcode-fields" style="width:100%">
									<option value=""><?php _e('Select a Product','wishlist-member'); ?></option>
								</select>

								<p>&nbsp;</p>

								<div class="spb">
									<div style="width: 47%; float: left">
										<p class="modal-field-label"><?php _e('Layout', 'wishlist-member'); ?>:</p>
										<select class="spb_options shortcode-fields" name="layout" style="width:100%">
											<option value="vertical"><?php _e('Vertical','wishlist-member'); ?></option>
											<option value="horizontal"><?php _e('Horizontal','wishlist-member'); ?></option>
										</select>
									</div>
									<div style="width: 47%; float: right">
										<p class="modal-field-label"><?php _e('Size', 'wishlist-member'); ?>:</p>
										<select class="spb_options shortcode-fields" name="size" style="width:100%">
											<option value="medium"><?php _e('Medium','wishlist-member'); ?></option>
											<option value="large"><?php _e('Large','wishlist-member'); ?></option>
											<option value="responsive"><?php _e('Responsive','wishlist-member'); ?></option>
										</select>
									</div>
									<div style="width: 47%; float: left">
										<p class="modal-field-label"><?php _e('Shape', 'wishlist-member'); ?>:</p>
										<select class="spb_options shortcode-fields" name="shape" style="width:100%">
											<option value="pill"><?php _e('Pill','wishlist-member'); ?></option>
											<option value="rect"><?php _e('Rectangle','wishlist-member'); ?></option>
										</select>
									</div>
									<div style="width: 47%; float: right">
										<p class="modal-field-label"><?php _e('Color', 'wishlist-member'); ?>:</p>
										<select class="spb_options shortcode-fields" name="color" style="width:100%">
											<option value="gold"><?php _e('Gold','wishlist-member'); ?></option>
											<option value="blue"><?php _e('Blue','wishlist-member'); ?></option>
											<option value="silver"><?php _e('Silver','wishlist-member'); ?></option>
											<option value="white"><?php _e('White','wishlist-member'); ?></option>
											<option value="black"><?php _e('Black','wishlist-member'); ?></option>
										</select>
									</div>

									<br clear="all">

									<p class="modal-field-label"><?php _e('Allowed Funding Source', 'wishlist-member'); ?>:</p>
									<label style="margin-right: 20px"><input class="spb_options shortcode-fields" name="funding" type="checkbox" value="CARD"> <?php _e('Card', 'wishlist-member'); ?></label>
									<label style="margin-right: 20px"><input class="spb_options shortcode-fields" name="funding" type="checkbox" value="CREDIT"> <?php _e('Credit', 'wishlist-member'); ?></label>
									<label style="margin-right: 20px"><input class="spb_options shortcode-fields" name="funding" type="checkbox" value="ELV"> <?php _e('ELV', 'wishlist-member'); ?></label>
								</div>

								<div class="notspb">
									<p class="modal-field-label"><?php _e('Select Button Type', 'wishlist-member'); ?>:</p>
									<select class="wlmtnmcelbox-buttons shortcode-fields" style="width:100%">
										<option value="pp_pay"><?php _e('PayPal Button: Pay with PayPal','wishlist-member'); ?></option>
										<option value="pp_buy"><?php _e('PayPal Button: Buy now with PayPal','wishlist-member'); ?></option>
										<option value="pp_checkout"><?php _e('PayPal Button: Check out with PayPal','wishlist-member'); ?></option>
										<option value="custom_image"><?php _e('Custom Image','wishlist-member'); ?></option>
										<option value="plain_text"><?php _e('Plain Text','wishlist-member'); ?></option>
									</select>

									<div class="wlmtnmcelbox-button-options plain_text" style="display:none;">
										<p class="modal-field-label"><?php _e('Button Text', 'wishlist-member'); ?>:</p>
										<input class="wlmtnmcelbox-button-options plain_text" style="width:100%;box-sizing:border-box" type="text" value="Buy Now">
									</div>
									<div class="wlmtnmcelbox-button-options custom_image" style="display:none;">
										<p class="modal-field-label"><?php _e('Image URL', 'wishlist-member'); ?>:</p>
										<input class="wlmtnmcelbox-button-options custom_image" style="width:100%;box-sizing:border-box" type="text" value="" placeholder="http://">
									</div>
									<div class="wlmtnmcelbox-button-options pp_pay pp_buy pp_checkout">
										<p class="modal-field-label"><?php _e('Button Size', 'wishlist-member'); ?>:</p>
										<select class="wlmtnmcelbox-button-options pp_pay pp_buy pp_checkout">
											<option selected="selected" value="s"><?php _e('Small','wishlist-member'); ?></option>
											<option value="m"><?php _e('Medium','wishlist-member'); ?></option>
											<option value="l"><?php _e('Large','wishlist-member'); ?></option>
										</select>
									</div>
								</div>
							</td>

							<td style="padding-left: 20px; text-align: center">
								<p class="modal-field-label"><?php _e('Button Preview','wishlist-member'); ?>:</p>
								<span id="paypalec-spb-preview"></span>
								<div class="wlmtnmcelbox-button-preview">
									<img style="display:none" class="pp_pay l" border="0" src="https://www.paypalobjects.com/webstatic/en_AU/i/buttons/btn_paywith_primary_l.png">
									<img style="display:none" class="pp_pay m" border="0" src="https://www.paypalobjects.com/webstatic/en_AU/i/buttons/btn_paywith_primary_m.png">
									<img style="display:none" class="pp_pay s" border="0" src="https://www.paypalobjects.com/webstatic/en_AU/i/buttons/btn_paywith_primary_s.png">
									<img style="display:none" class="pp_buy l" border="0" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-large.png">
									<img style="display:none" class="pp_buy m" border="0" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png">
									<img style="display:none" class="pp_buy s" border="0" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-small.png">
									<img style="display:none" class="pp_checkout l" border="0" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-large.png">
									<img style="display:none" class="pp_checkout m" border="0" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png">
									<img style="display:none" class="pp_checkout s" border="0" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-small.png">
									<img style="display:none;max-width:400px;max-height:90px;" class="custom_image s" border="0" src="">
									<input style="display:none" class="plain_text s" type="button" value="">
								</div>
							</td>
						</tr>

						<tr>
							<td valign="bottom" colspan="2">
								<p class="modal-field-label"><?php _e('Shortcode Preview','wishlist-member'); ?>:</p>
								<textarea class="wlmtnmcelbox-preview-text" readonly="readonly" style="text-align:center"></textarea>
								<div style="text-align:right">
									<input tab="display_details" type="button" class="button button-primary wlmtnmcelbox-insertcode" value="<?php _e("Insert Mergecode", "wishlist-member")?>" />
								</div>
							</td>
						</tr>

					</table>

				</div>
				<!-- Options Ends -->
			</div>
			<!-- Main Contend Ends -->
		</div>

	</div>
	<div class="media-modal-backdrop" style="display: none !important;"></div>
</div>

<!-- private tags -->
<div style='display: none !important;' class='wlmtnmcelbox' id="wlmtnmce-private-tags-lightbox">
	<div class="media-modal wp-core-ui" style="display: none !important;">
		<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span></button>
		<div class="media-frame-title"><h1>Private Tags</h1></div>
		<div class="media-modal-content">
			<!-- Main Contend Starts -->
			<div class="wlmtnmcelbox-content">

				<!-- Options -->
				<div class="options-holder">
						<p class="modal-field-label">
							<input type='checkbox' value='1' class='wlmtnmcelbox-reverse' /> Reverse Private Tags
						</p>
						<p class="modal-field-label">Membership Levels:</p>
						<select class="wlmtnmcelbox-levels" multiple="multiple" data-placeholder='Select Membership Level(s)' >
						<option value="all">Select All</option>
						<?php foreach( $wpm_levels as $sku => $level ){
							if (is_numeric($sku)){
								$levelname=$level['name'];
								$levelname=str_replace("%","&#37;",$levelname);
								?>
								<option value="<?php echo $sku; ?>"><?php echo trim($levelname); ?></option>
								<?php 
								}
							}
						?>
						</select>
						<p class="modal-field-label">Content:</p>
						<textarea class="wlmtnmcelbox-content-text"></textarea>
				</div>
				<!-- Options Ends -->

				<!-- Preview -->
				<div class="wlmtnmcelbox-preview">
					<div class="wlmtnmcelbox-preview-msg" >
						<input tab="display_details" type="button" class="button button-primary wlmtnmcelbox-insertcode" value="<?php _e("Insert Shortcode", "wishlist-member")?>" />
						Shortcode Preview:
					</div>
					<textarea class="wlmtnmcelbox-preview-text"></textarea>
				</div>
				<!-- Preview Ends -->
			</div>
			<!-- Main Contend Ends -->
		</div>

	</div>
	<div class="media-modal-backdrop" style="display: none !important;"></div>
</div>

<!-- reg form shortcodes-->
<div style='display: none !important;' class='wlmtnmcelbox wlmtnmcelbox-regform-modal' id="wlmtnmce-reg-form-lightbox">
	<div class="media-modal wp-core-ui wlmtnmcelbox-regform-modal" style="display: none !important;">
		<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span></button>
		<div class="media-frame-title"><h1>Registration Form</h1></div>
		<div class="media-modal-content">
			<!-- Main Contend Starts -->
			<div class="wlmtnmcelbox-content">

				<!-- Options -->
				<div class="options-holder">
						<p class="modal-field-label">Membership Level:</p>
						<select class="reg-form-wlmtnmcelbox-levels">
						<?php foreach( $wpm_levels as $sku => $level ){
							if (is_numeric($sku)){
								$levelname=$level['name'];
								$levelname=str_replace("%","&#37;",$levelname);
								?>
								<option value="<?php echo $sku; ?>"><?php echo trim($levelname); ?></option>
								<?php 
								}
							}
						?>
						</select>
						<div class="wlmtnmcelbox-preview-msg" > <br><br>
							Shortcode Preview:
						</div>
						<textarea class="wlmtnmcelbox-preview-text"></textarea>
						<input tab="display_details" type="button" class="button button-primary wlmtnmcelbox-insertcode" value="<?php _e("Insert Shortcode", "wishlist-member")?>" />
				</div>
				<!-- Options Ends -->

				<!-- Preview -->
				<div class="wlmtnmcelbox-preview">
					
				</div>
				<!-- Preview Ends -->
			</div>
			<!-- Main Contend Ends -->
		</div>

	</div>
	<div class="media-modal-backdrop" style="display: none !important;"></div>
</div>

<!-- scheduler form shortcodes-->
<div style='display: none !important;' class='wlmtnmcelbox wlmtnmcelbox-scheduler-modal' id="wlmtnmce-scheduler-lightbox">
	<div class="media-modal wp-core-ui wlmtnmcelbox-scheduler-modal" style="display: none !important;">
		<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span></button>
		<div class="media-frame-title"><h1>Content Scheduler</h1></div>
		<div class="media-modal-content">
			<!-- Main Contend Starts -->
			<div class="wlmtnmcelbox-content">
				<!-- Options -->
				<div class="options-holder">
						<p class="modal-field-label">Title</p>
						<input type="text" class="scheduler-wlmtnmcelbox-title" value="My Upcoming Posts" />
						<p class="modal-field-label">Post type:</p>
						<?php
							$post_types = [
								'post' => 'Posts',
								'page' => 'Pages',
							];
							$args = array( '_builtin' => false);
							$cpost_types = get_post_types($args,'objects');
							foreach ( (array) $cpost_types as $key => $value) {
								$post_types[$key] = $value->label;
							}
						?>
						<select class="scheduler-wlmtnmcelbox-ptype" data-placeholder='Select Post Type' >
							<option value="all">- All -</option>
							<?php foreach( $post_types as $ptype => $name ) : ?>
								<option value="<?php echo $ptype; ?>"><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
						<p class="modal-field-label">
							<table width="100%">
								<tr>
									<td>
										Number of content: <input type="text" size="3" class="scheduler-wlmtnmcelbox-showpost" value="10" />
									</td>
									<td>
										List Spacing: <input type="text" size="3" class="scheduler-wlmtnmcelbox-px" value="4" />
									</td>
									<td>
										Date Separator: <input type="text" size="3" class="scheduler-wlmtnmcelbox-separator" value="@" />
									</td>
								</tr>
								<tr>
									<td>
										Order by:
										<select class="scheduler-wlmtnmcelbox-sort">
											<option value="title">Title</option>
											<option value="ID">ID</option>
											<option value="menu_order">Menu Order</option>
											<option value="date">Schedule Date</option>
											<option value="days">Days</option>
										</select>
									</td>
									<td>
										<input type="checkbox" class="scheduler-wlmtnmcelbox-showdate" value="1" checked="checked" /> Show Date
									</td>
									<td>
										<input type="checkbox" class="scheduler-wlmtnmcelbox-showtime" value="1" checked="checked" /> Show Time
									</td>
								</tr>
							</table>
						</p>

						<div class="wlmtnmcelbox-preview-msg" ><br><br>
							Shortcode Preview:
						</div>
						<textarea class="wlmtnmcelbox-preview-text"></textarea>
						<input tab="display_details" type="button" class="button button-primary wlmtnmcelbox-insertcode" value="<?php _e("Insert Shortcode", "wishlist-member")?>" />
				</div>
				<!-- Options Ends -->

				<!-- Preview -->
				<div class="wlmtnmcelbox-preview">
				</div>
				<!-- Preview Ends -->
			</div>
			<!-- Main Contend Ends -->
		</div>

	</div>
	<div class="media-modal-backdrop" style="display: none !important;"></div>
</div>