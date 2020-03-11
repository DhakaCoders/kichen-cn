<?php

/*
 * GetResponse (API) Autoresponder Integration Functions
 * Original Author : Mike Lopez
 * Version: $Id: integration.autoresponder.getresponseapi.php 6535 2019-12-16 16:00:12Z mike $
 */

//$__classname__ = 'WLM_AUTORESPONDER_GETRESPONSEAPI';
//$__optionname__ = 'getresponseAPI';
//$__methodname__ = 'AutoResponderGetResponseAPI';

if (!class_exists('WLM_AUTORESPONDER_GETRESPONSEAPI')) {

	class WLM_AUTORESPONDER_GETRESPONSEAPI {

		function AutoResponderGetResponseAPI($that, $ar, $wpm_id, $email, $unsub = false) {
			global $wpdb;
			require_once $that->pluginDir . '/extlib/jsonRPCClient.php';
			require_once $that->pluginDir . '/extlib/wlm-getresponse-v3.php';

			if ($ar['campaign'][$wpm_id]) {
				$campaign = trim($ar['campaign'][$wpm_id]);
				$name = trim($that->ARSender['name']);
				$email = trim($that->ARSender['email']);
				$api_key = trim($ar['apikey']);
				$api_url = empty($ar['api_url'])? "https://api.getresponse.com/v3" : trim($ar['api_url']);
				$grUnsub = ($ar['grUnsub'][$wpm_id] == 1 ? true : false);

				$uid = $wpdb->get_var("SELECT ID FROM {$wpdb->users} WHERE `user_email`='" . esc_sql($that->ARSender['email']) . "'");
				$ip = trim($that->Get_UserMeta($uid, 'wpm_login_ip'));
				$ip = ($ip) ? $ip : trim($that->Get_UserMeta($uid, 'wpm_registration_ip'));
				$ip = ($ip) ? $ip : trim($_SERVER['REMOTE_ADDR']);

				try {
					if (!extension_loaded('curl') || !extension_loaded('json')) {
						# these extensions are a must
						throw new Exception("CURL and JSON are modules required to use"
								. " the GetResponse Integration");
					}

					if ( strpos($api_url, 'api2') === false) { //for V3 Users
						$api = new WLM_GETRESPONSE_V3($api_key,$api_url);
						$resp = $api->getCampaigns();
						if ( isset($resp->httpStatus) ) {
							throw new Exception("Unable to connect to API:" .$resp->message);
						}
						$cid = null;
						foreach ($resp as $i => $item) {
							if (strtolower($item->name) == strtolower($campaign)) {
								$cid = $item->campaignId;
							}
						}
						if (empty($cid)) {
							throw new Exception("Could not find campaign $campaign");
						}

						if ($unsub) {
							if ( $grUnsub ) {
								//list contacts
								$params = array(
									'query' => array('campaignId'=>$cid,'email'=>$email)
								);
								$contacts = $api->getContacts($params);
								$contacts = (array) $contacts;
								$contact = is_array($contacts) && isset($contacts[0]) ? $contacts[0] : false;
								if ( !$contact || !isset($contact->email) || !isset($contact->contactId) )  return; #could not find the contact, nothing to remove
								if ( $contact->email == $email ) {
									$params = array(
										'ipAddress' => $ip
									);
									$resp = $api->deleteContact( $contact->contactId,$params);
								}
							}
						} else {
							//CHECK FOR DUPLICATE, remove it for now to save api call
								// $params = array(
								// 	'query' => array('campaignId'=>$cid,'email'=>$email)
								// );
								// $contacts = $api->getContacts($params);
								// $contacts = (array) $contacts;
								// $contact = is_array($contacts) && isset($contacts[0]) ? $contacts[0] : false;
								// if ( $contact && isset($contact->email) && $contact->email == $email )  return; #duplicate
							$params = array(
								'name' => $name,
								'email' => $email,
								'campaign' => array('campaignId'=>$cid ),
								'dayOfCycle' => 0,
								'ipAddress' => $ip
							);
							$resp = $api->addContact($params);
						}
					} else { //for v2 Users
						$api = new jsonRPCClient($api_url);
						#get the campaign id
						$resp = $api->get_campaigns($api_key);
						$cid = null;
						if (!empty($resp)) {
							foreach ($resp as $i => $item) {
								if (strtolower($item['name']) == strtolower($campaign)) {
									$cid = $i;
								}
							}
						}
						if (empty($cid)) {
							throw new Exception("Could not find campaign $campaign");
						}

						if ($unsub) {
							if ($grUnsub) {
								//list contacts
								$contacts = $api->get_contacts(
										$api_key, array(
									'campaigns' => array($cid),
									'email' => array('EQUALS' => "$email")
										)
								);
								if (empty($contacts)) {
									#could not find the contact, nothing to remove
									return;
								}
								$pid = key($contacts);
								$res = $api->delete_contact($api_key, array('contact' => $pid));
								if (empty($res)) {
									throw new Exception("Empty server response while deleting contact");
								}
							}
						} else {

							// prepare data
							$data = array(
								'campaign' => trim( $cid ),
								'name' => trim( $name ),
								'email' => trim( $email ),
								'ip' => trim( $ip ),
								'cycle_day' => 0,
							);

							// remove empty items - getResponse don't like it
							$data = array_diff( $data, ['', null] );

							$resp = $api->add_contact( $api_key, $data );
							if (empty($resp)) {
								throw new Exception("Empty server response while sending");
							}
						}
					}
				} catch (Exception $e) {
					return;
				}
			}
		}

	}

}