<?php

/**
 * Provide compatibility
 */
require_once dirname(__FILE__).'/WishListPlugin.php';

if (!class_exists('WishListCompat')) {

	/**
	 * Core PluginName Class
	 * @package PluginClassName
	 * @subpackage classes
	 */
	class WishListCompat extends WishListPlugin{

		/**
		 * Core Constructor
		 * @global <type> $wpdb
		 * @param <type> $pluginfile
		 * @param <type> $sku
		 * @param <type> $menuid
		 * @param <type> $title
		 * @param <type> $link
		 * @param <type> $dbprefix
		 */
		function __construct($file, $sku, $slug, $name, $link_name, $prefix=null, $require_wlm=NULL) {
			/** Start Compat mode **/

			$pluginfile = $file;
			$menuid     = get_class($this);
			$title      = $name;
			$link       = $link_name;
			$dbprefix   = $prefix;
			$ReqWLM     = $require_wlm;

			/** End Compat mode */
			parent::__construct($file, $sku, $slug, $name, $link_name, $prefix, $require_wlm);

			global $wpdb;
			global $WishListMemberInstance;
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');

			$this->PluginOptionName = 'PluginClassNameOptions';
			$this->TablePrefix = $wpdb->prefix . $dbprefix;
			$this->OptionsTable = $this->TablePrefix . 'options';

			$this->BlogCharset = get_option('blog_charset');

			$this->ProductSKU = $sku;

			$this->MenuID = $menuid;
			$this->Title = $title;
			$this->Link = $link;

			$this->PluginInfo = (object) get_plugin_data($pluginfile);
			$this->Version = $this->PluginInfo->Version;
			$this->WPVersion = $GLOBALS['wp_version'] + 0;

			$this->pluginPath = $pluginfile;
			$this->pluginDir = dirname($this->pluginPath);
			$this->PluginFile = basename(dirname($pluginfile)) . '/' . basename($pluginfile);

			$this->PluginSlug = 'plugin-name';
			$this->pluginBasename = plugin_basename($this->pluginPath);
			$this->pluginURL = plugins_url('', $this->pluginPath);

			$this->Menus = array();

			$this->ClearOptions();
			$is_wlm_active = is_plugin_active('wishlist-member/wpm.php');
			$this->RequireWLM = ($ReqWLM == "RequireWLM") ? true:false;
			$this->RequireWLM = ($this->RequireWLM && !$is_wlm_active)? false:true;


			$this->PreloadOptions();

			// add_filter('upgrader_pre_install', array(&$this, 'Pre_Upgrade'), 10, 2);
			// add_filter('upgrader_post_install', array(&$this, 'Post_Upgrade'), 10, 2);

			$this->LoadTables();
		}


		/**
		 * Load PluginName Tables
		 */
		function LoadTables() {
			$this->load_tables();
		}

		/**
		 * Core Activation Routine
		 */
		function CoreActivate() {
			$this->activate();
			$this->load_tables();
		}

		/**
		 * Core Deactivation Routine
		 */
		function CoreDeactivate() {
			/* does nothing at the moment */
		}

		/**
		 * Displays Beta Tester Message
		 */
		function BetaTester($return) {
			return $this->beta_tester($return);
		}

		/**
		 * Adds an admin menu
		 * @param string $key Menu Key
		 * @param string $name Menu Name
		 * @param string $file Menu File
		 * @param bool $hasSubMenu
		 */
		function AddMenu($key, $name, $file, $hasSubMenu=false, $direct=false) {
			$this->add_menu($key, $name, $file, $has_submenu, $direct);
		}

		/**
		 * Retrieves a menu object.  Also displays an HTML version of the menu if the $html parameter is set to true
		 * @param string $key The index/key of the menu to retrieve
		 * @param boolean $html If true, it echoes the url in as an HTML link
		 * @return object|false Returns the menu object if successful or false on failure
		 */
		function GetMenu($key, $html=false) {
			return $this->get_menu($key, $html);
		}

		/**
		 * Displays the interface where the customer can enter the license information
		 */
		function WPWLKey() {
			$this->license();
		}

		function ActivationWarning() {
			$rets = $this->GetOption('LicenseRets', true, true);
			if (is_admin() && $rets > 0 && $rets < self::ACTIVATION_MAX_RETRIES) {
				echo '<div class="error fade"><p>';
				echo $this->lang('activation_warning');
				echo '</p></div>';
			}
		}
		/**
		 * Checks whether a url is possibly local
		 * @param string $url the url to test
		 */
		function isLocal($url) {
			return WishListUtils::is_url_local($url);
		}
		/**
		 * Processes the license information
		 */
		function WPWLKeyProcess() {
			$this->process_license();
		}

		// /**
		//  * Displays the license processing status
		//  */
		// function WPWLKeyResponse() {
		// 	if (strlen($this->WPWLCheckResponse) > 1) {
		// 		echo '<div class="updated fade" id="message"><p style="color:#f00"><strong>' . $this->WPWLCheckResponse . '</strong></p></div>';
		// 	}
		// }

		/**
		 * Returns the Query String. Pass a GET variable and that gets removed.
		 */
		function QueryString() {
			return parent::query_string();
		}

		/**
		 * Sets up an array of form options
		 * @param string $name of the option
		 * @param boolean $required Specifies if the option is a required option
		 */
		function Option($name='', $required=false) {
			return parent::option($name, $required);
		}

		/**
		 * Retrieves the value of the form option that was previously set with Option method
		 * @param boolean $return Specifies whether to return the value or just output it to the browser
		 * @param string $default Default value to display
		 * @return string The value of the option
		 */
		function OptionValue($return=false, $default='') {
			return parent::option_value($return, $default);
		}

		/**
		 * Outputs selected="true" to the browser if $value is equal to the value of the option that was previously set
		 * @param string $value
		 */
		function OptionSelected($value) {
			parent::option_selected($value);
		}

		/**
		 * Outputs checked="true" to the browser if $value is equal to the value of the option that was previously set
		 * @param string $value
		 */
		function OptionChecked($value) {
			parent::option_checked($value);
		}

		/**
		 * Echoes form options that were set as a comma delimited string
		 * @param boolean $html echoes form options as the value of a hidden input field with the name "WLOptions"
		 */
		function Options($html=true) {
			return parent::options($html);
		}

		/**
		 * Echoes REQUIRED form options that were set as a comma delimited string
		 * @param boolean $html echoes form options as the value of a hidden input field with the name "WLRequiredOptions"
		 */
		function RequiredOptions($html=true) {
			$this->required_options();
		}

		/**
		 * Clears the form options array
		 */
		function ClearOptions() {
			$this->FormOptions = array();
		}

		// -----------------------------------------
		// Saves Options
		/**
		 * Saves the form options passed by POST
		 * @param boolean $showmsg whether to display the "Settings Saved" message or not
		 * @return boolean Returns false if a required field is not set
		 */
		function SaveOptions($showmsg=true) {
			$this->save_options($showmsg=true);
		}

		/**
		 * Cache all autoload options
		 */
		function PreloadOptions() {
			return parent::preload_options();

		}

		/**
		 * Retrieves an option's value
		 * @param string $option The name of the option
		 * @param boolean $dec (optional) True to decrypt the return value
		 * @param boolean $no_cache (optional) True to skip cache data
		 * @return string The option value
		 */
		function GetOption($option, $dec=null, $no_cache=null) {
			return parent::get_option($option, $dec, $no_cache);
		}

		/**
		 * Deletes the option names passed as parameters
		 */
		function DeleteOption() {
			parent::delete_option(func_get_args());
		}

		/**
		 * Saves an option
		 * @param string $option Name of the option
		 * @param string $value Value of option
		 * @param $enc (default false) True to encrypt $value
		 */
		function SaveOption($option, $value, $enc=false) {
			return parent::save_option($option, $value, $enc);
		}

		/**
		 * Adds a new option. Will not add it if the option already exists.
		 * @param string $option Name of the option
		 * @param string $value Value of option
		 * @param $enc (default false) True to encrypt $value
		 */
		function AddOption($option, $value, $enc=false) {
			return parent::add_option($option, $value, $enc);
		}

		/**
		 * Reads the content of a URL using Wordpress WP_Http class if possible
		 * @param string|array $url The URL to read. If array, then each entry is checked if the previous entry fails
		 * @param int $timeout (optional) Optional timeout. defaults to 5
		 * @param bool $file_get_contents_fallback (optional) true to fallback to using file_get_contents if WP_Http fails. defaults to false
		 * @return mixed FALSE on Error or the Content of the URL that was read
		 */
		function ReadURL($url, $timeout=null, $file_get_contents_fallback=null, $wget_fallback=null) {
			WishListUtils::read_url($url, $timeout, $file_get_contents_fallback, $wget_fallback);
		}

		/**
		 * Just return False
		 * @return boolean Always False
		 */
		function ReturnFalse() {
			return false;
		}

		/**
		 * Retrieves the tooltip id
		 * @return string Tooltip
		 */
		function Tooltip($tooltipid) {
			return parent::tooltip($tooltipid);
		}

		function Plugin_Download_Url() {
			return parent::get_download_url();
		}

		function Plugin_Latest_Version() {
			return parent::get_latest_version();
		}

		function Plugin_Is_Latest() {
			return parent::is_plugin_latest();
		}

		function Plugin_Update_Notice($transient) {
			return parent::plugin_update_notice($transient);
		}


		function Pre_Upgrade($return, $plugin) {
			return parent::pre_upgrade($return, $plugin);
		}

		function Post_Upgrade($return, $plugin) {
			return parent::post_upgrade($return, $plugin);
		}
		/**
		 * Simple obfuscation to garble some text
		 * @param string $string String to obfuscate
		 * @return string Obfucated string
		 */
		function WLMEncrypt($string) {
			return WishListUtils::encrypt($string);
		}

		/**
		 * Simple un-obfuscation to restore garbled text
		 * @param string $string String to un-obfuscate
		 * @return string Un-obfucated string
		 */
		function WLMDecrypt($string) {
			$utility = new WishListUtils;
			return $utility->decrypt($string);
		}

		function Plugin_Update_Url() {
      		return parent::get_update_url();
    	}
	}

}
?>