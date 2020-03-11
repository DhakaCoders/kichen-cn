<?php

/*
 * ConvertKit Autoresponder Integration Functions
 * Original Author : Fel Jun Palawan
 * Version: $Id: integration.autoresponder.convertkit.php 3149 2016-11-28 15:24:43Z feljun $
 */


if (!class_exists('WLM_AUTORESPONDER_CONVERTKIT')) {

	class WLM_AUTORESPONDER_CONVERTKIT {
		/* This is the required function, this is being called by ARSubscibe, function name should be the same with $__methodname__ variable above */

		function AutoResponderConvertKit($that, $ar, $wpm_id, $email, $unsub = false) {
			$ckformid = $ar['ckformid'][$wpm_id]; // get the list ID of the Membership Level
			$ckapi = $ar['ckapi']; // get the CONVERTKIT API

			$WishlistAPIQueueInstance = new WishlistAPIQueue;
			$WLM_AUTORESPONDER_CONVERTKIT_INIT = new WLM_AUTORESPONDER_CONVERTKIT_INIT;

			$data = array();
			if ( $unsub ) {
				$ckOnRemCan = isset( $ar['ckOnRemCan'][$wpm_id] ) ? $ar['ckOnRemCan'][$wpm_id] : "";
				if ( $ckOnRemCan == "unsub" ) {
					$data = array(
						"apisecret"=> $ckapi,
						"action"=>"unsubscribe",
						"email"=>$email,
					);
				}
			}else {
				if ( $ckformid ) {
					$data = array(
						"apisecret"=> $ckapi,
						"action"=>"subscribe",
						"formid"=> $ckformid,
						"email"=>$email,
						"name"=> $that->ARSender['first_name'],
                                                "last_name"=>$that->ARSender['last_name'],
					);
				}
			}
			if ( $data ) {
				$qname = "convertkit_" .time();
				$data = maybe_serialize($data);
				$WishlistAPIQueueInstance->add_queue( $qname, $data, "For Queueing" );
				$WLM_AUTORESPONDER_CONVERTKIT_INIT->convertkitProcessQueue();
			}
		}

		/* End of Functions */
	}

}