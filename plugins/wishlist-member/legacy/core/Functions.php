<?php

/**
 * Converts $value to an absolute integer
 * @param mixed $value
 * @return integer
 */
function wlm_abs_int($value) {
	return abs((int) $value);
}

/**
 * adds a metadata to the user levels
 * note: right now only supports adding is_latest_registration
 * @param array user_levels
 * @param meta_name is_latest_registration
 *
 * Metadata implementations
 * is_latest_registration - if the current level is the latest level
 * the user has registered in, that level will have $obj->is_lastest_registration = 1
 *
 *
 */
function wlm_add_metadata(&$user_levels, $meta_name = 'is_latest_registration') {
	if ( ! is_array($user_levels) || count($user_levels) <= 0 ) return;
	if ($meta_name = 'is_latest_registration') {
		$idx = 0;
		$ref_ts = 0;
		foreach ($user_levels as $i => $item) {
			if ( is_object( $item ) ){
				$item->is_latest_registration = 0;
				if ($item->Timestamp > $ref_ts) {
					$idx = $i;
					$ref_tx = $item->Timestamp;
				}
			}
		}
		if(isset($user_levels[$idx]) && is_object($user_levels[$idx])) {
			$user_levels[$idx]->is_latest_registration = 1;
		}
		//break early please
		return;
	}
}

function wlm_diff_microtime($mt_old, $mt_new = '') {
	if (empty($mt_new)) {
		$mt_new = microtime();
	}
	list($old_usec, $old_sec) = explode(' ', $mt_old);
	list($new_usec, $new_sec) = explode(' ', $mt_new);
	$old_mt = ((float) $old_usec + (float) $old_sec);
	$new_mt = ((float) $new_usec + (float) $new_sec);
	return number_format($new_mt - $old_mt, 32);
}

/**
 * Prints text to specified file for debugging purposes
 * 
 * @param  string $text            Text to print
 * @param  string $filename        Optional destination filename. If none specified, then it will create a file prefixed with wlmdebug_ at the system temp dir
 * @param  string $cookie_to_check Optional cookie to check. If specified, then text is printed only if cookie is non-empty
 */
function wlm_debugout($text, $filename = null, $cookie_to_check = null) {
	if(!is_null($cookie_to_check) && empty($_COOKIE[$cookie_to_check])) return;

	$filename = $filename ? $filename : realpath(sys_get_temp_dir()) . '/wlmdebug_' . date('YMd');

	$text = trim($text) . "\n";

	file_put_contents($filename, $text, FILE_APPEND);
}

/**
 * Dissects the form part of a custom registration form
 * and returns an array of dissected field entries
 * @param string $custom_registration_form_data
 * @return array
 */
function wlm_dissect_custom_registration_form($custom_registration_form_data) {

	function fetch_label($string) {
		if (preg_match('#<td class="label".*?>(.*?)</td>#', $string, $match)) {
			return $match[1];
		} elseif (preg_match('#<td class="label ui-sortable-handle".*?>(.*?)</td>#', $string, $match)) {
			return $match[1];
		} else {
			return false;
		}
	}

	function fetch_desc($string) {
		if (preg_match('#<div class="desc".*?>(.*?)</div></td>#s', $string, $match)) {
			return $match[1];
		} else {
			return false;
		}
	}

	function fetch_attributes($tag, $string) {
		preg_match('#<' . $tag . '.+?>#', $string, $match);
		preg_match_all('# (.+?)="([^"]*?)"#', $match[0], $matches);
		$attrs = array_combine($matches[1], $matches[2]);
		unset($attrs['class']);
		unset($attrs['id']);
		return $attrs;
	}

	function wlm_fetch_options($type, $string) {
		$string = str_replace( [ "\n", "\r" ], '', $string );
		switch ($type) {
			case 'checkbox':
			case 'radio':
				preg_match_all('#<label[^>]*?>\s*<input.+?value="([^"]*?)"[^>]*?>(.*?)\s*</label>#', $string, $matches);
				$options = array();
				for ($i = 0; $i < count($matches[0]); $i++) {
					$option = array(
						'value' => $matches[1][$i],
						'text' => $matches[2][$i],
						'checked' => (int) preg_match('#checked="checked"#', $matches[0][$i])
					);
					$options[] = $option;
				}
				return $options;
				break;
			case 'select':
				preg_match_all('#<option value="([^"]*?)".*?>(.*?)</option>#', $string, $matches);
				$options = array();
				for ($i = 0; $i < count($matches[0]); $i++) {
					$option = array(
						'value' => $matches[1][$i],
						'text' => $matches[2][$i],
						'selected' => (int) preg_match('#selected="selected"#', $matches[0][$i])
					);
					$options[] = $option;
				}
				return $options;
				break;
		}

		return false;
	}

	$form = maybe_unserialize($custom_registration_form_data);

	$form_data = $form['form'];

	preg_match_all('#<tr class="(.*?li_(fld|submit).*?)".*?>(.+?)</tr>#is', $form_data, $fields);

	$field_types = $fields[1];
	$fields = $fields[3];

	foreach ($fields AS $key => $value) {
		$fields[$key] = array('fields' => $value, 'types' => explode(' ', $field_types[$key]));

		if (in_array('required', $fields[$key]['types'])) {
			$fields[$key]['required'] = 1;
		}
		if (in_array('systemFld', $fields[$key]['types'])) {
			$fields[$key]['required'] = 1;
			$fields[$key]['system_field'] = 1;
		}
		if (in_array('wp_field', $fields[$key]['types'])) {
			$fields[$key]['wp_field'] = 1;
		}

		$fields[$key]['description'] = fetch_desc($fields[$key]['fields']);

		if (in_array('field_special_paragraph', $fields[$key]['types'])) {
			$fields[$key]['type'] = 'paragraph';
			$fields[$key]['text'] = $fields[$key]['description'];
			unset($fields[$key]['description']);
		} elseif (in_array('field_special_header', $fields[$key]['types'])) {
			$fields[$key]['type'] = 'header';
			$fields[$key]['text'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_tos', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			unset($fields[$key]['attributes']['value']);
			unset($fields[$key]['attributes']['checked']);
			$options = wlm_fetch_options('checkbox', $fields[$key]['fields']);
			$fields[$key]['attributes']['value'] = trim( $options[0]['value'] );
			$fields[$key]['text'] = trim(preg_replace('#<[/]{0,1}a.*?>#', '', html_entity_decode($options[0]['value'])));
			$fields[$key]['type'] = 'tos';
			$fields[$key]['required'] = 1;
			$fields[$key]['lightbox'] = (int) in_array('lightbox_tos', $fields[$key]['types']);
		} elseif (in_array('field_radio', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			unset($fields[$key]['attributes']['checked']);
			unset($fields[$key]['attributes']['value']);
			$fields[$key]['options'] = wlm_fetch_options('radio', $fields[$key]['fields']);
			$fields[$key]['type'] = 'radio';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_checkbox', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			unset($fields[$key]['attributes']['checked']);
			unset($fields[$key]['attributes']['value']);
			$fields[$key]['options'] = wlm_fetch_options('checkbox', $fields[$key]['fields']);
			$fields[$key]['type'] = 'checkbox';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_select', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('select', $fields[$key]['fields']);
			$fields[$key]['options'] = wlm_fetch_options('select', $fields[$key]['fields']);
			$fields[$key]['type'] = 'select';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_textarea', $fields[$key]['types']) OR in_array('field_wp_biography', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('textarea', $fields[$key]['fields']);
			preg_match('#<textarea.+?>(.*?)</textarea>#', $fields[$key]['fields'], $match);
			$fields[$key]['attributes']['value'] = $match[1];
			$fields[$key]['type'] = 'textarea';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		} elseif (in_array('field_hidden', $fields[$key]['types'])) {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			$fields[$key]['type'] = 'hidden';
		} elseif (in_array('li_submit', $fields[$key]['types'])) {
			preg_match('#<input .+?value="(.+?)".*?>#', $fields[$key]['fields'], $match);
			$submit_label = $match[1];
			unset($fields[$key]);
		} else {
			$fields[$key]['attributes'] = fetch_attributes('input', $fields[$key]['fields']);
			$fields[$key]['type'] = 'input';
			$fields[$key]['label'] = fetch_label($fields[$key]['fields']);
		}

		unset($fields[$key]['fields']);
		unset($fields[$key]['types']);
	}

	ksort($fields);
	$fields = array('fields' => $fields, 'submit' => $submit_label);

	return $fields;
}

/**
 * Checks if the requested array index is set and returns its value
 * @param array $array_or_object
 * @param string|number $index
 * @return mixed
 */
function wlm_arrval($array_or_object, $index) {
	if (is_array($array_or_object) && isset($array_or_object[$index])) {
		return $array_or_object[$index];
	}
	if (is_object($array_or_object) && isset($array_or_object->$index)) {
		return $array_or_object->$index;
	}
	return;
}

/**
 * Function to correctly interpret boolean representations
 * - interprets false, 0, n and no as FALSE
 * - interprets true, 1, y and yes as TRUE
 *
 * @param mixed $value representation to interpret
 * @param type $no_match_value value to return if representation does not match any of the expected representations
 * @return boolean|$no_match_value
 */
function wlm_boolean_value($value, $no_match_value = false) {
	$value = trim(strtolower($value));
	if(in_array($value,array(false, 0, 'false','0','n','no'),true)){
		return false;
	}
	if(in_array($value,array(true, 1, 'true','1','y','yes'),true)){
		return true;
	}
	return $no_match_value;
}

function wlm_admin_in_admin() {
    
         return ((current_user_can('administrator') || current_user_can('wishlist_admin')) && is_admin());
}


/**
 * wlm cache functions
 */

function wlm_cache_flush() {
	wlm_cache_group_suffix(true);
}

function wlm_cache_set() {
	$args = func_get_args();
	$args[2] .= wlm_cache_group_suffix();
	return call_user_func_array('wp_cache_set', $args);
}

function wlm_cache_get() {
	$args = func_get_args();
	$args[1] .= wlm_cache_group_suffix();
	return call_user_func_array('wp_cache_get', $args);
}

function wlm_cache_delete($key, $group) {
	$args = func_get_args();
	$args[1] .= wlm_cache_group_suffix();
	return call_user_func_array('wp_cache_delete', $args);
}

function wlm_cache_group_suffix($reset = false) {
	static $wlm_cache_group_suffix;
	if(is_null($wlm_cache_group_suffix) && empty($reset)) {
		$wlm_cache_group_suffix = get_option( 'wlm_cache_group_suffix' );
	}
	if(empty($wlm_cache_group_suffix) || !empty($reset)) {
		$wlm_cache_group_suffix = microtime(true);
		update_option( 'wlm_cache_group_suffix', $wlm_cache_group_suffix );
	}
	return $wlm_cache_group_suffix;
}

// end of wlm cache functions

if (!function_exists('sys_get_temp_dir')) {

	function sys_get_temp_dir() {
		if ($temp = getenv('TMP'))
			return $temp;
		if ($temp = getenv('TEMP'))
			return $temp;
		if ($temp = getenv('TMPDIR'))
			return $temp;
		$temp = tempnam(__FILE__, '');
		if (file_exists($temp)) {
			unlink($temp);
			return dirname($temp);
		}
		return null;
	}

}

/**
 * Calls the WishList Member API 2 Internally
 * @param type $request (i.e. "/levels");
 * @param type $method (GET, POST, PUT, DELETE)
 * @param type $data (optional) Associate array of data to pass
 * @return type array WishList Member API2 Result
 */
function WishListMemberAPIRequest($request, $method = 'GET', $data = null) {
	require_once('API2.php');
	$api = new WLMAPI2($request, strtoupper($method), $data);
	return $api->result;
}


if(!function_exists('wlm_get_category_root')) {
	function wlm_get_category_root($id) {
		$cat = get_category($id);
		if($cat->parent) {
			$ancestors = get_ancestors($cat->term_id, 'category');
			$root        = count($ancestors) - 1;
			$root        = $ancestors[$root];
			return $root;
		} else {
			return $cat->term_id;
		}
	}
}

/**
 * @param id the category_id
 * @param string category|post
 * @return array returns a list of categories/posts and posts under category_id
 */
if(!function_exists('wlm_get_category_children')) {
	function wlm_get_category_children($id, $type = 'category') {
		$categories = array();
		$posts      = array();

		$categories = get_categories('child_of='.$id);

		$cats = array();
		foreach($categories as $c) {
			$cats[] = $c->term_id;
		}

		if($type == 'category') {
			return $cats;
		}

		$args = array(
			'category'       => $id,
			'posts_per_page' => -1
		);
		return get_posts($args);
	}
}


if(!function_exists('wlm_get_post_root')) {
	function wlm_get_post_root($id) {
		$cats  = get_the_category($id);
		$roots = array();
		foreach($cats as $c) {
			$roots[] = wlm_get_category_root($c);
		}
		return $roots;
	}
}


if(!function_exists('wlm_get_page_root')) {
	function wlm_get_page_root($id) {
		$post = get_post($id);
		if($post->post_parent) {
			$ancestors = get_post_ancestors($id);
			$root        = count($ancestors) - 1;
			$root        = $ancestors[$root];
		} else {
			$root        = $post->ID;
		}
		return $root;
	}
}
if(!function_exists('wlm_get_page_children')) {
	function wlm_get_page_children($page_id) {
		$children = array();
//		$root     = get_post($page_id);
//		$wp_query = new WP_Query();
//		$wp_pages = $wp_query->query(array('post_type' => 'page', 'posts_per_page' => 999));
//
//		$descendants = get_page_children($root->ID, $wp_pages);
        $descendants = get_children(array('post_parent' => $page_id));
		foreach($descendants as $d) {
			$children[] = $d->ID;
		}
		return $children;
	}

}

if(!function_exists('wlm_build_payment_form')) {
	function wlm_build_payment_form($data, $additional_classes='') {
		ob_start();
		extract((array) $data);
		include dirname(__FILE__).'/../resources/forms/popup-regform.php';
		$str = ob_get_clean();
		$str = preg_replace('/\s+/', ' ', $str);
		return $str;
	}

}

if(!function_exists('wlm_video_tutorial')) {
	function wlm_video_tutorial () {
		global $WishListMemberInstance;
		$args = func_get_args();
		$version = explode('.', $WishListMemberInstance->Version);

		// we only take the first digit of minor to comply
		// with john's URL format for tutorial video links
		$version = $version[0] . '-' . substr((string) $version[1], 0, 1);
		$parts = strtolower(implode('-', $args));
		$url = 'http://go.wlp.me/wlm:%s:vid:%s';
		return sprintf($url, $version, $parts);
	}
}

if(!function_exists('wlm_xss_sanitize')) {
	function wlm_xss_sanitize (&$string) {
		$string = preg_replace('/[<>]/', '', strip_tags($string));
	}
}

if(!function_exists('wlm_check_password_strength')) {
	function wlm_check_password_strength($password) {
		if(!preg_match('/[a-z]/', $password)) {
			return false;
		}
		if(!preg_match('/[A-Z]/', $password)) {
			return false;
		}
		if(!preg_match('/[0-9]/', $password)) {
			return false;
		}
		$chars = preg_quote('`~!@#$%^&*()-_=+[{]}|;:",<.>\'\?');
		if(!preg_match('/['.$chars.']/', $password)) {
			return false;
		}
		return true;
	}
}

function wlm_is_email($email) {
	return is_email( stripslashes($email) );
}

if(!function_exists('wlm_setcookie')) {
	function wlm_setcookie() {
		global $WishListMemberInstance;
		$args = func_get_args();
		$prefix = trim($WishListMemberInstance->GetOption('CookiePrefix'));
		if($prefix) {
			$args[0] = $prefix . $args[0];
		}
		return call_user_func_array('setcookie', $args);
	}
}
if(!class_exists('wlm_cookies')) {
	class wlm_cookies {
		private $prefix;
		function __construct() {
			global $wpdb;
			$tablename = $wpdb->prefix . 'wlm_options';
			$this->prefix = trim($wpdb->get_var("SELECT `option_value` FROM `{$tablename}` WHERE `option_name`='CookiePrefix'"));
		}
		function __set($name, $value) {
			$_COOKIE[$this->prefix . $name] = $value;
		}
		function __get($name) {
			return isset($_COOKIE[$this->prefix . $name]) ? $_COOKIE[$this->prefix . $name] : '';
		}
		function __isset($name) {
			return isset($_COOKIE[$this->prefix . $name]);
		}
		function __unset($name) {
			unset($_COOKIE[$this->prefix . $name]);
		}
	}
}

if(!function_exists('wlm_set_time_limit')) {
	function wlm_set_time_limit($time_limit = '') {
		$disabled = explode(',', ini_get('disable_functions'));
  		if(!in_array('set_time_limit', $disabled)) {
  			@set_time_limit($time_limit);
  			return;
  		}

	}
}

if( ! function_exists( 'wlm_insert_user' ) ) {
	function wlm_insert_user( $userdata ) {
		if ( is_multisite() ) {
			$blog_id = get_current_blog_id();
			$mu_user = get_user_by( 'email', $userdata['user_email'] );
			if ( $mu_user ) {
				if ( is_user_member_of_blog( $mu_user->ID, $blog_id ) ) {
					return false;
				} else {
					add_user_to_blog( $blog_id, $mu_user->ID, get_option('default_role') );
					return $mu_user->ID; 
				}
			}
		}
		return wp_insert_user( $userdata );
	}
}

if( ! function_exists( 'wlm_parse_size' ) ) {
	function wlm_parse_size( $size ) {
	  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	  if ( $unit ) {
	    return round( $size * pow(1024, stripos('bkmgtpezy', $unit[0] ) ) );
	  } else {
	    return round( $size );
	  }
	}
}

if( ! function_exists( 'wlm_get_file_upload_max_size' ) ) {
	function wlm_get_file_upload_max_size() {
	    $max_size   = wlm_parse_size(ini_get('post_max_size'));
	    $upload_max = wlm_parse_size(ini_get('upload_max_filesize'));
	    if ( $upload_max > 0 && $upload_max < $max_size ) {
	    	$max_size = $upload_max;
	    }
	    return $max_size;
	}
}

if( ! function_exists( 'wlm_get_client_ip' ) ) {
	function wlm_get_client_ip() {
		$sources = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR' );
		foreach( $sources AS $ip ) {
			if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false ) {
				return $ip;
			}
		}
		return $_SERVER['REMOTE_ADDR'];
	}
}

if( ! function_exists( 'wlm_enqueue_script' ) ) {
	/**
	 * @uses wp_enqueue_script - https://developer.wordpress.org/reference/functions/wp_enqueue_script/
	 * this is wp_enqueue_script on steroids
	 */
	function wlm_enqueue_script() {
		global $WishListMemberInstance, $current_screen;
		$args = func_get_args();

		wp_deregister_script( $args[0] );
		if( empty ( $args[5] ) ) {
			$args[0] = 'wishlistmember3-js-' . $args[0];
		}

		list( $url, $key, $data ) = array_pad( explode( '|', $args[1], 3), 3, '' );
		if( ! strpos( $url, '://' ) && strpos( $url, '/wp-content/' ) === false ) {
			$args[1] = $WishListMemberInstance->get_js( $url );
		}

		if( empty($args[2] ) ) $args[2] = array();
		array_walk( $args[2], function( &$value ) {
			if( substr( $value, 0, 1 ) == '-' ) $value = 'wishlistmember3-js' . $value;
		} );

		if( empty($args[3] ) ) $args[3] = $WishListMemberInstance->Version;
		call_user_func_array( 'wp_enqueue_script', $args );

		if( ! empty( $key ) && ! empty( $data ) && function_exists( 'wp_script_add_data' ) ) {
			wp_script_add_data( $args[0], $key, $data );
		}
	}
}

if( ! function_exists( 'wlm_enqueue_style' ) ) {
	/**
	 * @uses wp_enqueue_style - https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 * this is wp_enqueue_style on steroids
	 */
	function wlm_enqueue_style() {
		global $WishListMemberInstance, $current_screen;
		$args = func_get_args();
		if( empty ( $args[5] ) ) {
			$args[0] = 'wishlistmember3-css-' . $args[0];
		}

		list( $url, $key, $data ) = array_pad( explode( '|', $args[1], 3), 3, '' );
		if( ! strpos( $url, '://' ) && strpos( $url, '/wp-content/' ) === false ) {
			$args[1] = $WishListMemberInstance->get_css( $url );
		}

		if( empty($args[2] ) ) $args[2] = array();
		array_walk( $args[2], function( &$value ) {
			if( substr( $value, 0, 1 ) == '-' ) $value = 'wishlistmember3-css' . $value;
		} );
		
		if( empty($args[3] ) ) $args[3] = $WishListMemberInstance->Version;
		call_user_func_array( 'wp_enqueue_style', $args );

		if( ! empty( $key ) && ! empty( $data ) ) {
			wp_style_add_data( $args[0], $key, $data );
		}
	}

	function wlm_combine_styles() {
		global $WishListMemberInstance;
		
	}
}

if( ! function_exists( 'wlm_serialize_corrector' ) ) {
	function wlm_serialize_corrector( $serialized_string ){
		if ( @unserialize( $serialized_string ) !== true && preg_match( '/^[aOs]:/', $serialized_string ) ) {
			$serialized_string = preg_replace_callback( '/s\:(\d+)\:\"(.*?)\";/s', function( $matches ) { return 's:' . strlen( $matches[2]) . ':"' . $matches[2] . '";'; }, $serialized_string );
		}
		return $serialized_string;
	}
}

if( ! function_exists( 'wlm_form_field' ) )  {
	/**
	 * Generate and return standardized form field markup
	 * @param  array   $attributes   An array of attributes as supported by the input element. Special markup generated for type=textarea,select,checkbox,radio,submit,reset,button. options=array supported for type=select,checkbox,radio
	 * @return string                Standardized form field markup
	 */
	function wlm_form_field( $attributes ) {
		static $password_generator = false;
		static $password_metered = false;
		wp_enqueue_style( 'wlm3_form_css' );
		
		$defaults = [
			'label' => '',
			'name' => '',
			'type' => 'text',
			'value' => '',
			'options' => [],
			'class' => '',
			'id' => '',
			'description' => '',
			'text' => '',
			'lightbox' => '',
		];

		$hide = __( 'Hide', 'wishlist-member' );
		$show = __( 'Show', 'wishlist-member' );
		$cancel = __( 'Cancel', 'wishlist-member' );

		$attributes = wp_parse_args( $attributes, $defaults );

		$label = trim( $attributes[ 'label' ] );
		unset( $attributes[ 'label' ] );

		$value = $attributes[ 'value' ];
		unset( $attributes[ 'value' ] );

		$options = (array) $attributes[ 'options' ];
		unset( $attributes[ 'options' ] );

		$type = $attributes[ 'type' ];
		unset( $attributes[ 'type' ] );

		$text = $attributes[ 'text' ];
		unset( $attributes[ 'text' ] );

		$lightbox = $attributes[ 'lightbox' ];
		unset( $attributes[ 'lightbox' ] );

		if( !$attributes['id'] && $attributes['name'] ) {
			$attributes['id'] = 'wlm_form_field_' . $attributes['name'];
		}

		$description = trim( $attributes['description'] );
		unset( $attributes['description'] );
		if( $description ) {
			$description = sprintf( '<div class="wlm3-form-description">%s</div>', $description );
		}

		switch( $type ) {
			case 'paragraph':
				$field = sprintf( '<div class="wlm3-form-text">%s</div>', $text );
				break;
			case 'header':
				$field = sprintf( '<div class="wlm3-form-header">%s</div>', $text );
				break;
			case 'tos':
				if( $lightbox ) {
					wp_enqueue_script( 'wlm-jquery-fancybox' );
					wp_enqueue_style( 'wlm-jquery-fancybox' );
				}
				$field = [ 'input' ];
				$attributes[ 'class' ] .= ' form-checkbox fld';
				$attributes[ 'type' ] = 'checkbox';
				$attributes[ 'value' ] = $value;
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				if( !preg_match( '#((<p|div|br>)|</[a-zA-Z]+[0-9]*>)#', $description ) ) { // convert to html
					$description = nl2br( $description );
				}
				if( $lightbox ) {
					$description = sprintf( '<div style="display:none;"><div id="%s-lightbox">%s</div></div>', $attributes['id'], $description );
					$text = sprintf( '<a class="wlm3-tos-fancybox" href="#%s-lightbox">%s</a>', $attributes['id'], $text );
				} else {
					$description = sprintf( '<div class="wlm3-form-tos">%s</div>', $description );
				}

				$field = str_replace( [ '%%%field%%%', '%%%label%%%' ], [ implode( ' ', $field ), trim( $text ) ], '<label><%%%field%%%> %%%label%%%</label>' );
				break;
			case 'textarea':
				$attributes[ 'class' ] .= ' wlm3-form-field fld';
				$field = [ 'textarea' ];
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				$field = '<' . implode( ' ', $field ) . '>' . $value . '</textarea>';
				break;
			case 'select':
				$attributes[ 'class' ] .= ' wlm3-form-field fld';
				if( isset( $attributes[ 'multiple' ] ) && !preg_match( '/\[\]$/', $attributes[ 'name' ] ) ) {
					$attributes[ 'name' ] .= '[]';
				}
				$field = [ 'select' ];
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				foreach( $options AS $k => &$v ) {
					$selected = $k == $value ? ' selected="selected"' : '';
					$v = sprintf( '<option value="%s"%s>%s</option>', htmlentities( $k ), $selected, $v );
				}
				unset( $v );
				$field = '<' . implode( ' ', $field ) . '>' . implode( '', $options ) . '</select>';
				break;
			case 'checkbox':
				if( count( $options ) > 1 && !preg_match( '/\[\]$/', $attributes[ 'name' ] ) ) {
					$attributes[ 'name' ] .= '[]';
				}
			case 'radio':
				$attributes[ 'class' ] .= ' form-checkbox fld';
				$field = '';
				$checkbox = [ 'input' ];
				$attributes[ 'type' ] = $type;
				foreach( $attributes AS $k => $v ) {
					$checkbox[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				foreach( $options AS $k => $v ) {
					$checkbox['c'] = $k == $value ? 'checked="checked"' : '';
					$checkbox['v'] = sprintf( 'value="%s"', htmlentities( $k ) );
					$field .= str_replace( [ '%%%field%%%', '%%%label%%%' ], [ implode( ' ', $checkbox ), $v ], '<label><%%%field%%%> %%%label%%%</label>' );
				}
				break;
			case 'button':
				$field = [ 'button' ];
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				$field = '<' . implode( ' ', $field ) . '>' . $value . '</button>';
				break;
			case 'rawhtml':
				$field = $value;
				break;
			case 'media_uploader':
				if ( current_user_can( 'upload_files' ) ) {
					wp_enqueue_media();
					$str = __( 'Select File', 'wishlist-member' );
					
					$src = $value ?: $GLOBALS['WishListMemberInstance']->pluginURL3 . '/assets/images/grey.png';
					$field = '<div class="wlm3-profile-photo-container"><div class="wlm3-profile-photo"><input type="hidden" name="' . $attributes[ 'name' ] . '" value="' . $value . '"><img src="' . $src . '" /></div><div class="wlm3-profile-photo-icons"><span class="wlm3-frontend-media-clear">&#xf158;</span>&nbsp;<span class="wlm3-frontend-media-uploader">&#xf129;</span></div></div>';


				} else {
					$field = '';
				}
				break;
			case 'password_generator':
				if(!$password_generator) {
					$type = 'text';

					$id = '_' . md5( rand() . microtime() );
					$attributes[ 'id' ] = 'wlm3-password-field' . $id;

					$attributes[ 'onkeyup' ] = sprintf( 'wlm3_password_strength(this, \'%1$s\')', $id );
					$attributes[ 'style' ] .= ' display: none;';

					$append = sprintf( '<div id="wlm3-password-generator-strength%1$s"></div>', $id );

					$prepend = sprintf( '<button id="wlm3-password-generator-button%1$s" type="button" onclick="wlm3_generate_password(\'%1$s\'); return false">%2$s</button>', $id, __( 'Generate Password', 'wishlist-member' ) );
					$prepend .= sprintf( '<div id="wlm3-password-generator-buttons%1$s" style="display: none;"><button id="wlm3-password-generator-toggle%1$s" onclick="wlm3_generate_password_toggle(this, \'%1$s\'); return false;" data-hide="%2$s" data-show="%3$s">%2$s</button> <button id="wlm3-password-generator-cancel" onclick="wlm3_generate_password_hide(\'%1$s\'); return false;">%4$s</button></div>', $id, $hide, $show, $cancel );
					$password_generator = true;
				} else {
					$type = 'password';
				}
				$from_passgen = true;
			case 'password_metered':
				if( empty( $from_passgen ) ) {
					$type = 'password';

					$id = '_' . md5( rand() . microtime() );
					$attributes[ 'id' ] = 'wlm3-password-field' . $id;

					$attributes[ 'onkeyup' ] = sprintf( 'wlm3_password_strength(this, \'%1$s\')', $id );

					$append = sprintf( '<div id="wlm3-password-generator-strength%1$s"></div>', $id );
					$prepend = sprintf( '<div id="wlm3-password-generator-buttons%1$s" style="display: none;"><button id="wlm3-password-generator-toggle%1$s" onclick="wlm3_generate_password_toggle(this, \'%1$s\'); return false;" data-hide="%2$s" data-show="%3$s">%3$s</button></div>', $id, $hide, $show );
				}

				wp_enqueue_script( 'wlm3_form_js' );
				wp_enqueue_script( 'jquery' );
			case 'password':
				$value = '';

			default:
				if( !in_array( $type, [ 'submit', 'reset', 'image' ] ) ) {
					$attributes[ 'class' ] .= ' wlm3-form-field fld';
				}
				$attributes[ 'type' ] = $type;
				$attributes[ 'value' ] = htmlentities( $value );
				$field = [ 'input' ];
				foreach( $attributes AS $k => $v ) {
					$field[] = sprintf( '%s="%s"', $k, htmlentities( $v ) );
				}
				$field = '<' . implode( ' ', $field ) . '>';
				if( !empty( $prepend ) ) {
					$field = $prepend . $field;
				}
				if( !empty( $append ) ) {
					$field .= $append;
				}
		}

		switch( $type ) {
			case 'submit':
			case 'button':
			case 'image':
			case 'reset':
				$markup = '<p>%%%field%%%</p>';
				break;
			case 'hidden':
				$markup = '%%%field%%%';
				break;
			default:
				$markup = $label ? '<div class="wlm3-form-group"><label>%%%label%%%</label>%%%field%%%%%%description%%%</div>' : '<div class="wlm3-form-group">%%%field%%%%%%description%%%</div>';
		}

		$code = str_replace( [ '%%%label%%%', '%%%field%%%', '%%%description%%%' ], [ $label, $field, $description ], $markup );

		return $code;
	}
}

if( !function_exists( 'wlm_get_import_file_csv_separator' ) ) {
	/**
	 * Attempt to auto-detect the separator used in a CSV file
	 * Important: This function rewinds the file pointer to the beginning of the file
	 * 
	 * @param  resource $file_resource File handle
	 * @return string
	 */
	function wlm_detect_csv_separator( $file_resource ) {
		$separators = [ ',' => 0, ';' => 0, '|' => 0, "\t" => 0 ];

		rewind( $file_resource );
		$line = fgets( $file_resource );
		rewind( $file_resource );

		foreach( $separators AS $sep => &$count ) {
			$count = count( str_getcsv( $line, $sep ) );			
		}
		unset( $count );

		return array_search( max( $separators ), $separators );
	}
}

if( !function_exists( 'wlm_get_active_plugins' ) ) {
	/**
	 * Attempt to auto-detect the separator used in a CSV file
	 * Important: This function rewinds the file pointer to the beginning of the file
	 * 
	 * @param  resource $file_resource File handle
	 * @return string
	 */
	function wlm_get_active_plugins() {
		$active = get_option('active_plugins');
		$plugins = get_plugins();
		$active_plugins = array();
		foreach ( $active as $a ) {
		    if ( isset($plugins[$a]) ) {
		    	$active_plugins[$a] = isset($plugins[$a]['Name']) ? $plugins[$a]['Name'] : $a;
		    }
		}
		return $active_plugins;
	}
}

if( !function_exists( 'wlm_post_type_is_excluded' ) ) {
	function wlm_post_type_is_excluded( $post_type ) {
		// woocommerce
		if( class_exists( 'woocommerce' ) && in_array( $post_type, array( 'shop_coupon', 'shop_order_refund', 'shop_order', 'product_variation', 'product', 'shop_subscription' ) ) ) {
			return true;
		}

		// cartflows
		if( class_exists( 'Cartflows_Loader' ) && in_array( $post_type, array( 'cartflows_step' ) ) ) {
			return true;
		}
		
		return false;
	}
}