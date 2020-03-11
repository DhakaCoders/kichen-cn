<?php if ($show_page_menu) : ?>
<?php return; endif; ?>

<?php
$pages = get_pages(array('number' => 999));
$posts = get_posts(array('posts_per_page' => 999));
?>


<h2><?php printf($this->lang('plugin_settings'), $this->name) ?></h2>


<form method="post">
	<table class="form-table">
		<tr>
			<td colspan="2"><em>In order to track the reporting of a successful coupon transaction, please select the appropriate thank you pages or posts below.</em></td>
		</tr>
		<tr>
			<th>Apply Tracking To These Pages</th>
			<td>
				<select data-placeholder="Select pages to be tracked" style="width: 400px;" class="chosen-select" multiple name="<?php echo $this->option('tracking_pages')?>[]">
					<?php foreach($pages as $p): ?>
					<option <?php $this->option_selected($p->ID)?> value="<?php echo $p->ID?>"><?php echo $p->post_title?></option>
					<?php endforeach; ?>
				</select>
				<?php echo $this->tooltip('tooltip_pages')?>
			</td>
		</tr>
		<tr>
			<th>Apply Tracking To These Posts</th>
			<td>
				<select data-placeholder="Select posts to be tracked" style="width: 400px;"  class="chosen-select" multiple name="<?php echo $this->option('tracking_posts')?>[]">
					<?php foreach($posts as $p): ?>
					<option <?php $this->option_selected($p->ID)?> value="<?php echo $p->ID?>"><?php echo $p->post_title?></option>
					<?php endforeach; ?>
				</select>
				<?php echo $this->tooltip('tooltip_posts')?>
			</td>
		</tr>
	</table>
	<p class="submit">
	<?php $this->options(); $this->required_options(); ?>
	<input type="hidden" name="<?php echo $this->plugin_action?>" value="Save" />
	<input type="submit" value="<?php echo $this->lang('save_settings')?> "  class="button-primary"/>
</p>
</form>

<hr/>
<table class="form-table">
	<tr>
		<th>Tracking Code</th>
		<td>
			<textarea cols="90" rows="5"><?php echo $this->create_tracking_js()?></textarea><?php echo $this->tooltip('tooltip_tracking')?>
		</td>
	</tr>
</table>
<style type="text/css">
	.chosen-container-multi .chosen-choices li.search-field input[type=text] {
		height: 25px;
		width: 220px;
	}
	.chosen-container-multi {
		width: 430px;
	}
	textarea {
		vertical-align: top;
	}

</style>

<div id="default-content" style="display:none;">
<strong>[wishlist_coupon_label]</strong><br/>
<p class="wishlist-coupon-alert">
	[wishlist_coupon_success]
	[wishlist_coupon_alert]
</p>
[wishlist_coupon_code] [wishlist_coupon_apply] [wishlist_coupon_pay]


<style type="text/css">
.wishlist-coupon-success{
	color: green;
}

.wishlist-coupon-alert {
	color: red;
}

</style>
</div>

<script type="text/javascript">
jQuery(function($) {
	$(".chosen-select").chosen();
});
</script>
<?php include $this->plugin_dir .'/admin/tooltips/settings.tooltips.php'?>