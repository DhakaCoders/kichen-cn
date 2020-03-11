<?php if ($show_page_menu) : ?>
<?php return;
endif; ?>
<?php
$latest_wpm_ver = $this->get_latest_version();
if (!$latest_wpm_ver)
	$latest_wpm_ver = $this->version;

$reversion = explode(".", $this->version);
$wlm_version = $reversion[0] . '.' . $reversion[1];
$wlm_build = $reversion[2];


?>
<h2><?php printf($this->lang('plugin_dashboard'), $this->name); ?></h2>
<div id="dashboard-widgets-wrap">

	<div id="dashboard-widgets" class="metabox-holder">
		<div class='postbox-container' style='width:49%;margin-right:1%'><!-- BEGIN LEFT POSTBOX CONTAINER -->


			<!-- BEGIN NEW POSTBOX -->
			<div id="wl_dashboard_right_now" class="postbox">
				<h3><span><?php echo $this->lang('overview')?></span></h3>
				<div class="inside"><!-- begin inside content -->

					<div class="table table_content">
						<p class="sub">WishList Coupon 2.0</p>
						<p>WishList Coupon 2.0 allows you to offer discount coupons which can be applied when users purchase membership levels. These coupons can be customized according to your needs.</p>
						</div>

						<div class="table table_discussion">
							<p class="sub">Support</p>
							<table>
								<tr class="first">
									<td class="last t"><a href="http://support.wishlistproducts.com" target="_blank"><?php echo $this->lang('customer_support')?></a></td>
								</tr>
								<tr>
									<td class="last t"><a href="http://customers.wishlistproducts.com/video-tutorials/<?php echo $this->slug?>" target="_blank"><?php echo $this->lang('video_tutorials')?></a></td>
								</tr>
								<tr>
									<td class="last t"><a href="http://wishlistproducts.com/release-notes/<?php echo $this->slug?>" target="_blank"><?php echo $this->lang('release_notes') ?></a></td>
								</tr>
							</table>
						</div>

						<hr class="clear" />

						<p>
						<strong> <a  href="admin.php?page=WishListCoupon20&wl=settings">Settings</a>  </strong> -  Adjust the main settings for WishList Coupon 2.0 <br />
						</p>

						<hr class="clear" />

					<?php if ($this->is_plugin_latest()): ?>
									<p>
										<a style="float:right" href="?<?php echo $_SERVER['QUERY_STRING']; ?>&checkversion=1"><?php echo $this->lang('update_check')?></a>
										<?php printf($this->lang('updated_version'), $this->name, $wlm_version); ?>
									</p>

					<?php else: ?>
										<p><?php printf($this->lang('current_version'),  $wlm_version) ?>
											<br />
											<span style="color:red"><?php printf($this->lang('latest_version'), $latest_wpm_ver); ?></span></p>
										<p style="text-align:right; " class="upgrade">
						<?php printf($this->lang('link_upgrade'), $this->get_download_url(), $this->get_update_url()); ?></p>
					<?php endif; ?>
									</div><!-- end inside -->
								</div><!-- end this postbox -->

								<?php if($this->sku > 0 && !WishListUtils::is_url_local(strtolower(get_bloginfo('url')))) : ?>
								<!-- BEGIN NEW POSTBOX -->
								<div class="postbox">
									<h3>Deactivate <?php echo $this->name?></h3>
									<div class="inside"><!-- begin inside content -->
										<form method="post" onsubmit="return confirm('<?php echo $this->lang('confirm_deactivate') ?>')">
											<p class="submit"><?php echo $this->lang('deactivate_note') ?><br /><br />
												<input type="hidden" name="wordpress_wishlist_deactivate" value="<?php echo $this->sku; ?>" />
												<input type="submit" value="Deactivate License For This Site" name="Submit" class="button-secondary"/>
											</p>
										</form>

									</div><!-- end inside -->
								</div><!-- end this postbox -->
								<?php endif; ?>


							</div><!-- END LEFT POSTBOX CONTAINER -->
							<div class="postbox-container" style="width:49%;"><!-- BEGIN RIGHT POSTBOX CONTAINER -->

								<!-- BEGIN NEW POSTBOX -->
								<div class="postbox">
									<h3><?php printf($this->lang('news'), $this->name); ?></h3>
									<div class="inside wlrss-widget"><!-- begin inside content -->

									<p><?php echo $this->lang('news_link')?></p>
								</div><!-- end inside -->
							</div><!-- end this postbox -->

						</div><!-- END RIGHT POSTBOX CONTAINER -->
					</div><!-- END dashboard-widgets-wrap -->

					<div class="clear"></div>

					<p>
						<small><?php echo $this->name?> v<?php echo $wlm_version; ?> |  Build  <?php echo $wlm_build; ?> | WordPress <?php echo get_bloginfo('version'); ?> | PHP <?php echo phpversion(); ?> on <?php echo php_sapi_name(); ?></small>
	</p>
</div>
<style type="text/css">
	/** Override to fix upgrade/downgrade buttons **/
	#dashboard_right_now .upgrade a.button {
		float: none;
	}
</style>
<script type="text/javascript">
jQuery(function($) {
	data = {
		action: 'wlm_feeds'
	}
	$.ajax({
		type: 'POST',
		url: '<?php echo admin_url('admin-ajax.php');?>',
		data: data,
		success: function(response) {
			$('.wlrss-widget').html(response);
		}
	});
});
</script>
