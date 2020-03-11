<?php

/*
 * ConvertKit Autoresponder Integration Init Functions
 * Original Author : Fel Jun Palawan
 * Version: $Id: integration.autoresponder.convertkit.init.php 3144 2016-11-15 15:06:52Z feljun $
 */
if ( ! class_exists('cksdk') ) {
	include_once( $this->pluginDir . '/extlib/convertkit/cksdk.php' );
}
if (!class_exists('WLM_AUTORESPONDER_CONVERTKIT_INIT')) {

	class WLM_AUTORESPONDER_CONVERTKIT_INIT {
		/* This is the required function, this is being called by ARSubscibe, function name should be the same with $__methodname__ variable above */

		function convertkitProcessQueue($recnum = 10,$tries = 5){
			$WishlistAPIQueueInstance = new WishlistAPIQueue;
			$last_process = get_option("WLM_ConvertKitAPI_LastProcess");
			$current_time = time();
			$tries = $tries > 1 ? (int)$tries:5;
			$error = false;
			if ( ! $last_process || ( $current_time - $last_process ) > 5 ){
				$queues = $WishlistAPIQueueInstance->get_queue("convertkit",$recnum,$tries,"tries,name");
				foreach ( $queues as $queue ) {
					$data = maybe_unserialize( $queue->value );
					if ( $data['action'] == 'subscribe' ) {
						$res = $this->ck_form_subscribe( $data['apisecret'], $data['formid'], $data['email'], $data['name'], $data['last_name'] );
					}elseif( $data['action'] == 'unsubscribe' ){
						$res = $this->ck_unsubscribe( $data['apisecret'], $data['email'] );
					}

					if ( $res !== true ) {
						$d = array(
							'notes'=> $res,
							'tries'=> $queue->tries + 1
						);
						$WishlistAPIQueueInstance->update_queue( $queue->ID, $d );
						$error = true;
					} else {
						$WishlistAPIQueueInstance->delete_queue( $queue->ID );
						$error = false;
					}
				}
				//save the last processing time when error has occured on last transaction
				if ( $error ) {
					$current_time = time();
					if ( $last_process ) {
						update_option( "WLM_ConvertKitAPI_LastProcess", $current_time );
					} else {
						add_option( "WLM_ConvertKitAPI_LastProcess", $current_time );
					}
				}
			}
		}

		function ck_form_subscribe( $api_secret, $formid, $email, $name , $last_name ) {
			$ck = new cksdk( $api_secret );
			if ( $ck->last_error != "" ) return $ck->last_error;
			$args = array(
			    'email' => $email,
			    'first_name' => $name,
			    'fields'=>array( 'last_name' => $last_name),
			);
			$f = $ck->form_subscribe( $formid, $args );
			if ( !$f ) {
				return $ck->last_error;
			}
			return true;
		}

		function ck_unsubscribe( $api_secret, $email ) {
			$ck = new cksdk( $api_secret );
			if ( $ck->last_error != "" ) return $ck->last_error;
			$f = $ck->form_unsubscribe( $email );
			if ( !$f ) {
				return $ck->last_error;
			}
			return true;
		}

		function ck_get_forms( $api_secret ) {
			$forms = array();
			$ck = new cksdk( $api_secret );
			if ( $ck->last_error != "" ) return $forms;
			$f = $ck->get_forms();
			if ( $f && isset( $f['forms'] ) ) {
				$f = $f['forms'];
				foreach ( $f as $key => $value ) {
					$forms[] = array(
						"id" => $value['id'],
						"name" => $value['name'],
					);
				}
			}
			return $forms;
		}
		/* End of Functions */
	}
}
$WLM_AUTORESPONDER_CONVERTKIT_INIT = new WLM_AUTORESPONDER_CONVERTKIT_INIT;