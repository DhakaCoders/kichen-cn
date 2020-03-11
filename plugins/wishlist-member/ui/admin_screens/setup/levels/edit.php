<div id="levels-create" style="display:none;" class="show-saving">
	<form id="level-form">
		<input type="hidden" id="first-save">
		<div id="save-action-fields">
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="save_membership_level" />
			<input type="hidden" name="id">
		</div>
 		<div class="page-header">
			<div class="large-form">
				<div class="row">
					<div class="col-sm-auto col-md-auto col-lg-auto">
						<h2 class="page-title"><?php _e( 'Level Name','wishlist-member' ); ?></h2>
					</div>
					<div class="col-sm-5 col-md-6 col-lg-6 level-name-holder">
						<input name="name" placeholder="Enter Level Name" data-initial="" required="required" class="form-control input-lg" type="text">
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="all-level-data">
			<div class="col-md-12">
				<!-- Nav tabs -->
				<!-- start: v4 -->
				<ul class="nav nav-tabs responsive-tabs -no-background levels-edit-tabs" role="tablist">
				<!-- end: v4 -->					
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_access" role="tab" data-toggle="tab"><?php _e( 'Access','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_registrations" role="tab" data-toggle="tab"><?php _e( 'Registrations','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_requirements" role="tab" data-toggle="tab"><?php _e( 'Requirements','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_additional_settings" role="tab" data-toggle="tab"><?php _e( 'Additional Settings','wishlist-member' ); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link" href="#" data-href="#levels_notifications" role="tab" data-toggle="tab"><?php _e( 'Email Notifications','wishlist-member' ); ?></a></li>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content">
					<?php
						// tab panes
						include_once 'edit/access.php';
						include_once 'edit/registrations.php';
						include_once 'edit/requirements.php';
						include_once 'edit/additional_settings.php';
						include_once 'edit/notifications.php';
						include_once 'edit/hidden.php';
					?>
				</div>
			</div>
		</div>
		<?php
			// per level modals
			include_once 'edit/modal/header_footer.php';
			include_once 'edit/modal/email_notifications.php';
			include_once 'edit/modal/terms_and_conditions.php';
			include_once 'edit/modal/custom_redirects.php';
		?>
	</form>
</div>
<?php
	// global modals
	include_once 'edit/modal/recaptcha.php';
?>