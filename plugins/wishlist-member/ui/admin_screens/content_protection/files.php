<div class="page-header">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-8">
			<h2 class="page-title pull-left">
				<?php _e( 'Files', 'wishlist-member' ); ?>
			</h2>
			<div class="pull-left -in-header" style="margin: 0 0 -5px 5px">
				<?php $enable_protection = $this->GetOption('file_protection') ?>
				<template class="wlm3-form-group">
					{
						name  : 'file_protection',
						value : '1',
						checked_value : '<?php echo $enable_protection; ?>',
						uncheck_value : '0',
						class : 'wlm_toggle-switch -in-header enable-protection',
						type  : 'checkbox',
					}
				</template>
				<input type="hidden" name="action" value="admin_actions" />
				<input type="hidden" name="WishListMemberAction" value="toggle_file_protection" />
			</div>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-4">
			<?php include $this->pluginDir3 . '/helpers/header-icons.php'; ?>
		</div>
	</div>
</div>
<?php
	if ( !$enable_protection ) {
		_e("File Protection is currently disabled.", 'wishlist-member');
		return;
	}
?>
<?php
$content_type = "attachment";
$content_comment = false;
include( $this->pluginDir3 ."/ui/admin_screens/content_protection/post_page_files/content.php");
?>
<div id="settings-modal" data-id="settings-modal" data-label="settings_modal_label" data-title="Settings" data-classes="modal-lg" style="display:none">
	<div class="body">
		<div class="form-group">
			<label for="">File Protection Ignore List:</label>
			<input type="text" name="<?php $this->Option('file_protection_ignore'); ?>" class="form-control" value="<?php $this->OptionValue(); ?>" />
			<small class="form-text text-muted"><?php _e( 'Add the filename extensions of files that should not be protected. Separate each filename extension with a comma. (example: txt, css)', 'wishlist-member' ); ?></small>
		</div>
		<input type="hidden" name="action" value="admin_actions" />
		<input type="hidden" name="WishListMemberAction" value="save" />
		<div class="row">
			<div class="col-md-12">
				<p>* <?php _e( 'Using NGINX?', 'wishlist-member' ); ?> <a href="#wlm-nginx" data-toggle="collapse" ><?php _e( 'Click here.', 'wishlist-member' ); ?></a></p>
				<div id="wlm-nginx" class="collapse">
					<p><?php _e( 'Add the following line in your NGINX site configuration\'s server {} block:', 'wishlist-member' ); ?></p>
					<p><code>include <?php echo $this->wp_upload_path; ?>/wlm_file_protect_nginx.conf;</code></p>
					<p><a href="http://wlplink.com/go/nginxinfo" target="_blank"><?php _e( 'Read the knowledge base article for more info.', 'wishlist-member' ); ?></a></p>
				</div>
			</div>
		</div>
	</div>
	<div class="footer">
    <button type="button" class="btn -bare" data-dismiss="modal">
    	<span><?php _e( 'Close', 'wishlist-member' ); ?></span>
    </button>
    <button type="button" class="btn -primary save-button">
    	<i class="wlm-icons">save</i>
    	<span><?php _e( 'Save', 'wishlist-member' ); ?></span>
    </button>
    <button class="-close btn -success -modal-btn save-button">
    	<i class="wlm-icons">save</i>
    	<span><?php _e( 'Save & Close', 'wishlist-member' ); ?></span>
    </button>
	</div>
</div>