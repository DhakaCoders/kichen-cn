<?php
require($this->legacy_wlm_dir . '/core/InitialValues.php');
$country_list = $this->get_country_list();
$howmany = $this->GetOption('broadcast_page_pagination');
if (is_numeric(wlm_arrval($_GET, 'howmany')) || !$howmany) {
	if ( wlm_arrval($_GET, 'howmany') ) $howmany = (int) $_GET['howmany'];
	if ( !$howmany ) $howmany = $this->pagination_items[1];
	if ( !in_array( $howmany, $this->pagination_items ) ) $howmany = $this->pagination_items[1];
	$this->SaveOption('broadcast_page_pagination', $howmany);
}

// pagination
$offset = $_GET['offset'] - 1;
if ( $offset < 0 ) $offset = 0;
$perpage = $howmany;
$offset = $offset * $perpage;
$emails_count = $this->GetALLEmailbroadcast(true);
$total_pages = ceil( $emails_count / $perpage);
$broadcast_emails = $this->GetALLEmailbroadcast(false,$offset,$perpage);
$current_page = $offset / $perpage + 1;
$offset += 1;
//get the number of emails in queue
$email_queue_count = $this->GetEmailBroadcastQueue(null,false,false,0,true);
$wpm_levels = $this->GetOption('wpm_levels');

//lets get the signature
$signature = $this->GetOption('broadcast');
if ( isset( $signature['signature'] ) ) {
	$signature = $signature['signature'];
} else {
	$signature = "";
}

$street1 = $this->GetOption('email_sender_street1');
$street2 = $this->GetOption('email_sender_street2');
$city    = $this->GetOption('email_sender_city');
$state   = $this->GetOption('email_sender_state');
$zipcode = $this->GetOption('email_sender_zipcode');
$country = $this->GetOption("email_sender_country");
$complete_canspam = $street1 && $city && $state && $zipcode && $country ? true : false;

require($this->legacy_wlm_dir . '/core/InitialValues.php');
$keys = array(
	'unsubscribe_notice_email_subject',
	'unsubscribe_notice_email_message',
);
$default_data = array();
foreach($keys AS $key) {
	$default_data[$key] = $WishListMemberInitialData[$key];
}
printf("\n<script type='text/javascript'>var default_data = %s;\n</script>\n", json_encode($default_data));
?>
<?php
	include( $this->pluginDir3 ."/ui/admin_screens/administration/broadcast/list.php");
?>
<?php
	include( $this->pluginDir3 ."/ui/admin_screens/administration/broadcast/new.php");
?>
<!-- Modal Boxes -->
<div id="canspam-modal" data-id="canspam-modal" data-label="canspam_modal_label" data-title="Settings" data-classes="modal-lg" style="display:none">
	<div class="body">
		<ul class="nav nav-tabs -nav-tab-tight mb-3" role="tablist">
			<li role="presentation" class="nav-item"><a class="active nav-link can-span-nav" href="#can-spam" aria-controls="can-spam" role="tab" data-toggle="tab"><?php _e( 'CAN-SPAM','wishlist-member' ); ?></a></li>
			<li role="presentation" class="nav-item"><a class="nav-link" href="#notification" aria-controls="notification" role="tab" data-toggle="tab"><?php _e( 'Member Unsubcribed Notification','wishlist-member' ); ?></a></li>
		</ul>
		<div class="tab-content no-margin">
			<div role="tabpanel" class="tab-pane active" id="can-spam">
				<p><?php _e( 'CAN-SPAM requires a physical mailing address be provided in emails in order to send Email Broadcasts to members.', 'wishlist-member' ); ?></p>
				<p class="no-canspam-msg text-danger"><?php _e( 'You must fill in all CAN-SPAM fields below before you can create an Email Broadcast.', 'wishlist-member' ); ?></p>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Name', 'wishlist-member' ); ?>',
							name : '<?php $this->Option('email_sender_name'); ?>',
							value : '<?php $this->OptionValue(); ?>',
							required : 'required'
						}
						</template>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Email', 'wishlist-member' ); ?>',
							name : '<?php $this->Option('email_sender_address'); ?>',
							value : '<?php $this->OptionValue(); ?>',
							required : 'required'
						}
						</template>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Street 1', 'wishlist-member' ); ?>',
							name : 'email_sender_street1',
							value : '<?php echo $street1; ?>'
						}
						</template>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Street 2 (Optional)', 'wishlist-member' ); ?>',
							name : 'email_sender_street2',
							value : '<?php echo $street2; ?>'
						}
						</template>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<template class="wlm3-form-group">
						{
							label : '<?php _e( 'City/Town', 'wishlist-member' ); ?>',
							name : 'email_sender_city',
							value : '<?php echo $city; ?>'
						}
						</template>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<template class="wlm3-form-group">
						{
							label : '<?php _e( 'State/Province', 'wishlist-member' ); ?>',
							name : 'email_sender_state',
							value : '<?php echo $state; ?>'
						}
						</template>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
						<template class="wlm3-form-group">
						{
							label : '<?php _e( 'Zip/Postal Code', 'wishlist-member' ); ?>',
							name : 'email_sender_zipcode',
							value : '<?php echo $zipcode; ?>'
						}
						</template>
					</div>
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
						<div class="form-group">
							<label for=""><?php _e( 'Country', 'wishlist-member' ); ?></label>
							<select class="form-control wlm-countries" name="<?php $this->Option('email_sender_country'); ?>" style="width: 100%" data-placeholder="Select a Country">
								<option></option>
								<?php foreach ( $country_list as $cntry ): ?>
								<?php $selected = ($cntry == $country) ? 'selected="selected"' : ''; ?>
								<option value="<?php echo $cntry; ?>" <?php echo $selected; ?>><?php echo $cntry; ?></option>
							<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="notification">
				<p><?php _e( 'If Enabled, this optional email notification will be sent to the site admin when a Member Unsubscribes from Email Broadcasts.', 'wishlist-member' ); ?></p>
				<?php
					$email_recipient    = $this->GetOption('unsubscribe_notice_email_recipient') ?: get_bloginfo('admin_email');
					$email_subject      = $this->GetOption('unsubscribe_notice_email_subject');
					$email_subject 	    = $email_subject ? $email_subject : $WishListMemberInitialData['unsubscribe_notice_email_subject'];
					$email_body  	      = $this->GetOption('unsubscribe_notice_email_message');
					$email_body		      = $email_body ? $email_body : nl2br($WishListMemberInitialData['unsubscribe_notice_email_message']);
					$unsub_notification = $this->GetOption('unsub_notification');
				?>
				<div class="row">
					<div class="col-md-12">
						<template class="wlm3-form-group">
							{
								label : '<?php _e( 'Enable', 'wishlist-member' ); ?>',
								name  : 'unsub_notification',
								value : '1',
								checked_value : '<?php echo $unsub_notification; ?>',
								uncheck_value : '0',
								class : 'wlm_toggle-switch notification-switch',
								type  : 'checkbox'
							}
						</template>
						<input type="hidden" name="action" value="admin_actions" />
						<input type="hidden" name="WishListMemberAction" value="save" />
					</div>
				</div><br />
				<div class="row">
					<template class="wlm3-form-group">{
						addon_left : 'Recipient Email',
						group_class : '-label-addon mb-2',
						type : 'text',
						name : 'unsubscribe_notice_email_recipient',
						column: 'col-md-6',
						value: '<?php echo $email_recipient; ?>'
					}</template>
					<template class="wlm3-form-group">{
						addon_left : 'Subject',
						group_class : '-label-addon mb-2',
						type : 'text',
						name : 'unsubscribe_notice_email_subject',
						column: 'col-md-12',
						value: '<?php echo $email_subject; ?>',
						class: 'email-subject'
					}</template>
					<div class="col-md-12">
						<div class="form-group mb-2">
							<textarea class="form-control email-message" data-name="unsubscribe_notice_email_message" name="unsubscribe_notice_email_message" id="email-message" skip-save="1"><?php echo $email_body; ?></textarea>
						</div>
					</div>
					<div class="col-md-12">
						<button class="btn -default -condensed email-reset-button" data-target="unsubscribe_notice_email"><?php _e( 'Reset to Default', 'wishlist-member' ); ?></button>
						<template class="wlm3-form-group">{
							type : 'select',
							column : 'col-md-5 pull-right no-margin no-padding',
							'data-placeholder' : '<?php _e( 'Insert Merge Codes', 'wishlist-member' ); ?>',
							group_class : 'shortcode_inserter mb-0',
							style : 'width: 100%',
							options : get_merge_codes(),
							grouped: true,
							class : 'insert_text_at_caret',
							'data-target' : '[name=unsubscribe_notice_email_message]',
						}</template>
					</div>
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

<div id="send-queue-modal" data-id="send-queue-modal" data-label="send-queue_modal_label" data-title="Send Email Queue" data-classes="modal-md" style="display:none">
	<div class="body">
		<div class="message-holder">
			<p class="message"><?php _e( 'Are you sure you want to send the emails currently in queue?', 'wishlist-member' ); ?></p>
			<input type="hidden" name="action" value="admin_actions" />
			<input type="hidden" name="WishListMemberAction" value="get_emails_in_queue" />
		</div>
		<div class="row progress-holder" style="display:none;">
			<div class="col-md-12">
				<div class="export-progress">
					<div class="progress">
						<div class="progress-bar -success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0" ></div>
					</div>
					<div class="text-center progress-warning text-danger"></div>
				</div>
				<br />
			</div>
		</div>
	</div>
	<div class="footer">
		<button type="button" class="btn -bare cancel-button" data-dismiss="modal">
			<span><?php _e( 'Cancel', 'wishlist-member' ); ?></span>
		</button>
		<button type="button" class="btn -primary save-button">
			<span><?php _e( 'Yes', 'wishlist-member' ); ?></span>
		</button>
	</div>
</div>

<div id="check-status-modal" data-id="check-status-modal" data-label="check-status_modal_label" data-title="Broadcast Email Status" data-classes="modal-md" style="display:none">
	<div class="body">
		<p><?php _e( 'Retrieving broadcast email status, please wait...', 'wishlist-member' ); ?></p>
	</div>
	<div class="footer">
		<button type="button" class="btn -bare" data-dismiss="modal">
			<span><?php _e( 'Close', 'wishlist-member' ); ?></span>
		</button>
	</div>
</div>