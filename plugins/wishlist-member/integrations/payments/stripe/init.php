<?php // initialization

define( 'MAX_PLAN_COUNT', 999 );
define( 'MAX_PROD_COUNT', 999 );

if(!class_exists('WLM3_Stripe_Hooks')) {
	class WLM3_Stripe_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			
			add_action('wp_ajax_wlm3_stripe_test_keys', array($this, 'test_keys'));
		}
		function test_keys() {
			extract ($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
			);
			if ( !empty($stripeapikey) ) {
				try {

					$status = WLMStripe\WLM_Stripe::setApiKey($stripeapikey);
					$plans = WLMStripe\Plan::all(array('count' => MAX_PLAN_COUNT));
					$_products = WLMStripe\Product::all(array('count' => MAX_PROD_COUNT));
					$products = array();
					foreach($_products->data AS $product) {
						$products[$product->id] = $product->name;
					}

					$api_type = strpos($stripeapikey, "test") === false ? "LIVE" : "TEST";
					$data['message'] = $api_type;
					$data['status'] = true;

					$data['data']['plan_options'] = array();

					foreach ( $plans->data as $plan ) {
						$interval = $plan['interval'];
						if($plan['interval_count'] <> 1) {
							$interval = sprintf('%d %ss', $plan['interval_count'], $interval);
						}
						$text = sprintf( '%s - %s (%s %s / %s)', $products[$plan['product']], $plan['nickname'] ?: $plan['id'], strtoupper($plan['currency']), number_format($plan['amount'] / 100, 2, '.', ','), $interval);

						$data['data']['plan_options'][] = array(
							'value' => $plan['id'],
							'id' => $plan['id'],
							'text' => $text,
						);
					}

					$data['data']['plans'] = $plans;

					if($save) {
						$this->wlm->SaveOption('stripeapikey', $stripeapikey);
						$this->wlm->SaveOption('stripepublishablekey', $stripepublishablekey);
					}
				} catch (Exception $e) {
					$data['message'] = $e->getMessage();
				}
			} else {
				$data['message'] = 'No Stripe Secret Key';
			}
			wp_die(json_encode($data));
		}
	}
	new WLM3_Stripe_Hooks;
}
