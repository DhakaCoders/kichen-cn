<?php

/*
  Plugin Name: WishList Coupon 2.0
  Plugin URI: http://customers.wishlistproducts.com/buy-plugins/
  Description: WishList Coupon 2.0 allows you to offer discount coupons which can be applied when users purchase membership levels. These coupons can be customized according to your needs.
  Version: 2.0.25
  Author: WishList Products
  Author URI: http://wishlistproducts.com/support-options/
  Text Domain: wishlist-coupon20
 */


require_once dirname(__FILE__) .'/lib/wishlist-framework/WishListPlugin.php';
require_once dirname(__FILE__) .'/lib/WishListCouponDb.php';
require_once dirname(__FILE__) .'/lib/WishListCouponXHRHandler.php';

define("TRCK_STATUS_APPLY", 1);
define("TRCK_STATUS_VRFYD", 3);
define("TRCK_STATUS_COMPL", 5);
define("TRCK_STATUS_FINSH", 7);

class WishListCoupon20 extends WishListPlugin {
	public function __construct($file, $slug, $sku, $name, $link_name, $prefix, $require_wlm) {
		parent::__construct($file, $slug, $sku, $name, $link_name, $prefix, $require_wlm);

		session_start();

		$this->add_menu( 'promotions', 'Promotions', 'promotions.php');
		$this->add_menu( 'reports', 'Reports', 'reports.php');
		$this->add_menu( 'styles', 'Styles', 'styles.php');
		$this->add_menu( 'settings', 'Settings', 'settings.php');


		//inject our custom db
		$this->wldb        = new WishListCouponDb($this->prefix, $this);
		//inject our custom xhr handler
		$this->xhr_handler = new WishListCouponXHRHandler($this);


		add_shortcode('wishlist_coupon',  array($this, 'shortcode_wishlist_coupon'));
		add_shortcode('wishlist_coupon_tracking_code', array($this, 'shortcode_wishlist_coupon_tracking_code'));
		add_action('init', array($this, 'init_shortcode_buttons'));
		add_action('init', array($this, 'init_plugin'));
		add_action('init', array($this, 'init'));
		add_action('init', array($this, 'admin_init'));
		add_action('wp', array($this, 'check_finalize'));
		add_action('wp_head', array($this, 'wp_head'));
		add_action('admin_head', array($this, 'wp_head'));
		add_action('wishlistcoupon_save_options', array($this, 'post_save_options'), 10);
	}

	//save_options post processing
	//this is where we add the tracking script to internal posts/pages
	public function post_save_options($options) {
		$options['tracking_pages'] = is_array($options['tracking_pages'])? $options['tracking_pages'] : array();
		$options['tracking_posts'] = is_array($options['tracking_posts'])? $options['tracking_posts'] : array();
		$posts = array_merge($options['tracking_pages'], $options['tracking_posts']);


		$posts = query_posts(array(
				'post__in'       => $posts,
				'post_type'      => array('post', 'page'),
				'posts_per_page' => -1
			)
		);


		foreach($posts as $p) {
			if(stripos($p->post_content, '[wishlist_coupon_tracking_code]') === false) {
				$update = array(
					'ID'           => $p->ID,
					'post_content' => $p->post_content . "\n[wishlist_coupon_tracking_code]"
				);
				wp_update_post($update);
			}
		}
	}
	public function admin_init() {

		if(!isset($_REQUEST[$this->class_name.'Action'])) {
			return;
		}

		$action = $_REQUEST[$this->class_name.'Action'];

		switch ($action) {
			case 'create_style':
				if(!empty($_POST['id'])) {
					//update style
					$status = $this->wldb->update_style($_POST['id'], $_POST, $_FILES);
					$redirect_to = $_POST['redirect_to'];
					if(!$status) {
						$redirect_to .= '&status=fail&act=updatefail';
					} else {
						$redirect_to .= '&status=ok&act=updated';
					}
					wp_redirect($redirect_to);
					die();
				}
				$status = $this->wldb->create_style($_POST, $_FILES);
				$redirect_to = $_POST['redirect_to'];
				if(!$status) {
					$redirect_to .= '&status=fail&act=createfail';
				} else {
					$redirect_to .= '&status=ok&act=created';
				}
				wp_redirect($redirect_to);
				die();
				break;
			case 'remove_style':
				$this->wldb->remove_style($_GET['id']);
				$redirect_to = $_GET['redirect_to'];
				$redirect_to .= '&status=ok&act=removed';
				wp_redirect($redirect_to );
				die();
			case 'clone_style':
				$id = $this->wldb->clone_style($_GET['id']);
				$redirect_to = $_GET['redirect_to'];
				$redirect_to .= '&status=ok&act=cloned&id='.preg_replace('/\W/', '_', $id);
				wp_redirect($redirect_to );
				die();
			default:

				# code...
				break;
		}


	}
	public function show_conversion_js() {
		header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
		header("Pragma: no-cache");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

		$coupon_id = $_COOKIE['WPCPN_LAST_CPN'];
		if(empty($coupon_id)) {
			return;
		}
?>
<?php
		die();
	}

	//runs on 'wp' action so that we already have the idea of the current post/page
	public function check_finalize() {
		global $post;
		if(!empty($_COOKIE['WPCPN_LAST_CPN'])) {
			$tracking_pages = $this->get_option('tracking_pages');
			$tracking_posts = $this->get_option('tracking_posts');
			if(in_array($post->ID, (array) $tracking_pages) || in_array($post->ID, (array) $tracking_posts)) {
				$this->set_tracking_status(TRCK_STATUS_FINSH, $_COOKIE['WPCPN_LAST_CPN']);
				$this->wldb->redeem_coupon($coupon_id);
			}
		}
	}
	public function init() {
		//$this->pre_upgrade(true, array('plugin' => 'wishlist-coupon/wishlist-coupon.php'));
		$this->post_upgrade(true, array('plugin' => 'wishlist-coupon/wishlist-coupon.php'));

		//show conversion js if needed
                $act = (isset($_GET['act'])) ? $act : '';
		if($act == 'conversion.js') {
			$this->show_conversion_js();
		}

		if(!isset($_GET['wpcpn'])) {
			return;
		}

		if($_GET['wpcpn'] != 1) {
			return;
		}


		$status = $this->wldb->validate_coupon($_GET['code'], $_GET['id']);

		if($status['status'] === true) {
			$coupon = $this->wldb->get_coupon($status['cpnid']);
			$this->set_tracking_status(TRCK_STATUS_VRFYD, $status['cpnid']);
			$this->set_tracking_status(TRCK_STATUS_COMPL, $status['cpnid']);
			wp_redirect($coupon->payment_link);
			die();
		}

		$promotion = $this->wldb->get_promotion($_GET['id']);
		wp_redirect($promotion->default_payment_link);
		die();
	}
	public function init_plugin() {
		wp_enqueue_script('json2');

		if(is_admin()) {
			wp_enqueue_script('jquery-ui-datepicker');
			if (isset($_GET['page']) && $_GET['page'] == 'WishListCoupon20') { 
				wp_enqueue_script('underscore-wlc2', $this->plugin_url . 'assets/js/underscore.min.js', array('underscore'), $this->include_ver);
			}
			wp_enqueue_script('jquery.jcarousel', $this->plugin_url .'assets/js/jquery.jcarousel.min.js', array('jquery'), '1.0', true);
			wp_enqueue_script('wishlist-coupon-admin-bb-js', $this->plugin_url .'assets/js/backbone/admin.js', array('jquery', 'backbone', 'jquery-ui-datepicker', 'jquery.jcarousel'), '1.0', true);
			wp_enqueue_script('chosen.jquery', $this->plugin_url .'assets/js/chosen.jquery.min.js', array('jquery'));
			wp_enqueue_script('jquery.validate', $this->plugin_url .'assets/js/jquery.validate.min.js', array('jquery'));

			wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
			wp_enqueue_style('media-views');
			wp_enqueue_style('chosen.jquery', $this->plugin_url.'assets/css/chosen.min.css');

		} else {
			wp_enqueue_script('wishlist-coupon-js', $this->plugin_url .'assets/js/wishlist-coupon.js', array('jquery', 'json2'), '1.0', true);
		}
		wp_enqueue_style('wishlist-coupon-css', $this->plugin_url .'assets/css/wishlist-coupon.css', array(), '1.0');
	}
	public function init_shortcode_buttons() {
		if(!is_admin()) {
			return;
		}
		$coupons = $this->wldb->get_promotions();
		foreach($coupons as $c) {
			$this->tmce->add_shortcode_btn($this->name, $c->name,
				sprintf("[wishlist_coupon id=%s]", $c->id)
			);
		}
	}

	public function shortcode_wishlist_coupon_tracking_code() {
		return $this->create_tracking_js();
	}
	public  function shortcode_wishlist_coupon( $atts ) {
		$atts = extract( shortcode_atts( array( 'id'=>'null' ),$atts ) );

		$promo = $this->wldb->get_promotion($id);
		$style  = empty($promo->style)? 'default' : $promo->style;
		$styles = $this->wldb->get_available_styles();
		$style = $styles[$style];

		if(empty($promo)) {
			return _e("This coupon does not exist", "wishlist-coupon");
		}


		ob_start();
		include $style['filename'];
		$str = ob_get_clean();

		$str = str_replace('[wishlist_coupon_content]', sprintf('<p id="wishlist-coupon-content-%s" class="wishlist-coupon-grp-%s wishlist-coupon-content">%s</p>', $promo->id, $promo->id, $promo->content), $str);
		$str = str_replace('[wishlist_coupon_code]', sprintf('<input type="text" id="wishlist-coupon-code-%s" class="wishlist-coupon-grp-%s wishlist-coupon-code"/>', $promo->id, $promo->id), $str);
		$str = str_replace('[wishlist_coupon_apply]', sprintf('<button id="wishlist-coupon-btn-apply-%s" class="wishlist-coupon-grp-%s wishlist-coupon-btn-apply">[wishlist_coupon_button_text]</button>', $promo->id, $promo->id), $str);
		$str = str_replace('[wishlist_coupon_pay]', sprintf('<button id="wishlist-coupon-btn-pay-%s" class="wishlist-coupon-grp-%s wishlist-coupon-btn-pay">[wishlist_coupon_pay_text]</button>', $promo->id, $promo->id), $str);
		$str = str_replace('[wishlist_coupon_alert]', sprintf('<span id="wishlist-coupon-alert-%s" class="wishlist-coupon-grp-%s wishlist-coupon-alert"></span>', $promo->id, $promo->id), $str);
		$str = str_replace('[wishlist_coupon_success]', sprintf('<span id="wishlist-coupon-success-%s" class="wishlist-coupon-grp-%s wishlist-coupon-success"></span>', $promo->id, $promo->id), $str);
		$str = str_replace('[wishlist_coupon_button_text]', $promo->apply_button_text, $str);
		$str = str_replace('[wishlist_coupon_pay_text]', $promo->pay_button_text, $str);
		$str = str_replace('[wishlist_coupon_label]', $promo->label, $str);
		$str = str_replace('[id]', 'promo-'.$promo->id, $str);
		return $str;
	}

	public function set_tracking_status($status, $coupon_id) {
		$cookie_name = 'wlcpnstatus_' . $coupon_id;
		$current_status = json_decode(stripslashes($_COOKIE[$cookie_name]));
		error_log($cookie_name);
		error_log(print_r($current_status, true));
		$cookie_val = json_encode(array('status' => $status, 'id' => $coupon_id));
		$this->wldb->update_tracking_status($status, $coupon_id);
		switch($status) {
			case TRCK_STATUS_APPLY:
				error_log(sprintf("coupon %s is applied", $coupon_id));
				if($current_status->status >= TRCK_STATUS_FINSH) {
					error_log(sprintf("trying to apply a fin coupon", $coupon_id));
					return false;
				}
				setcookie( $cookie_name, $cookie_val,  time() + (60*60*24*365*2), COOKIEPATH, COOKIE_DOMAIN);
				break;
			case TRCK_STATUS_VRFYD:
				error_log(sprintf("coupon %s is verified", $coupon_id));
				setcookie( 'WPCPN_LAST_CPN', $coupon_id,  time() + (60*60*24*365*2), COOKIEPATH, COOKIE_DOMAIN);
				break;
			case TRCK_STATUS_COMPL:
				error_log(sprintf("coupon %s is completed", $coupon_id));
				setcookie( $cookie_name, $cookie_val,  time() + (60*60*24*365*2), COOKIEPATH, COOKIE_DOMAIN);
				break;
			case TRCK_STATUS_FINSH:
				if($current_status->status < TRCK_STATUS_COMPL) {
					return false;
				}
				error_log(sprintf("coupon %s is finished", $coupon_id));
				setcookie( $cookie_name, $cookie_val,  time() + (60*60*24*365*2), COOKIEPATH, COOKIE_DOMAIN);
				//setcookie( 'WPCPN_LAST_CPN', null, -1 , COOKIEPATH, COOKIE_DOMAIN);
				break;

		}
		return true;
	}

	public function encodeURIComponent($str) {
    	$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    	return strtr(rawurlencode($str), $revert);
	}
	public function create_tracking_js() {
		$site_url = get_bloginfo('siteurl');
		$inner = $this->encodeURIComponent('<script src="'. get_bloginfo('siteurl'). '/index.php?wpcpn=1&act=conversion.js" type="text/javascript"></script>');
$script = <<<php
<script type="text/javascript">
document.write(unescape("$inner"));
</script>
php;
		return $script;
	}
	public function wp_head() {
		$perm = get_permalink();
		if(stripos($perm, '?') === false) {
			$perm .= '?';
		}
		$perm .= '&wpcpn=1&redirect_to='.urlencode(get_permalink());
		?>
		<script type="text/javascript">
		wishlist_coupon_redirect_to = '<?php echo $perm ?>';
		wpcpn_calendar_img = '<?php echo $this->plugin_url?>assets/images/calendar.gif';
		</script>
<?php
	}

	function pre_upgrade($return, $plugin) {
		$plugin = (isset($plugin['plugin'])) ? $plugin['plugin'] : '';
		if ($plugin == $this->plugin_file) {
			$dir = sys_get_temp_dir() . '/' . $this->class_name . '-upgrade';
			$this->recursive_delete($dir);
			$this->recursive_copy($this->plugin_dir . '/res/styles', $dir . '/res/styles');
		}
		return $return;
	}

	function post_upgrade($return, $plugin) {
		$plugin = (isset($plugin['plugin'])) ? $plugin['plugin'] : '';
		if ($plugin == $this->plugin_file) {
			$dir = sys_get_temp_dir() . '/' . $this->class_name . '-upgrade';
			$this->recursive_copy($this->plugin_dir . '/res/styles', $dir . '/res/styles');
			$this->recursive_copy($dir . '/res/styles', $this->plugin_dir . '/res/styles');
			$this->recursive_delete($dir);
		}
		return $return;
	}
	/**
	 * Deletes an entire directory tree
	 * @param string $dir Folder Name
	 */
	function recursive_delete($dir) {
		if (substr($dir, -1) != '/')
			$dir.='/';
		$files = glob($dir . '*', GLOB_MARK);
		foreach ($files AS $file) {
			if (is_dir($file)) {
				$this->recursive_delete($file);
				rmdir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dir);
	}

	function recursive_copy($source, $dest) {
		if (substr($source, -1) != '/')
			$source.='/';
		$files = glob($source . '*', GLOB_MARK);
		if (!file_exists($dest) || !is_dir($dest)) {
			mkdir($dest, 0777, true);
		}
		foreach ($files AS $file) {
			if (is_dir($file)) {
				$this->recursive_copy($file, $dest . '/' . basename($file));
			} else {
				copy($file, $dest . '/' . basename($file));
			}
		}
	}
}

//initialize the plugin
global $wishlist_coupon;
$wishlist_coupon = new WishListCoupon20(__FILE__, 8945, 'wishlist-coupon20', 'WishList Coupon 2.0', 'WL Coupon 2.0', 'wpcpn_', true);
