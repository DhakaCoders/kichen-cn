<?php


if(extension_loaded('curl')) {
	global $WishListMemberInstance;
	include_once($WishListMemberInstance->pluginDir . '/extlib/paypal/PPAutoloader.php');
	PPAutoloader::register();
}

if (!class_exists('WLM_INTEGRATION_PAYPALEC')) {
	class WLM_INTEGRATION_PAYPALEC {
		private $settings;
		private $wlm;

		private $thankyou_url;
		private $pp_settings;
		public function __construct() {
			global $WishListMemberInstance;
			$this->wlm      = $WishListMemberInstance;
			$this->products = $this->wlm->GetOption('paypalecproducts');

			$settings           = $this->wlm->GetOption('paypalecthankyou_url');
			$paypalecthankyou  = $this->wlm->GetOption('paypalecthankyou');
			$this->thankyou_url = $this->wlm->make_thankyou_url( $paypalecthankyou );

			$this->cancel_url = $this->wlm->GetOption('paypalec_cancel_url');

			$pp_settings = $this->wlm->GetOption('paypalecsettings');


			$index = 'live';
			if($pp_settings['sandbox_mode']) {
				$index = 'sandbox';
			}

			$this->pp_settings = array(
				'acct1.UserName'  => $pp_settings[$index]['api_username'],
				'acct1.Password'  => $pp_settings[$index]['api_password'],
				'acct1.Signature' => $pp_settings[$index]['api_signature'],
				'mode'            => $pp_settings['sandbox_mode']? 'sandbox' : 'live',
				'gateway'         => $pp_settings['sandbox_mode']? 'https://www.sandbox.paypal.com' : 'https://www.paypal.com',
			);


		}
		public function paypalec($that) {
			$action = strtolower(trim($_GET['action']));

			switch ($action) {
				case 'purchase-express':
					try {
						$this->purchase_express($_GET['id']);
					} catch (Exception $e) {
					}

					break;
				case 'confirm':
					try {
						$this->confirm($_GET['id'], $_GET['token'], $_GET['PayerID']);
					} catch (Exception $e) {
					}

					break;
				case 'ipn':
					$this->ipn($_GET['id']);
				default:
					# code...
					break;
			}
		}
		public function ipn($id = null) {
			//$products = $this->products;
			//$product = $products[$id];


			$ipn_message = new PPIPNMessage(null, $this->pp_settings);
			$raw_data    = $ipn_message->getRawData();

			if(!$ipn_message->validate()) {
				return false;
			}

			foreach($raw_data as $key => $value) {
				//error_log("IPN: $key => $value");
			}
			//error_log("-----------------------------end ipn------------------------------");

			$txn_id           = isset($raw_data['parent_txn_id'])? $raw_data['parent_txn_id'] : $raw_data['txn_id'];
			$txn_id           = isset($raw_data['recurring_payment_id'])? $raw_data['recurring_payment_id'] : $txn_id;
			$_POST['sctxnid'] = $txn_id;

			switch ($raw_data['txn_type']) {
				//anything related to recurring, we follow
				//the profiles status
				case 'recurring_payment_profile_created':
				case 'subscr_signup':
				case 'recurring_payment':
				case 'recurring_payment_skipped':
				case 'subscr_modify':
				case 'subscr_payment':
				case 'recurring_payment_profile_cancel':
				case 'recurring_payment_expired':
				case 'recurring_payment_failed':
				case 'recurring_payment_suspended_due_to_max_failed_payment':
				case 'recurring_payment_suspended':
				case 'subscr_eot':

					switch ($raw_data['profile_status']) {
						case 'Active':
							$this->wlm->ShoppingCartReactivate();
							break;
						case 'Suspended':
						case 'Cancelled':

							//lets get the level id first so that we know if the settings is Cancel Immediately
							if ( ! isset( $_POST['wpm_id'] ) || is_null( $_POST['wpm_id'] ) || empty( $_POST['wpm_id'] ) ) {
								// get the user of this txnid
								$uid = $this->wlm->GetUserIDFromTxnID( $_POST['sctxnid'] );
								if ( ! $uid ) break; //let stop it!
								//get the levels who uses this txnid
								$levels = $this->wlm->GetMembershipLevelsTxnIDs( $uid, $_POST['sctxnid'] );
								if ( !is_array( $levels ) || count( $levels ) <= 0 ) break; //let stop it!
								$levels = array_keys( $levels );

								$_POST['wpm_id'] = $levels[0];
							}

							// If Cancel Membership Immediately is enabled for the level then cancel the level
							$paypalecimmediatecancel = $this->wlm->GetOption('paypalecsubscrcancel');
							if($paypalecimmediatecancel) $paypalecimmediatecancel = maybe_unserialize($paypalecimmediatecancel);
							else $paypalecimmediatecancel = array();

							if(!isset($paypalecimmediatecancel[wlm_arrval($_POST,'wpm_id')]) || (isset($paypalecimmediatecancel[wlm_arrval($_POST,'wpm_id')]) && $paypalecimmediatecancel[wlm_arrval($_POST,'wpm_id')] == 1)){
								$this->wlm->ShoppingCartDeactivate();
								return;
							}							

							// If Cancel Membership immediately is off then go try the eot settings
							$paypaleceotcancel = $this->wlm->GetOption('paypaleceotcancel');
							if($paypaleceotcancel) $paypaleceotcancel = maybe_unserialize($paypaleceotcancel);
							else $paypaleceotcancel = array();

							if(isset($paypaleceotcancel[wlm_arrval($_POST,'wpm_id')]) && $paypaleceotcancel[wlm_arrval($_POST,'wpm_id')] == 1){
								// This means that we cancel the level at end of Paypal subscription so only do this when 
								// we receive the IPN for EOT.
								if($raw_data['txn_type'] == 'subscr_eot')
									$this->wlm->ShoppingCartDeactivate();
							}
							break;

						default:
							break;
					}

					
					break;
				case 'subscr_cancel':

					// In case subscr_cancel is from Paypal Standard then use subscr_id as the txn_id in case $_POST['sctxnid']  is empty
					$_POST['sctxnid'] = isset($_POST['sctxnid'])? $_POST['sctxnid'] : $raw_data['subscr_id'];

					//lets cancel for trial subscriptions
					$paypalecsubscrcancel = $this->wlm->GetOption('paypalecsubscrcancel');
					if($paypalecsubscrcancel) $paypalecsubscrcancel = maybe_unserialize($paypalecsubscrcancel);
					else $paypalecsubscrcancel = false;

					if (isset($_POST['amount1']) && wlm_arrval($_POST,'amount1') == "0.00") {
						$this->wlm->ShoppingCartDeactivate();
					} elseif (isset($_POST['mc_amount1']) && wlm_arrval($_POST,'mc_amount1') == "0.00") {
						$this->wlm->ShoppingCartDeactivate();
					}elseif($paypalecsubscrcancel === false){ //default settings
						$this->wlm->ShoppingCartDeactivate();
					} else {
						//lets get the level id first so that we know if the settings is cancelled
						if ( ! isset( $_POST['wpm_id'] ) || is_null( $_POST['wpm_id'] ) || empty( $_POST['wpm_id'] ) ) {
							// get the user of this txnid
							$uid = $this->wlm->GetUserIDFromTxnID( $_POST['subscr_id'] );
							if ( ! $uid ) break; //let stop it!
							//get the levels who uses this txnid
							$levels = $this->wlm->GetMembershipLevelsTxnIDs( $uid, $_POST['subscr_id'] );
							if ( !is_array( $levels ) || count( $levels ) <= 0 ) break; //let stop it!
							$levels = array_keys( $levels );

							//if multiple levels is found using the txnid
							//lets check the name and amount to get the real level
							// -- needed for levels with child and parent
							$p = $this->wlm->GetOption('paypalecproducts');
							if ( count( $p ) >= 1 && count( $levels ) > 1 ) {
								//lets get the price and name
								$item_name = isset( $_POST['item_name'] ) ? $_POST['item_name'] : '';
								$item_amount = isset( $_POST['amount3'] ) ? $_POST['amount3'] : NULL;
								$item_amount = ( is_null( $item_amount ) && isset( $_POST['mc_amount3'] ) )  ? $_POST['mc_amount3'] : $item_amount;

								//lets check all products and make sure we process the recurring only
								foreach ($p as $key => $value) {
									if ( $value['recurring'] == "1" ) {
										//if their name and amount matches, we got our guy
										if ( $value['name'] == $item_name && $value['recur_amount'] == $item_amount  ) {
											$_POST['wpm_id'] = $value['sku'];
											break; //lets end the loop (only the loop not the switch)
										}
									}
								}
							}
							//still empty? lets use the first level we found
							if ( ! isset( $_POST['wpm_id'] ) || is_null( $_POST['wpm_id'] ) || empty( $_POST['wpm_id'] ) ) {
								$_POST['wpm_id'] = $levels[0];
							}
						}

						if ( isset( $paypalecsubscrcancel[ wlm_arrval($_POST,'wpm_id') ] ) && $paypalecsubscrcancel[wlm_arrval($_POST,'wpm_id')] == 1){
							$this->wlm->ShoppingCartDeactivate();
						}
					}
					break;
				case 'subscr_failed':
					switch ($raw_data['profile_status']) {
						case 'Active':
							$this->wlm->ShoppingCartReactivate();
							break;
						case 'Suspended':
						case 'Cancelled':
							$this->wlm->ShoppingCartDeactivate();
							break;
						default:
							//ignore
							break;
					}
					//were done
					return;
					break;
				case 'subscr_cancel':
					$_POST['sctxnid'] = isset($_POST['sctxnid'])? $_POST['sctxnid'] : $raw_data['subscr_id'];
					$this->wlm->ShoppingCartDeactivate();
					break;
			}

			// this is a one time payment
			switch($raw_data['payment_status']) {
				case 'Completed':
					if (isset($raw_data['echeck_time_processed'])) {
						$this->wlm->ShoppingCartReactivate(1);
					} else {
						$this->wlm->ShoppingCartRegistration(null, false);
						$this->wlm->CartIntegrationTerminate();
					}
					break;
				case 'Canceled-Reversal':
					$this->wlm->ShoppingCartReactivate();
					break;
				case 'Processed':
					$this->wlm->ShoppingCartReactivate('Confirm');
					break;
				case 'Expired':
				case 'Failed':
				case 'Refunded':
				case 'Reversed':
					$this->wlm->ShoppingCartDeactivate();
					break;

			}
		}
		public function confirm($id, $token, $payer_id) {
			$products = $this->products;
			$product = $products[$id];
			if(empty($product)) {
				return;
			}

			$paypal_service  = new PayPalAPIInterfaceServiceService($this->pp_settings);

			$ec_details_req_type = new GetExpressCheckoutDetailsRequestType($token);
			$ec_detail_req = new GetExpressCheckoutDetailsReq();

			$ec_detail_req->GetExpressCheckoutDetailsRequest = $ec_details_req_type;

			$ec_resp = $paypal_service->GetExpressCheckoutDetails($ec_detail_req);

			if(!$ec_resp && $ec_resp->Ack != 'Success') {
				throw new Exception("Paypal Request Failed");
			}

			//we now have the payer info
			$payer_info = $ec_resp->GetExpressCheckoutDetailsResponseDetails->PayerInfo;

			if($product['recurring']) {
				$order_total = new BasicAmountType($product['currency'], 0);
			} else {
				$order_total = new BasicAmountType($product['currency'], $product['amount']);
			}

			$payment_details             = new PaymentDetailsType();
			$payment_details->OrderTotal = $order_total;
			$payment_details->OrderDescription = $product['name'];
			$payment_details->NotifyURL  = $this->thankyou_url.'?action=ipn&id='.$id;

			$item_details                = new PaymentDetailsItemType();
			$item_details->Name          = $product['name'];;
			$item_details->Amount        = $product['amount'];
			$item_details->Quantity      = 1;

			$payment_details->PaymentDetailsItem[$i] = $item_details;

			$do_ec_details = new DoExpressCheckoutPaymentRequestDetailsType();
			$do_ec_details->PayerID = $payer_id;
			$do_ec_details->Token = $token;
			$do_ec_details->PaymentDetails[0] = $payment_details;

			$do_ec_request = new DoExpressCheckoutPaymentRequestType();
			$do_ec_request->DoExpressCheckoutPaymentRequestDetails = $do_ec_details;


			if($order_total->value > 0) {
				$do_ec = new DoExpressCheckoutPaymentReq();
				$do_ec->DoExpressCheckoutPaymentRequest = $do_ec_request;

				$do_ec_resp = $paypal_service->DoExpressCheckoutPayment($do_ec);
				if(!$do_ec_resp || $do_ec_resp->Ack != 'Success') {
					throw new Exception("Paypal Checkout Error Has Occured");
				}

				//we now have a payment info. Yeehaaa
				$payment_info = current($do_ec_resp->DoExpressCheckoutPaymentResponseDetails->PaymentInfo);

				$accept_statuses = array('Completed', 'In-Progress', 'Pending', 'Processed');
				if(!in_array($payment_info->PaymentStatus, $accept_statuses)) {
					throw new Exception("Paypal Payment Checkout Failed");
				}
			}


			if($product['recurring']) {
				//create a recurring payment profile
				$schedule_details = new ScheduleDetailsType();

				$schedule_details->MaxFailedPayments = $product['max_failed_payments'] ? $product['max_failed_payments'] : $product['max_failed_payments'];

				$payment_billing_period                   = new BillingPeriodDetailsType();
				$payment_billing_period->BillingFrequency = $product['recur_billing_frequency'];
				$payment_billing_period->BillingPeriod    = $product['recur_billing_period'];
				$payment_billing_period->Amount           = new BasicAmountType($product['currency'], $product['recur_amount']);
				if($product['recur_billing_cycles'] > 1) {
					$payment_billing_period->TotalBillingCycles = $product['recur_billing_cycles'];
				}
				$schedule_details->PaymentPeriod = $payment_billing_period;

				if($product['trial'] && is_numeric($product['trial_amount'])) {
					$trial_payment_billing_period                     = new BillingPeriodDetailsType();
					$trial_payment_billing_period->BillingFrequency   = $product['trial_recur_billing_frequency'];
					$trial_payment_billing_period->BillingPeriod      = $product['trial_recur_billing_period'];
					$trial_payment_billing_period->Amount             = new BasicAmountType($product['currency'], $product['trial_amount']);
					$trial_payment_billing_period->TotalBillingCycles = 1;
					$schedule_details->TrialPeriod                    = $trial_payment_billing_period;
				}

				$schedule_details->Description = wlm_paypal_create_description($product);

				$recur_profile_details = new RecurringPaymentsProfileDetailsType();
				// $recur_profile_details->BillingStartDate = date(DATE_ATOM, strtotime(sprintf("+%s %s", $product['recur_billing_frequency'], $product['recur_billing_period'])));
				$recur_profile_details->BillingStartDate = date(DATE_ATOM);

				$create_recur_paypay_profile_details = new CreateRecurringPaymentsProfileRequestDetailsType();
				$create_recur_paypay_profile_details->Token  = $token;
				$create_recur_paypay_profile_details->ScheduleDetails = $schedule_details;
				$create_recur_paypay_profile_details->RecurringPaymentsProfileDetails = $recur_profile_details;

				$create_recur_profile = new CreateRecurringPaymentsProfileRequestType();
				$create_recur_profile->CreateRecurringPaymentsProfileRequestDetails = $create_recur_paypay_profile_details;

				$create_recur_profile_req =  new CreateRecurringPaymentsProfileReq();
				$create_recur_profile_req->CreateRecurringPaymentsProfileRequest = $create_recur_profile;
				$create_profile_resp = $paypal_service->CreateRecurringPaymentsProfile($create_recur_profile_req);

				if(!$create_profile_resp || $create_profile_resp->Ack != 'Success') {
					throw new Exception("Could not create recurring profile");
				}
			}

			$address = array();
			$address['company']       = $payer_info->PayerBusiness;
			$address['address1']      = $payer_info->Address->Street1;
			$address['address2']      = $payer_info->Address->Street2;
			$address['city']          = $payer_info->Address->CityName;
			$address['state']         = $payer_info->Address->StateOrProvince;
			$address['zip']           = $payer_info->Address->PostalCode;
			$address['country']       = $payer_info->Address->CountryName;

			$_POST['wpm_useraddress'] = $address;
			$_POST['lastname']        = $payer_info->PayerName->LastName;
			$_POST['firstname']       = $payer_info->PayerName->FirstName;
			$_POST['action']          = 'wpm_register';
			$_POST['wpm_id']          = $product['sku'];
			$_POST['username']        = $payer_info->Payer;
			$_POST['email']           = $payer_info->Payer;
			$_POST['password1']       = $_POST['password2'] = $this->wlm->PassGen();
			$_POST['sctxnid']         = $product['recurring']? $create_profile_resp->CreateRecurringPaymentsProfileResponseDetails->ProfileID :
			$payment_info->TransactionID;

			$pending_statuses = array('In-Progress', 'Pending');
			if(in_array($payment_info->PaymentStatus, $pending_statuses) || $create_profile_resp->CreateRecurringPaymentsProfileResponseDetails->ProfileStatus == 'PendingProfile') {
				$this->wlm->ShoppingCartRegistration(null, null, 'Paypal Pending');
			} else {
				$this->wlm->ShoppingCartRegistration();
			}


		}
		public function purchase_express($id) {
			$products = $this->products;
			$product = $products[$id];
			if(empty($product)) {
				return;
			}

			$paypal_service  = new PayPalAPIInterfaceServiceService($this->pp_settings);
			$payment_details = new PaymentDetailsType();

			if($product['recurring']) {
				$item_details                                   = new PaymentDetailsItemType();
				$billing_agreement                              = new BillingAgreementDetailsType('RecurringPayments');
				$billing_agreement->BillingAgreementDescription = wlm_paypal_create_description($product);
			} else {
				$item_details                = new PaymentDetailsItemType();
				$item_details->Name          = $product['name'];
				$item_details->Amount        = $product['amount'];
				$item_details->Quantity      = 1;
				$payment_details->OrderTotal = new BasicAmountType($product['currency'], $product['amount']);
			}


			$payment_details->PaymentDetailsItem[$i] = $item_details;

			$ec_req_details                     = new SetExpressCheckoutRequestDetailsType();
			$ec_req_details->NoShipping         = empty($product['shipping']) ? 1 : 0;
			$ec_req_details->ReqConfirmShipping = 0;
			$ec_req_details->SolutionType       = 'Sole';
			$ec_req_details->ReturnURL          = $this->thankyou_url.'?action=confirm&id='.$id;

			if(!$this->cancel_url) 
				$this->cancel_url = get_bloginfo('url');
			
			$ec_req_details->CancelURL          = $this->cancel_url;
			$ec_req_details->LandingPage        = 'Billing';
			$ec_req_details->PaymentDetails[0]  = $payment_details;

			if(isset($billing_agreement)) {
				$ec_req_details->BillingAgreementDetails = array($billing_agreement);
			}

			$ec_req_type = new SetExpressCheckoutRequestType();
			$ec_req_type->SetExpressCheckoutRequestDetails = $ec_req_details;

			$ec_req = new SetExpressCheckoutReq();
			$ec_req->SetExpressCheckoutRequest = $ec_req_type;

			$ec_res = $paypal_service->SetExpressCheckout($ec_req);

			if($ec_res && $ec_res->Ack == 'Success') {
				if(!empty($_GET['spb'])) {
					wp_send_json(array('token' => $ec_res->Token));
				}
				$next_loc = sprintf("%s/webscr?cmd=_express-checkout&useraction=commit&token=%s", $this->pp_settings['gateway'], $ec_res->Token);
				wp_redirect($next_loc);
				die();
			} else {
				//var_dump($ec_res);
			}

		}
	}
}
