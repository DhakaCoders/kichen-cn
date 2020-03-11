<?php

class WishListCouponXHRHandler extends WishListXHRHandler {
	public function __construct($plugin_instance) {
		parent::__construct($plugin_instance);

		//
		add_action('wp_ajax_wishlist_coupon_apply', array($this, 'apply'));
		add_action('wp_ajax_nopriv_wishlist_coupon_apply', array($this, 'apply'));

		add_action('wp_ajax_wishlist_coupon_update_coupon', array($this, 'update_coupon'));
		add_action('wp_ajax_wishlist_coupon_update_promotion', array($this, 'update_promotion'));

		//backbone handlers
		add_action('wp_ajax_wishlist_coupon_backbone_coupon_list', array($this, 'coupon_list'));
		add_action('wp_ajax_wishlist_coupon_backbone_promotion_coupons', array($this, 'coupon_actions'));
		add_action('wp_ajax_wishlist_coupon_backbone_promotions', array($this, 'promotion_actions'));
	}
	public function apply() {
		extract($_POST);
		$status = $this->plugin_instance->get_wldb()->validate_coupon($code, $id);
		$status = json_encode($status);
		die($status);
	}

	public function update_coupon() {
		$this->plugin_instance->get_wldb()->update_coupon($_POST['id'], $_POST);
		die();
	}

	public function update_promotion() {
		$this->plugin_instance->get_wldb()->update_promotion($_POST['id'], $_POST);
		die();
	}


	//backbone handlers
	public function coupon_list() {
		$result = (array) $this->plugin_instance->get_wldb()->get_coupons($_GET['promotion_id']);
		echo json_encode($result);
		die();
	}

	public function coupon_actions() {
		$model = $_POST['model'];
		$model = stripslashes_deep($model);
		$obj = json_decode($model);

		switch($_POST['_method']) {
			case 'DELETE':
				$this->plugin_instance->get_wldb()->delete_coupon($_GET['id']);
				die();
				break;
			case 'PUT':
				$obj = (array) $obj;
				$this->plugin_instance->get_wldb()->update_coupon($obj['coupon_id'], $obj);
				echo json_encode($this->plugin_instance->get_wldb()->get_coupon($obj['coupon_id']));
				die();
			default:
				//create
				$obj = (array) $obj;
				if(empty($obj['coupon_code'])) {
					$obj['coupon_code'] = strtoupper(substr(md5(time()), 0 , 7));
				}

				$t  = time();
				$t2 = $t + (3600 * 24 * 7);
				$obj = array_merge($obj, array(
					'valid_num_tries'           => 0,
					'valid_num_tries_remaining' => 0
				));
				$new_obj = $this->plugin_instance->get_wldb()->create_coupon($obj);
				echo json_encode($new_obj);
				die();
		}

	}
	public function promotion_actions() {
		$model = $_POST['model'];
		$model = stripslashes_deep($model);
		$obj = json_decode($model);

		switch($_POST['_method']) {
			case 'DELETE':
				$this->plugin_instance->get_wldb()->delete_promotion($_GET['id']);
				die();
				break;
			case 'PUT':
				$obj = (array) $obj;
				$this->plugin_instance->get_wldb()->update_promotion($obj['id'], $obj);
				echo json_encode($obj);
				die();
			default:
				//create
				$obj = (array) $obj;
				$obj = array_merge($obj, array(
					'name'              => 'A New Promotion',
					'label'				=> 'Enter Coupon Code:',
					'valid_text'        => 'Coupon Applied',
					'invalid_text'      => 'Invalid Coupon',
					'apply_button_text' => 'APPLY',
					'pay_button_text'   => 'BUY NOW',
					'style'             => 'Basic'
				));
				$new_obj = $this->plugin_instance->get_wldb()->create_promotion($obj);
				echo json_encode($new_obj);
				die();
		}

	}
}