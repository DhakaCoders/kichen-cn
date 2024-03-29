<div class="wizard-form -dark">
	<div class="content-wrapper -no-header level-data">
		<div class="row align-items-center">
			<div class="col-md-5">
				<div class="information text-center">
					<img src="<?php echo $this->pluginURL3; ?>/ui/images/wishlist-member-logo.png" class="mx-auto d-block" alt="">
				</div>
			</div>
			<div class="col-md-7">
				<div class="white-background">
					<div class="row">
						<div class="col-md-12">
							<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
						</div>
					</div>
					<br>
					<?php
						$WPWLKey = $this->GetOption('LicenseKey');
						if( $WPWLKey ) {
							$WPWLKeyExpire = $this->GetOption('LicenseExpiration');
							$WPWLEmail = $this->GetOption('LicenseEmail');
							//make sure we have a valid license info
							$WPWLKey = $WPWLKey !== false ? ( trim($WPWLKey) != "" ? trim($WPWLKey) : false )  : false;
							$WPWLEmail = $WPWLEmail !== false ? ( trim($WPWLEmail) != "" ? trim($WPWLEmail) : false )  : false;

							$key_is_expired = date('Y-m-d 00:00:00') > $WPWLKeyExpire;
							$lifetime = substr($WPWLKeyExpire, 0, 4) > 2999;
							$text1 = $key_is_expired ? __('Support Plan Expired', 'wishlist-member') : __('Support Plan Expiration', 'wishlist-member');
							$text2 = $key_is_expired ? __('Click Here to update your expired Support Plan Now', 'wishlist-member') : __('Click Here for more information on a Support Plan Renewal', 'wishlist-member');
							$span_style = $key_is_expired ? ' style="color:red"' : '';
						}
					?>
					<div class="row">
						<?php if ( $WPWLKey ) : ?>
						<div class="col-md-12">
							<div class="panel-body mb-4 text-center">
								<h4 class="mb-3"><?php _e( 'Congratulations, your license was successfully activated.', 'wishlist-member' ); ?></h4>
								<p><strong><?php _e('License Key','wishlist-member');?>:</strong> ************************<?php echo substr($WPWLKey, -4); ?></p>
								<p><?php _e('Support Plan Expiration','wishlist-member');?>: <span<?php echo $span_style; ?>><?php echo $lifetime ? __('Lifetime','wishlist-member') : date('F j, Y', strtotime($WPWLKeyExpire)); ?></span></p>
							</div>
						</div>
						<?php else : ?>
						<div class="col-md-12">
							<div class="text-center form-text text-danger help-block mb-5">
								<h4><?php _e( 'A valid WishList Member license key is required to qualify for updates and support.', 'wishlist-member' ); ?></h4>
							</div>
						</div>
						<?php endif; ?>
						<div class="col-md-12 text-center">
							<?php $wpm_levels = $this->GetOption('wpm_levels'); ?>
							<a href="#" data-screen="license-confirm" next-screen="start" class="btn -success -lg next-btn">
								Run the Setup Wizard now
								<i class="wlm-icons">arrow_forward</i>
							</a>
							<br><br>
							<a href="#" class="btn -bare -lg next-btn" data-screen="thanks" next-screen="home">
								No Thanks
							</a>
							<br><br>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
