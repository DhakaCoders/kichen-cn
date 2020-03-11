<?php

// common stuff for all 3 paypal integrations

global $wlm_paypal_buttons;
$wlm_paypal_buttons = array(
	'pp_pay:l'      => 'https://www.paypalobjects.com/webstatic/en_AU/i/buttons/btn_paywith_primary_l.png',
	'pp_pay:m'      => 'https://www.paypalobjects.com/webstatic/en_AU/i/buttons/btn_paywith_primary_m.png',
	'pp_pay:s'      => 'https://www.paypalobjects.com/webstatic/en_AU/i/buttons/btn_paywith_primary_s.png',
	'pp_buy:l'      => 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-large.png',
	'pp_buy:m'      => 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png',
	'pp_buy:s'      => 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-small.png',
	'pp_checkout:l' => 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-large.png',
	'pp_checkout:m' => 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png',
	'pp_checkout:s' => 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-small.png',
);

function wlm_paypal_create_description($product, $with_name = true) {
	$description = '';
	if($with_name) {
		$description = $product['name'] . ' (';
	}
	if($product['trial'] && $product['trial_amount']) {
		$description .= sprintf(__("%s %0.2f for the first %d %s%s\nthen ", 'wishlist-member'), $product['currency'], $product['trial_amount'], $product['trial_recur_billing_frequency'], strtolower($product['trial_recur_billing_period']), $product['trial_recur_billing_frequency'] > 1 ? 's' : '');
	}
	if ( $product['payflow_recur_pay_period'] ) {
		switch ($product['payflow_recur_pay_period']) {
			case 'DAY':
				$product_pay_period = 'DAY';
				break;
			case 'WEEK':
				$product_pay_period = 'WEEK';
				break;
			case 'BIWK':
				$product_pay_period = 'TWO WEEKS';
				break;
			case 'MONT':
				$product_pay_period = 'MONTH';
				break;			
			case 'FRWK':
				$product_pay_period = 'FOUR WEEKS';			
				break;
			case 'QTER':
				$product_pay_period = 'QUARTER';
				break;
			case 'SMYR':
				$product_pay_period = 'TWICE EVERY YEAR';				
				break;				
			case 'SMMO':
				$product_pay_period = 'TWICE EVERY MONTH';				
				break;
			case 'YEAR':
				$product_pay_period = 'YEAR';
				break;
		}
			if ( $product['payflow_recur_pay_period'] == 'SMYR' || $product['payflow_recur_pay_period'] == 'SMMO' ) {
			    $description .= sprintf(__('%s %0.2f %d %s%s','wishlist-member'), $product['currency'], $product['recur_amount'], $product['recur_billing_frequency'], strtolower($product_pay_period), $product['recur_billing_frequency'] > 1 ? 's' : '');		
			} else {
			    $description .= sprintf(__('%s %0.2f every %d %s%s','wishlist-member'), $product['currency'], $product['recur_amount'], $product['recur_billing_frequency'], strtolower($product_pay_period), $product['recur_billing_frequency'] > 1 ? 's' : '');
			}
			
		
	} else {
		$description .= sprintf(__('%s %0.2f every %d %s%s','wishlist-member'), $product['currency'], $product['recur_amount'], $product['recur_billing_frequency'], strtolower($product['recur_billing_period']), $product['recur_billing_frequency'] > 1 ? 's' : '');
	}
	if($product['recur_billing_cycles'] > 1) {
		$description .= sprintf(__("\nfor %d installments",'wishlist-member'), $product['recur_billing_cycles']);
	}
	if($with_name) {
		$description .= ')';
	}
	return str_replace(' 1 ',' ', $description);
}
