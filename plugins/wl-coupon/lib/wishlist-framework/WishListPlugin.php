<?php

require_once dirname(__FILE__) .'/WishListDebugger.php';
require_once dirname(__FILE__) .'/WishListDb.php';
require_once dirname(__FILE__) .'/WishListXHRHandler.php';
require_once dirname(__FILE__) .'/WishListUtils.php';
require_once dirname(__FILE__) .'/WishListPluginBase.php';
require_once dirname(__FILE__) .'/WishListTinyMCEPluginAdapter.php';
require_once dirname(__FILE__) .'/TinyMCEPlugin.php';

if(!class_exists('WishListPlugin')) {
	abstract class WishListPlugin extends WishListPluginBase {
		protected $last_activation_response = null;
		protected $screen_id;
		protected $tmce;

		public function __construct($file, $sku, $slug, $name, $link_name, $prefix, $require_wlm) {
			parent::__construct($file, $sku, $slug, $name, $link_name, $prefix, $require_wlm);

			add_action('init', array($this, 'save_settings'));
			add_action('init', array($this, 'process_license'));
			add_action('admin_menu', array($this, 'admin_menus'));
			add_action('admin_notices', array($this, 'admin_submenus'), 1);
			add_action('admin_enqueue_scripts', array($this, 'admin_head'), 1);
			add_filter('site_transient_update_plugins', array($this, 'plugin_update_notice'));

			//support plugin update $file
			add_action('in_plugin_update_message-' . $this->plugin_file, array($this, 'plugin_info_link'));

			//upgrades
			add_filter('upgrader_pre_install', array(&$this, 'pre_upgrade'), 10, 2);
			add_filter('upgrader_post_install', array(&$this, 'post_upgrade'), 10, 2);
			$this->tmce = new WishListTinyMCEPluginAdapter($this);
		}
		public function save_settings() {
			if (isset($_POST[$this->plugin_action]) && $_POST[$this->plugin_action] == 'Save') {
				$this->save_options();
			}
		}
		public function admin_head() {

			$screen = get_current_screen();
			if($screen->id != $this->screen_id) {
				return;
			}

			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-tooltip-custom-wlp', $this->lib_url . 'js/jquery.tooltip.js', array('jquery'), '1.3');
			wp_enqueue_script('jquery-ui-tooltip-wlp', $this->lib_url . 'js/jquery.tooltip.wlp.js', array('jquery'));

			global $wp_version;

			if ( $wp_version >= 3.8 )
				wp_enqueue_style('wl-admin-main', $this->lib_url . '/css/admin_main.css');
			else
				wp_enqueue_style('wl-admin-main', $this->lib_url . '/css/admin_main_3_7_below.css');


			wp_enqueue_style('wl-admin-more', $this->lib_url .'/css/admin_more.css');
			wp_enqueue_style('wl-admin-tooltips', $this->lib_url .'/css/jquery.tooltip.css');

			//use same name as wlm so we don't queueu the same scripts multiple times
			wp_enqueue_style('wlm-font-awesome', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css');
		}
		/**
		 * Displays the admin menus for the plugin
		 */
		function admin_menus() {
			// Top Menu
			$firstMenu = $this->get_option('LicenseStatus') != '1' ? 'license' : 'admin_page';
			if($this->require_wlm && !is_plugin_active( 'wishlist-member/wpm.php' )) {
				$firstMenu = 'require_wlm';
			}


			if (!defined('WPWLTOPMENU')) {
				add_menu_page('WishList Plugins', 'WishList Plugins', 'manage_options', 'WPWishList', array($this, $firstMenu), $this->lib_url . '/images/WishListIcon.png');
				define('WPWLTOPMENU', 'WPWishList');
			}

			$this->screen_id = add_submenu_page(WPWLTOPMENU, get_class($this), $this->link_name, 'manage_options', get_class($this), array($this, $firstMenu));

			// Submenu for "Other Tab"
			$found = false;
			foreach ((array) $GLOBALS['submenu'] AS $key => $sm) {
				foreach ($sm AS $k => $m) {
					if ($m[2] == 'WPWLOther') {
						unset($GLOBALS['submenu'][$key][$k]);
						$found = true;
						$GLOBALS['submenu'][$key][] = $m;
						break;
					}
				}
			}
			if (!$found)  {
				add_submenu_page(WPWLTOPMENU, $this->lang('other_plugins'), 'Other', 'manage_options', 'WPWLOther', array($this, 'admin_other_tab'));
				// End of Submenu for "Other Tab"
			}

			unset($GLOBALS['submenu']['WPWishList'][0]);
		}
		/**
		 * Displays the admin sub-menus for this plugin
		 */
		public function admin_submenus() {
			if ($_GET['page'] == $this->class_name) {
				echo '<div class="wl_plugin_page">';
				echo '<h2 class="wl-nav-tab-wrapper">';
				echo '<a class="wl-nav-tab' . ($_GET['wl'] == '' ? ' wl-nav-tab-active' : '') . '" href="?page=' . $this->class_name . '">' . $this->lang('dashboard') . '</a>';
				foreach ((array) $this->menus AS $key => $menu) {
					$hasSubMenu = ($menu['HasSubMenu']) ? ' has-sub-menu' : '';
					if ( $menu['Direct'] ) {
						echo '<a class="wl-nav-tab"  href="' . $menu['Direct'] . '">' . $menu['Name'] . '</a>';
					} else {
						echo '<a class="wl-nav-tab' . ($_GET['wl'] == ($key) ? ' wl-nav-tab-active' . $hasSubMenu : '') . '" href="?page=' . $this->class_name . '&wl=' . $key . '">' . $menu['Name'] . '</a>';
					}
				}
				echo '</h2>';
				if ($_POST['err'])
					echo '<div class="error fade"><p>' . $_POST['err'] . '</p></div>';
				if ($_GET['err'])
					echo '<div class="error fade"><p>' . $_GET['err'] . '</p></div>';
				if ($_POST['msg'])
					echo '<div class="updated fade"><p>' . $_POST['msg'] . '</p></div>';
				if ($_GET['msg'])
					echo '<div class="updated fade"><p>' . $_GET['msg'] . '</p></div>';
				echo '</div>';
			}
		}
		public function require_wlm() {
			?>
			<div class="wrap">
				<div class="WLMRequireHolder error"><p><strong>WishList Member</strong> is required for this plugin to work.</p></div>
			</div>
			<?php
		}

		public function admin_page() {
			echo '<div class="wl_plugin_page">';
			$menu = $this->menus[$_GET['wl']];

			if(empty($menu)) {
				 $menu = array(
				 	"Name" => "Settings",
				 	"File" =>  "dashboard.php",
				 	"HasSubMenu" => false,
				 	"Direct" =>  false
				 );
			}

			$include = $this->plugin_dir . 'admin/' . $menu['File'];
			if(!file_exists($include) || !is_file($include)) {
				$include = $this->lib_dir . '/admin/' . $menu['File'];
			}

			$show_page_menu = true;
			include($include);
			$show_page_menu = false;
			echo '<div class="wrap">';
			include($include);
			if (WP_DEBUG) {
				echo '<p>' . get_num_queries() . ' queries in ';
				timer_stop(1);
				echo 'seconds.</p>';
			}
			echo '</div>';
			echo '</div>';

		}
		function admin_other_tab() {
			if (!@readfile('http://www.wishlistproducts.com/download/list.html')) {
				echo'<div class="wrap">', $this->lang('other_tab_content') . '</div>';
			}
		}

		/**
		 * Adds an admin menu
		 * @param string $key Menu Key
		 * @param string $name Menu Name
		 * @param string $file Menu File
		 * @param bool $hasSubMenu
		 */
		public function add_menu($key, $name, $file, $hasSubMenu=false, $direct=false) {
			$this->menus[$key] = array('Name' => $name, 'File' => $file, 'HasSubMenu' => (bool) $hasSubMenu, 'Direct' => $direct);
		}

		/**
		 * Retrieves a menu object.  Also displays an HTML version of the menu if the $html parameter is set to true
		 * @param string $key The index/key of the menu to retrieve
		 * @param boolean $html If true, it echoes the url in as an HTML link
		 * @return object|false Returns the menu object if successful or false on failure
		 */
		function get_menu($key, $html=false) {
			$obj = $this->menus[$key];
			if ($obj) {
				$obj = (object) $obj;
				$obj->URL = '?page=' . $this->MenuID . '&wl=' . $key;
				$obj->HTML = '<a href="' . $obj->URL . '">' . $obj->Name . '</a>';
				if ($html)
					echo $obj->HTML;
				return $obj;
			}else {
				return false;
			}
		}

		/**
		 * Displays the interface where the customer can enter the license information
		 */
		public function license() {
			?>
			<div class="wrap">
				<h2>WishList Products License Information</h2>
				<form method="post">
					<table class="form-table">
						<tr valign="top">
							<td colspan="3" style="border:none"><?php echo $this->lang('license_instructions') ?></td>
						</tr>
						<tr valign="top">
							<th scope="row" style="border:none;white-space:nowrap" class="WLRequired"><?php  echo $this->lang('label_license_email') ?></th>
							<td style="border:none"><input type="text" name="<?php $this->option('LicenseEmail', true); ?>" placeholder="WishList Products Email" value="<?php $this->option_value(); ?>" size="32" /></td>
							<td style="border:none"><?php echo $this->lang('license_email_help') ?></td>
						</tr>
						<tr valign="top">
							<th scope="row" style="border:none;white-space:nowrap;" class="WLRequired"><?php echo $this->lang('label_license_key') ?></th>
							<td style="border:none"><input type="text" name="<?php $this->option('LicenseKey', true); ?>" placeholder="WishList Products Key" value="<?php $this->option_value(); ?>" size="32" /></td>
							<td style="border:none"><?php echo $this->lang('license_key_help') ?></td>
						</tr>
					</table>
					<p class="submit">
						<input type="hidden" value="0" name="<?php $this->option('LicenseLastCheck'); ?>" />
						<?php $this->options();
						$this->required_options(); ?>
						<input type="hidden" value="<strong>License Information Saved</strong>" name="WLSaveMessage" />
						<input type="hidden" value="Save" name="<?php echo $this->plugin_action?>" />
						<input type="submit" value="Save WishList Products License Information" name="Submit" class="button-primary"/>
					</p>
				</form>
			</div>
			<?php
		}
		/**
		 * Processes the license information
		 */
		function process_license() {
			//bypass activation for
			if (WishListUtils::is_url_Local(strtolower(get_bloginfo('url'))) || $this->sku == 0) {
				$this->last_activation_response = '';
				$this->save_option('LicenseLastCheck', time());
				$this->save_option('LicenseStatus', 1);
				return;
			}

			$WPWLKey=$this->get_option('LicenseKey');
			$WPWLEmail=$this->get_option('LicenseEmail');
			$LicenseStatus=$this->get_option('LicenseStatus');
			$Retries=$this->get_option('LicenseRets',true,true)+0;
			$this->isBetaTester=$WPWLEmail=='beta@wishlistproducts.com';
			if($this->isBetaTester){
				add_action('admin_notices',array($this,'beta_tester'));
				add_action('the_content',array($this,'beta_tester'));
			}
			$WPWLLast=$this->get_option('LicenseLastCheck');
			$WPWLPID=$this->sku;
			$WPWLCheck=md5("{$WPWLKey}_{$WPWLPID}_".($WPWLURL=strtolower(get_bloginfo('url'))));			
                        $WPWLKeyAction = $this->arrval($_POST, 'wordpress_wishlist_deactivate') == $WPWLPID ? 'deactivate' : 'activate';
			$WPWLTime=time();
			$Month=60*60*24*7*30;

			//error_log('Checking again in '. ($WPWLLast - ($WPWLTime-$Month) ));
			//error_log('Retries: '.$Retries);
			if($WPWLTime-$Month>$WPWLLast || $WPWLKeyAction=='deactivate'){
				$urls=explode(',',self::ACTIVATION_URLS);
				$urlargs=array(
					'',
					'',
					urlencode($WPWLKey),
					urlencode($WPWLPID),
					urlencode($WPWLCheck),
					urlencode($WPWLEmail),
					urlencode($WPWLURL),
					urlencode($WPWLKeyAction),
					urlencode($this->Version)
				);
				foreach($urls AS &$url){
					$urlargs[0]='http://%s/activ8.php?key=%s&pid=%d&check=%s&email=%s&url=%s&%s=1&ver=%s';
					$urlargs[1]=$url;
					$url=call_user_func_array('sprintf',$urlargs);
				}
				$WPWLStatus=$this->last_activation_response= WishListUtils::read_url($urls, 5);
				if($WPWLStatus===false){
					if($Retries>=self::ACTIVATION_MAX_RETRIES || $LicenseStatus!=1){
						$WPWLStatus = $this->last_activation_response = 'Unable to contact License Activation Server. <a href="http://wlplink.com/go/activation" target="_blank">Click here for more info.</a>';
					}else{
						$this->save_option('LicenseRets', $Retries+1, true);
						$WPWLStatus = $this->get_option('LicenseStatus');
					}

					//staggered rechecks
					//if there is an error with wlm servers, check after an hour
					//so that we won't keep making requests
					$Month=60*60*24*7*30;
					$checkafter = 60 * 60 * 24 * 7;
					//For testing check after a minute
					//$checkafter = 60;
					$this->save_option('LicenseLastCheck',$WPWLTime - $Month + ($checkafter));
				}else{
					$this->save_option('LicenseRets', 0, true);
					$this->save_option('LicenseLastCheck',$WPWLTime);
				}

				$WPWLStatus = trim($WPWLStatus);
				$this->save_option('LicenseStatus',$WPWLStatus);

				if($WPWLKeyAction=='deactivate'){
					$this->delete_option('LicenseKey','LicenseEmail');
				}
			}

			// removing this line of code. causes error to E_STRICT
			// and I dont see significance of this line
			// will comment it now, in  case someone will need it.. :)
			//$this->this->last_activation_response = $this->last_activation_response;

			if($Retries>0){
				add_action('admin_notices',array($this,'activation_warning'));
			}

			if ( $this->get_option('LicenseStatus')!='1' ) {
				$this->last_activation_response = $this->get_option('LicenseStatus'); //added this line to display license processing error
				add_action('admin_notices',array($this,'process_license_warn'));
			}
		}
                
                /**
                * Checks if the requested array index is set and returns its value
                * @param array $array_or_object
                * @param string|number $index
                * @return mixed
                */
                function arrval($array_or_object, $index) {
                    if (is_array($array_or_object) && isset($array_or_object[$index])) {
                        return $array_or_object[$index];
                    }
                    if (is_object($array_or_object) && isset($array_or_object->$index)) {
                        return $array_or_object->$index;
                    }
                    return;
                }
		public function activation_warning() {
			if (!current_user_can('manage_options')) {
				return;
			}
			$rets = $this->get_option('LicenseRets', true, true);
			if (is_admin() && $rets > 0 && $rets < self::ACTIVATION_MAX_RETRIES) {
				echo '<div class="error fade"><p>';
				echo $this->lang('activation_warning');
				echo '</p></div>';
			}
		}
		/**
		 * Displays the license processing status
		 */
		public function process_license_warn() {
			if (!current_user_can('manage_options')) {
				return;
			}
			if ( strlen( $this->last_activation_response ) > 1) {
				echo '<div class="updated fade" id="message"><p style="color:#f00"><strong>' . $this->last_activation_response . '</strong></p></div>';
			}
		}
		/**
		 * Displays Beta Tester Message
		 */
		function beta_tester($return) {
			$aff = $this->get_option('affiliate_id');
			$url = $aff ? 'http://wishlistproducts.com/wlp.php?af=' . $aff : 'http://wishlistproducts.com/';
			$message = "This is a <strong><a href='{$url}'>PluginName</a></strong> Beta Test Site.";
			if (is_admin()) {
				echo '<div class="error fade"><p>';
				echo $message;
				echo '</p></div>';
			} else {
				echo '<div style="background:#FFEBE8; border:1px solid #CC0000; border-radius:3px; padding:0.2em 0.6em;">';
				echo $message;
				echo '</div>';
			}
			return $return;
		}
		public function plugin_update_notice($transient) {
			static $our_transient_response;
			if ($this->is_plugin_latest()) {
				return $transient;
			}


			if (!$our_transient_response) {
				$package = $this->get_download_url();
				if ($package === false) {
					return $transient;
				}

				$file = $this->plugin_file;
				$our_transient_response = array(
						$file => (object) array(
								'id' => 'plugin-name-' . time(),
								'slug' => $this->slug,
								'new_version' => $this->get_latest_version(),
								'url' => 'http://wordpress.org/extend/plugins/akismet/',
								'package' => $package
						)
				);
			}
			$transient->response = array_merge((array) $transient->response, (array) $our_transient_response);
			return $transient;
		}

		public function plugin_info_link() {
			echo <<<STRING
<span class="plugin-name_update-span"></span>
<script type="text/javascript">
	var wishlistproducts_link=jQuery('.plugin-name_update-span').siblings('a')[0];
	jQuery(wishlistproducts_link).attr('href','http://wishlistactivation.com/changelog.php?{$this->slug}');
	jQuery(wishlistproducts_link).attr('class','');
	jQuery(wishlistproducts_link).attr('target','_blank');
</script>
STRING;
		}

		function pre_upgrade($return, $plugin) {
			$plugin = (isset($plugin['plugin'])) ? $plugin['plugin'] : '';
			if ($plugin == $this->plugin_file) {
				$dir = sys_get_temp_dir() . '/' . 'plugin-name-Upgrade';

				$this->recursive_delete($dir);

				$this->recursive_copy($this->plugin_dir . '/lang', $dir . '/lang');
			}
			return $return;
		}

		function post_upgrade($return, $plugin) {
			$plugin = (isset($plugin['plugin'])) ? $plugin['plugin'] : '';
			if ($plugin == $this->plugin_file) {
				$dir = sys_get_temp_dir() . '/' . 'plugin-name-Upgrade';

				$this->recursive_copy($this->plugin_dir . '/lang', $dir . '/lang');

				$this->recursive_copy($dir . '/lang', $this->plugin_dir . '/lang');

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

		public function recursive_copy($source, $dest) {
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

		public function add_shortcode($title, $value, $shortcode=true) {

			$shortcodes = array(
 						array('title' => 'Login Button', 'value' => '[wllogin2_button]'),
 						array('title' => 'Full Login Form', 'value' => '[wllogin2_full]'),
 						array('title' => 'Compact Login Form', 'value' => '[wllogin2_compact]'),
 						array('title' => 'Horizontal Login Form', 'value' => '[wllogin2_horizontal]')
 				);
		}
		public function tooltip($tooltpid) {
			$thisTooltip = '<a class="help" rel="#' . $tooltpid . '" href="help"><span>&nbsp;<i class="icon-question-sign"></i> </span></a>';
			return $thisTooltip;
		}

	}
}
