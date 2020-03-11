<?php

/*
 * Generic2 Integration Functions
 * Original Author : Mike
 * Version: $Id$
 */

//$__classname__ = 'WLM_AUTORESPONDER_GENERIC2';
//$__optionname__ = 'generic2';
//$__methodname__ = 'AutoResponderGeneric2';

if (!class_exists('WLM_AUTORESPONDER_GENERIC2')) {

	class WLM_AUTORESPONDER_GENERIC2 {

		function AutoResponderGeneric2($that, $ar, $wpm_id, $email, $unsub = false) {
			if (function_exists('curl_init')) {
				$postURL = $ar['postURL'][$wpm_id];
				$arUnsub = ($ar['arUnsub'][$wpm_id] == 1 ? true : false);
				if ($postURL) {
					$emailAddress = $that->ARSender['email'];
					list($fName, $lName) = explode(" ", $that->ARSender['name'], 2); //split the name into First and Last Name
					$httpAgent = "WLM_GENERIC_AGENT";
					$postData = array(
						"email_address" => $emailAddress,
						"first_name" => $fName,
						"last_name" => $lName
					);
					if ($unsub) {
						if ($arUnsub) {
							$postData["unsubscribe"] = 1;
						}
					} else {
						$postData["unsubscribe"] = 0;
					}

					if (isset($postData["unsubscribe"])) {
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_USERAGENT, $httpAgent);
						curl_setopt($ch, CURLOPT_URL, $postURL);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
						curl_exec($ch);
						curl_close($ch);
					}
				}
			}
		}

	}

}
?>
