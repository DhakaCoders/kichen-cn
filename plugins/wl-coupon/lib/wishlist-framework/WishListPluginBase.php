<?php

if(!class_exists('WishListPluginBase')) {
	abstract class WishListPluginBase {
		const ACTIVATION_URLS = 'wishlistactivation.com';
		const ACTIVATION_MAX_RETRIES = 5;

		protected $libversion = '0.1';

		protected $plugin_info;
		protected $file;
		protected $version;
		protected $wldb;
		protected $sku;
		protected $name;
		protected $class_name;
		protected $link_name;
		protected $prefix;
		protected $require_wlm;
		protected $slug;
		protected $xhr_handler;
		protected $plugin_url;
		protected $plugin_dir;
		protected $plugin_file;

		protected $lib_url;
		protected $lib_dir;


		//tables
		protected $options_table;

		//misc
		protected $plugin_action;
		protected $text_domain;
		protected $lang;

		//
		protected $debugger;

		public function __construct($file, $sku, $slug, $name, $link_name, $prefix=null, $require_wlm=false) {
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');

			register_activation_hook($file, array($this, 'activate'));
			register_deactivation_hook($file, array($this, 'deactivate'));

			$this->file          = $file;
			$this->name          = $name;
			$this->class_name    = get_class($this);
			$this->sku           = $sku;
			$this->link_name     = $link_name;
			$this->require_wlm   = $require_wlm;
			$this->slug          = $slug;
			$this->plugin_file   = basename(dirname($this->file)) . '/' . basename($this->file);

			$this->plugin_info   = (object) get_plugin_data($file);
			$this->version       = $this->plugin_info->Version;

			$this->plugin_url    = plugins_url('/', $file);
			$this->plugin_dir    = plugin_dir_path($file);

			$this->lib_url       = plugins_url('/', __FILE__);
			$this->lib_dir       = rtrim(plugin_dir_path( __FILE__ ), '/');

			$this->plugin_action = $this->class_name . 'Action';
			$this->text_domain   = $this->plugin_info->TextDomain;

			$this->debugger      = new WishListDebugger($this);


			if(empty($prefix)) {
				$prefix = strtolower(get_class($this)) . '_';
			}

			$this->prefix      = $prefix;
			$this->wldb        = new WishListDb($this->prefix, $this);
			$this->xhr_handler = new WishListXHRHandler($this);

			//support translation
			$this->support_translation();
			$this->load_translations();
		}

		public function activate() {
			$this->wldb->create_tables();
		}

		public function deactivate() {
		}

		/**
		 * Returns the Query String. Pass a GET variable and that gets removed.
		 */
		public function query_string() {
			$args = func_get_args();
			$args[] = 'msg';
			$args[] = 'err';
			$get = array();
			parse_str($_SERVER['QUERY_STRING'], $querystring);
			foreach ((array) $querystring AS $key => $value)
				$get[$key] = "{$key}={$value}";
			foreach ((array) array_keys((array) $get) AS $key) {
				if (in_array($key, $args))
					unset($get[$key]);
			}
			return implode('&', $get);
		}
		/**
		 * Load PluginName Tables
		 */
		function load_tables() {
			global $wpdb;
			// prepare table names
			$this->tables = new stdClass();
			$p = esc_sql($this->prefix);
			$tables = $wpdb->get_results("SHOW TABLES LIKE '{$this->prefix}%'", ARRAY_N);
			$plen = strlen($this->prefix);
			foreach ($tables AS $table) {
				$x = substr($table[0], $plen);
				$this->tables->$x = $table[0];
			}
		}
		/**
		 * Adds a new option. Will not add it if the option already exists.
		 * @param string $option Name of the option
		 * @param string $value Value of option
		 * @param $enc (default false) True to encrypt $value
		 */
		function add_option($option, $value, $enc=false) {
			global $wpdb;
			$WishListUtils = new WishListUtils();
			$cache_key = $option;
			$cache_group = $this->wldb->options;
			$x = $this->get_option($option);
			if ($x === false) {
				if ($enc)
					$value = $WishListUtils->encrypt($value);
				$data = array(
						'option_name' => $option,
						'option_value' => maybe_serialize($value)
				);
				$x = $wpdb->insert($this->wldb->options, $data);
				wp_cache_delete($cache_key, $cache_group);
			}
			return $x ? true : false;
		}
		/**
		 * Saves an option
		 * @param string $option Name of the option
		 * @param string $value Value of option
		 * @param $enc (default false) True to encrypt $value
		 */
		function save_option($option, $value, $enc=false) {
			global $wpdb;
			$WishListUtils = new WishListUtils();
			$cache_key = $option;
			$cache_group = $this->wldb->options;
			if ($enc)
				$value = $WishListUtils->encrypt($value);

			$x = $this->get_option($option);
			if ($x === false) {
				$x = $this->add_option($option, $value, $enc);
				return $x ? true : false;
			} elseif ($x != $value) {
				$data = array(
						'option_name' => $option,
						'option_value' => maybe_serialize($value)
				);
				$where = array(
						'option_name' => $option
				);
				$x = $wpdb->update($this->wldb->options, $data, $where);

				wp_cache_delete($cache_key, $cache_group);
				return $x ? true : false;
			}
		}
		/**
		 * Saves the form options passed by POST
		 * @param boolean $showmsg whether to display the "Settings Saved" message or not
		 * @return boolean Returns false if a required field is not set
		 */
		function save_options($showmsg=true) {
			foreach ((array) $_POST AS $k => $v) {
				if (!is_array($v)) {
					$_POST[$k] = trim(stripslashes($v));
				}
			}
			$required = explode(',', $_POST['WLRequiredOptions']);
			foreach ((array) $required AS $req) {
				if ($req && !$_POST[$req]) {
					$_POST['err'] = $this->lang('error_required');
					return false;
				}
			}
			$options = explode(',', $_POST['WLOptions']);
			$options_arr = array();
			foreach ((array) $options AS $option) {
				$this->save_option($option, $_POST[$option]);
				$options_arr[$option] = $_POST[$option];
			}

			do_action(strtolower($this->class_name) . '_save_options', $options_arr);

			if ($showmsg) {
				$_POST['msg'] = $_POST['WLSaveMessage'] ? $_POST['WLSaveMessage'] : $this->lang('settings_saved');
			}


		}

		function get_option($option, $dec=null, $no_cache=null) {
			global $wpdb;
			$utility = new WishListUtils;
			$cache_key = $option;
			$cache_group = $this->wldb->options;

			if (is_null($dec))
				$dec = false;
			if (is_null($no_cache))
				$no_cache = false;

			$value = ($no_cache === true) ? false : wp_cache_get($cache_key, $cache_group);
			if ($value === false) {
				$row = $wpdb->get_row($wpdb->prepare("SELECT `option_value` FROM `{$this->wldb->options}` WHERE `option_name`='%s'", $option));
				if (!is_object($row))
					return false;
				$value = $row->option_value;

				$value = maybe_unserialize($value);

				wp_cache_set($cache_key, $value, $cache_group);
			}
			if ($dec) {
				$value = $utility->decrypt($value);
			}
			return $value;
		}
		/**
		 * Sets up an array of form options
		 * @param string $name of the option
		 * @param boolean $required Specifies if the option is a required option
		 */
		public function option($name='', $required=false) {
			if ($name) {
				$this->form_option = $name;
				$this->form_options[$name] = (bool) $required;
				echo $name;
			} else {
				echo $this->form_option;
			}
		}
		/**
		 * Retrieves the value of the form option that was previously set with Option method
		 * @param boolean $return Specifies whether to return the value or just output it to the browser
		 * @param string $default Default value to display
		 * @return string The value of the option
		 */
		function option_value($return=false, $default='') {
			if ($_POST['err']) {
				$x = $_POST[$this->form_option];
			} else {
				$x = $this->get_option($this->form_option);
			}
			//also handle arrays
			if (!strlen($x) & !is_array($x)) {
				$x = $default;
			}
			if ($return) {
				return $x;
			}
			echo $x; //htmlentities($x, ENT_QUOTES, $this->BlogCharset);
		}
		/**
		 * Outputs selected="true" to the browser if $value is equal to the value of the option that was previously set
		 * @param string $value
		 */
		function option_selected($value) {
			$x = $this->option_value(true);
			if ($x == $value || in_array($value, $x)) {
				echo ' selected="true"';
			}
		}

		/**
		 * Outputs checked="true" to the browser if $value is equal to the value of the option that was previously set
		 * @param string $value
		 */
		function option_checked($value) {
			$x = $this->option_value(true);
			if ($x == $value || in_array($value, $x)) {
				echo ' checked="true"';
			}
		}
		/**
		 * Deletes the option names passed as parameters
		 */
		function delete_option() {
			global $wpdb;
			$cache_group = $this->wldb->options;
			$x = func_get_args();

			foreach ($x as $option) {
				$cache_key = $option;
				$wpdb->query($wpdb->prepare("DELETE FROM `{$this->wldb->options}` WHERE `option_name`='%s'", $option));
				wp_cache_delete($cache_key, $cache_group);
			}
		}
		/**
		 * Echoes form options that were set as a comma delimited string
		 * @param boolean $html echoes form options as the value of a hidden input field with the name "WLOptions"
		 */
		function options($html=true) {
			$value = implode(',', array_keys((array) $this->form_options));
			if ($html) {
				echo '<input type="hidden" name="WLOptions" value="' . $value . '" />';
			} else {
				echo $value;
			}
		}

		/**
		 * Echoes REQUIRED form options that were set as a comma delimited string
		 * @param boolean $html echoes form options as the value of a hidden input field with the name "WLRequiredOptions"
		 */
		public function required_options($html=true) {
			$value = implode(',', array_keys((array) $this->form_options, true));
			if ($html) {
				echo '<input type="hidden" name="WLRequiredOptions" value="' . $value . '" />';
			} else {
				echo $value;
			}
		}

		public function get_latest_version() {
			static $latest_ver;
			$varname = $this->slug . '_latest_plugin_version';
			if (empty($latest_ver) OR isset($_GET['checkversion'])) {
				$latest_ver = get_transient($varname);
				if (empty($latest_ver) OR isset($_GET['checkversion'])) {
					$latest_ver = WishListUtils::read_url('http://wishlistactivation.com/versioncheck/?'.$this->slug);
					if(empty($latest_ver)){
						$latest_ver=$this->version;
					}
					set_transient($varname, $latest_ver, 60 * 60 * 24);
				}
			}
			return $latest_ver;
		}

		function is_plugin_latest() {
			$latest_ver = $this->get_latest_version();
			$ver = $this->version;
			if (preg_match('/^(\d+\.\d+)\.{' . 'GLOBALREV}$/', $this->version, $match)) {
				$ver = $match[1];
				preg_match('/^(\d+\.\d+)\.[^\.]*/', $latest_ver, $match);
				$latest_ver = $match[1];
			}
			return version_compare($latest_ver, $ver, '<=');
		}
		/**
		 * Cache all autoload options
		 */
		function preload_options() {
			global $wpdb;
			$results = $wpdb->get_results("SELECT `option_name`, `option_value` FROM `{$this->wldb->options}` WHERE `autoload`='yes'");
			if (!count($results)) {
				return;
			}

			foreach ($results AS $result) {
				if (substr($result->option_name, 0, 3) != 'xxx') {
					$value = maybe_unserialize($result->option_value);
					wp_cache_set($result->option_name, $value, $this->wldb->options);
				}
			}
		}

		function get_download_url() {
			static $url;
			if ($this->get_option('LicenseStatus') != 1) {
				return false;
			}
			if (!$url) {
				$url = 'http://wishlistproducts.com/download/' . $this->get_option('LicenseKey') . '/==' . base64_encode(pack('i', $this->sku));
			}
			return $url;
		}

		function get_update_url() {
			return wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $this->plugin_file, 'upgrade-plugin_' . $this->plugin_file);
		}

		public function lang($name) {
			return $this->lang[$name];
		}
		public function load_translations() {
			include $this->plugin_dir .'/lang/lang.php';
			$child_lang = $lang;

			$lang = array();
			include $this->lib_dir . '/lang/lang.php';
			$parent_lang = $lang;

			$this->lang = array_merge($parent_lang, $child_lang);

		}
		public function support_translation() {
			//load child text domain
			$pd = basename($this->plugin_dir) . '/lang';
			load_plugin_textdomain($this->text_domain, PLUGINDIR . '/' . $pd, $pd);

			//load library text domain
			$pd = basename($this->plugin_dir) . '/'. basename(dirname($this->lib_dir)) . '/'. basename($this->lib_dir) .'/lang';
			load_plugin_textdomain('wishlist-framework', $pd, $pd);
		}

		public function __call($name, $x) {
			if(!method_exists($this, $name)) {
				if(preg_match('/get_(.*$)/', $name, $matches)) {
					$attr = $matches[1];
					return $this->$attr;
				}
			}
		}
	}
}
