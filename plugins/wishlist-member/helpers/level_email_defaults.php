<?php
require $this->legacy_wlm_dir . '/core/InitialValues.php';

$level_email_defaults = array(
	'require_email_confirmation_start'               => $WishListMemberInitialData['email_conf_send_after'],
	'require_email_confirmation_send_every'          => $WishListMemberInitialData['email_conf_send_every'],
	'require_email_confirmation_howmany'             => $WishListMemberInitialData['email_conf_how_many'],
	'require_email_confirmation_sender_name'         => $this->GetOption( 'email_sender_name' ),
	'require_email_confirmation_sender_email'        => $this->GetOption( 'email_sender_address' ),
	'require_email_confirmation_subject'             => $this->GetOption( 'confirm_email_subject' ),
	'require_email_confirmation_message'             => $this->GetOption( 'confirm_email_message' ),

	'require_admin_approval_free_notification_admin' => $this->GetOption( 'require_admin_approval_free_notification_admin' ),
	'require_admin_approval_free_admin_subject'      => $this->GetOption( 'requireadminapproval_admin_subject' ),
	'require_admin_approval_free_admin_message'      => $this->GetOption( 'requireadminapproval_admin_message' ),

	'require_admin_approval_free_notification_user1' => $this->GetOption( 'require_admin_approval_free_notification_user1' ),
	'require_admin_approval_free_user1_sender_name'  => $this->GetOption( 'email_sender_name' ),
	'require_admin_approval_free_user1_sender_email' => $this->GetOption( 'email_sender_address' ),
	'require_admin_approval_free_user1_subject'      => $this->GetOption( 'requireadminapproval_email_subject' ),
	'require_admin_approval_free_user1_message'      => $this->GetOption( 'requireadminapproval_email_message' ),

	'require_admin_approval_free_notification_user2' => $this->GetOption( 'require_admin_approval_free_notification_user2' ),
	'require_admin_approval_free_user2_sender_name'  => $this->GetOption( 'email_sender_name' ),
	'require_admin_approval_free_user2_sender_email' => $this->GetOption( 'email_sender_address' ),
	'require_admin_approval_free_user2_subject'      => $this->GetOption( 'registrationadminapproval_email_subject' ),
	'require_admin_approval_free_user2_message'      => $this->GetOption( 'registrationadminapproval_email_message' ),

	'require_admin_approval_paid_notification_admin' => $this->GetOption( 'require_admin_approval_paid_notification_admin' ),
	'require_admin_approval_paid_admin_subject'      => $this->GetOption( 'requireadminapproval_admin_paid_subject' ),
	'require_admin_approval_paid_admin_message'      => $this->GetOption( 'requireadminapproval_admin_paid_message' ),

	'require_admin_approval_paid_notification_user1' => $this->GetOption( 'require_admin_approval_paid_notification_user1' ),
	'require_admin_approval_paid_user1_sender_name'  => $this->GetOption( 'email_sender_name' ),
	'require_admin_approval_paid_user1_sender_email' => $this->GetOption( 'email_sender_address' ),
	'require_admin_approval_paid_user1_subject'      => $this->GetOption( 'requireadminapproval_email_paid_subject' ),
	'require_admin_approval_paid_user1_message'      => $this->GetOption( 'requireadminapproval_email_paid_message' ),

	'require_admin_approval_paid_notification_user2' => $this->GetOption( 'require_admin_approval_paid_notification_user2' ),
	'require_admin_approval_paid_user2_sender_name'  => $this->GetOption( 'email_sender_name' ),
	'require_admin_approval_paid_user2_sender_email' => $this->GetOption( 'email_sender_address' ),
	'require_admin_approval_paid_user2_subject'      => $this->GetOption( 'registrationadminapproval_email_paid_subject' ),
	'require_admin_approval_paid_user2_message'      => $this->GetOption( 'registrationadminapproval_email_paid_message' ),

	'incomplete_notification'                        => $this->GetOption( 'incomplete_notification' ),
	'incomplete_start'                               => $WishListMemberInitialData['incomplete_notification_first'],
	'incomplete_start_type'                          => null,
	'incomplete_send_every'                          => $WishListMemberInitialData['incomplete_notification_add_every'],
	'incomplete_howmany'                             => $WishListMemberInitialData['incomplete_notification_add'],
	'incomplete_sender_name'                         => $this->GetOption( 'email_sender_name' ),
	'incomplete_sender_email'                        => $this->GetOption( 'email_sender_address' ),
	'incomplete_subject'                             => $this->GetOption( 'incnotification_email_subject' ),
	'incomplete_message'                             => $this->GetOption( 'incnotification_email_message' ),

	'newuser_notification_admin'                     => $this->GetOption( 'notify_admin_of_newuser' ),
	'newuser_admin_subject'                          => $this->GetOption( 'newmembernotice_email_subject' ),
	'newuser_admin_message'                          => $this->GetOption( 'newmembernotice_email_message' ),

	'newuser_notification_user'                      => $this->GetOption( 'newuser_notification_user' ),
	'newuser_user_sender_name'                       => $this->GetOption( 'email_sender_name' ),
	'newuser_user_sender_email'                      => $this->GetOption( 'email_sender_address' ),
	'newuser_user_subject'                           => $this->GetOption( 'register_email_subject' ),
	'newuser_user_message'                           => $this->GetOption( 'register_email_body' ),

	'expiring_notification_admin'                    => $this->GetOption( 'expiring_notification_admin' ),
	'expiring_admin_send'                            => $WishListMemberInitialData['expiring_notification_days'],
	'expiring_admin_subject'                         => $this->GetOption( 'expiring_admin_subject' ),
	'expiring_admin_message'                         => $this->GetOption( 'expiring_admin_message' ),

	'expiring_notification_user'                     => $this->GetOption( 'expiring_notification' ),
	'expiring_user_send'                             => $WishListMemberInitialData['expiring_notification_days'],
	'expiring_user_sender_name'                      => $this->GetOption( 'email_sender_name' ),
	'expiring_user_sender_email'                     => $this->GetOption( 'email_sender_address' ),
	'expiring_user_subject'                          => $this->GetOption( 'expiringnotification_email_subject' ),
	'expiring_user_message'                          => $this->GetOption( 'expiringnotification_email_message' ),

	'cancel_notification'                            => $this->GetOption( 'cancel_notification' ),
	'cancel_sender_name'                             => $this->GetOption( 'email_sender_name' ),
	'cancel_sender_email'                            => $this->GetOption( 'email_sender_address' ),
	'cancel_subject'                                 => $this->GetOption( 'cancel_email_subject' ),
	'cancel_message'                                 => $this->GetOption( 'cancel_email_message' ),

	'uncancel_notification'                          => $this->GetOption( 'uncancel_notification' ),
	'uncancel_sender_name'                           => $this->GetOption( 'email_sender_name' ),
	'uncancel_sender_email'                          => $this->GetOption( 'email_sender_address' ),
	'uncancel_subject'                               => $this->GetOption( 'uncancel_email_subject' ),
	'uncancel_message'                               => $this->GetOption( 'uncancel_email_message' ),
);
