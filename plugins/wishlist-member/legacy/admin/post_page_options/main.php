<?php
$ptype = $post->post_type;
$ptype_object = get_post_type_object( $ptype );

if ( ! $ptype ) {
	$ptype = 'post';
}
$hide_options_style = '';

if ( ! $this->PostTypeEnabled( $ptype ) ) : ?>
<div style="padding: 12px" class="wlm-custom-post-type-disabled">
	<div class="wlm-sp-container clearfix">
		<div class="sp-container-left">
			<p><?php _e('Content Protection is disabled for this Post Type.', 'wishlist-member'); ?></p>
		</div>
		<div class="sp-container-right">
			<a name="<?php _e('Enable Content Protection', 'wishlist-member'); ?>" href="#" class="wlm-btn -with-icons" id="wlm3_enable_custom_post_type">
				<i class="wlm-icons"><img src="<?php echo $this->pluginURL3; ?>/ui/images/outline-power_settings_new-24px.svg"></i>
				<span><?php _e('Enable Content Protection', 'wishlist-member'); ?></span>
			</a>
		</div>
	</div>
	<br clear="both">
</div>
<script type="text/javascript">
	jQuery('body').on('click', '#wlm3_enable_custom_post_type', function(e) {
		e.preventDefault();
		var data = {
			<?php echo $ptype; ?> : 1,
			action : 'admin_actions',
			WishListMemberAction : 'enable_custom_post_types'
		}

		jQuery.post(
			ajaxurl,
			data,
			function(result) {
				result = JSON.parse(result);
				if(result.success) {
					jQuery('.wlm-plugin-inside').show();
					jQuery('.wlm-custom-post-type-disabled').hide();
				}
			}
		);
		return false;
	});
</script>
<?php
$hide_options_style = 'style="display:none"';
endif;
include 'js.php';
?>

<div class="wlm-plugin-inside" <?php echo $hide_options_style; ?>>
	<!-- Sidebar: Start -->
	<div class="wlm-plugin-sidebar">
		<li class="active"><a href="#" data-target=".wlm-inside01" class="wlm-inside-toggle"><?php _e('Protection and Access', 'wishlist-member'); ?></a></li>
		<?php if ( $post->post_type != 'attachment' ) : ?><li><a href="#" data-target=".wlm-inside02" class="wlm-inside-toggle"><?php _e('Pay Per Post Access', 'wishlist-member'); ?></a></li><?php endif; ?>
		<li><a href="#" data-target=".wlm-inside03" class="wlm-inside-toggle"><?php _e('System Pages', 'wishlist-member'); ?></a></li>
		<?php
			do_action( 'wishlistmember3_post_page_options_menu' );
		?>

	</div>
	<!-- Sidebar: End -->
	<div class="wlm-plugin-content">
		<?php
			include 'protection.php';
			if ( $post->post_type != 'attachment' ) {
				include 'payperpost.php';
			}
			include 'systempages.php';

			do_action( 'wishlistmember3_post_page_options_content' );
		?>
	</div>
</div>