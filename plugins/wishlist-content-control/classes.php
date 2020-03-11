<?php

/* CORE CLASS */ 
if (!class_exists('WishListContentControl')) {

	class WishListContentControl {
		const ActivationURLs = 'wishlistactivation.com';
		const ActivationMaxRetries = 5;
//constructor
		function WishListContentControl($pluginfile, $menuid) {
			$this->pluginfile = $pluginfile;
			$this->plugindir = dirname($this->pluginfile);
			$this->pluginurl = plugins_url('', '/') . basename($this->plugindir);
			$this->menuid = $menuid;
			$this->modules = array();
			$this->modules_version = array();
			$this->WLCCNotices = "";
			$this->WLCCLicenseNotices = array();
			$this->WLCCActivationWarningNotices = array();
			$this->skus = array('ContentScheduler' => '8909', 'ContentManager' => '8911', 'ContentArchiver' => '8910');
			$this->vercode = array('ContentScheduler' => 'wlcontentscheduler', 'ContentManager' => 'wlcontentmanager', 'ContentArchiver' => 'wlcontentarchiver');
		}

//init
		function Init() {
			global $WishListMemberInstance;
			if( ! isset( $WishListMemberInstance ) ) return;
			$modules = glob($this->plugindir . '/modules/*.php');
			sort($modules);
			//Load Modules
			$wlcc_status = $WishListMemberInstance->GetOption('wlcc_status');
			foreach ((array) $modules as $module) {
				include_once($module);
				$this->modules[$CCModule['ClassName']] = $CCModule;
				$this->modules_version[$CCModule['ClassName']] = $CCModule['Version'];
				if (array_key_exists($CCModule['ClassName'], (array) $wlcc_status)) {
					if ($wlcc_status[$CCModule['ClassName']] <= 0 || $wlcc_status[$CCModule['ClassName']] == "" || $status[$CCModule['ClassName']] = false) {
						$status[$CCModule['ClassName']] = false;
					} else {
						$status[$CCModule['ClassName']] = $wlcc_status[$CCModule['ClassName']];
					}
				} else {
					$status[$CCModule['ClassName']] = false;
				}
			}

			if (count($status) == 1) {
				$key = array_keys($status);
				$status[$key[0]] = 1;
			}
			//get WLCC Actions
			if (isset($_POST['WishListControlAction'])) {
				switch ($_POST['WishListControlAction']) {
					//save license key
					case 'SaveLincenseKey':
						$LicenseKey = $_POST['LicenseKey'];
						$LicenseEmail = $_POST['LicenseEmail'];
						$Module = $_POST['WishListControlModule'];
						if ($LicenseKey == "" || $LicenseEmail == "" || $Module == "") {
							$this->WLCCNotices = "License Email and Key are required.";
							add_action('admin_notices', array(&$this, 'WLCCMessage'), 1);
						} else {
							//check if the module is valid
							if (array_key_exists($Module, (array) $status)) {
								$LicenseKeyOption = $Module . 'LicenseKey';
								$LicenseEmailOption = $Module . 'LicenseEmail';
								$WishListMemberInstance->SaveOption($LicenseKeyOption, $LicenseKey);
								$WishListMemberInstance->SaveOption($LicenseEmailOption, $LicenseEmail);
							}
						}
						break;
				    case 'DeactivateLicense':
						$Module = isset($_POST['WishListControlModule']) ? $_POST['WishListControlModule'] : $_GET['WishListControlModule'];
						if (array_key_exists($Module, (array) $status)) {
							$this->ProcessKey($Module);
						}
					    break;
				}
			}

			//call the license key processor for each module
			foreach ($status as $key => $value) {
				if ($value) {
						$this->ProcessKey($key);
				}
			}

			$WishListMemberInstance->SaveOption('wlcc_status', $status);
			//do wl-contentcontrol action hooks for modules
			do_action('wl-contentcontrol_hook', $this);
			add_action('admin_head', array(&$this, 'AdminHead'), 1);
		}

//Admin Head for CSS and JS
		function AdminHead() {
				$pagenow = $GLOBALS['pagenow'];
				$page = isset($_GET['page']) ? $_GET['page'] : false;
				if($pagenow == "post.php" || $pagenow == "post-new.php" || $page == $this->menuid){

					//echo "<link rel='stylesheet' type='text/css' href='{$this->pluginurl}/css/jquery.tooltip.css' />";
					//echo "<link rel='stylesheet' type='text/css' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css' />";
					//echo "<link rel='stylesheet' type='text/css' href='{$this->pluginurl}/css/jquery.ui.datetimepicker.css' />";
					//echo "<link rel='stylesheet' type='text/css' href='{$this->pluginurl}/css/admin_main.css' />";

					wp_enqueue_style('wlcc_tooltip_css', $this->pluginurl.'/css/jquery.tooltip.css');
					wp_enqueue_style('wlcc_jquery_ui_css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css' );
					wp_enqueue_style('wlcc_datetimepicker_css', $this->pluginurl.'/css/jquery.ui.datetimepicker.css');
					wp_enqueue_style('wlcc_admin_main_css', $this->pluginurl.'/css/admin_main.css');

					wp_enqueue_script('jquery-ui-core');
					wp_enqueue_script('jquery_wlcc_tooltip', $this->pluginurl . '/js/jquery.tooltip.js');
					wp_enqueue_script('jquery_wlm_tooltip', $this->pluginurl . '/js/jquery.tooltip.wlm.js');
					wp_enqueue_script('wlcc_datepicker_js', $this->pluginurl . '/js/jquery.ui.datetimepicker.min.js');
					wp_enqueue_script('wlcc_admin_js', $this->pluginurl . '/js/admin.js');
					wp_enqueue_script('wlcc_jquery_js', $this->pluginurl . '/js/wlcc.jquery.js');
			}
		}

//settings page
		function SettingsPage() {
			global $WishListMemberInstance;
			//get the module status
			$wlcc_status = $WishListMemberInstance->GetOption('wlcc_status');
			$keys = array_keys($wlcc_status);
			foreach ((array) $keys as $key) {
				if (isset($_POST[$key])) {
					$val = ($_POST[$key] == "Enable" ? true : false);
					$wlcc_status[$key] = $val;
				}
			}
			//save module status
			$WishListMemberInstance->SaveOption('wlcc_status', $wlcc_status);
			$wlcc_status = $WishListMemberInstance->GetOption('wlcc_status');
			echo '<br />';
			//get module page
			$modulepage = $_GET['module'];
			if (count($wlcc_status) == 1 && $modulepage == "") { // if theres only 1 module make it the default screen
				$key = array_keys($wlcc_status);
				$modulepage = $key[0];
			}

			//show the modules menu
			echo '<h2 class="nav-tab-wrapper">';
			echo '<a href="?page=' . $this->menuid . '&module=dashboard" class="nav-tab' . ($modulepage == 'dashboard' || $modulepage == '' ? ' nav-tab-active' : '') .'" >Dashboard</a>';
			foreach ((array) $this->modules as $module) {
				if ($wlcc_status[$module['ClassName']]) {
					echo '<a href="?page=' . $this->menuid . '&module=' . $module['ClassName'] . '" class="nav-tab' . ($modulepage == $module['ClassName'] ? ' nav-tab-active' : '') . '" >' . $module['Name'] . '</a>';
				}
			}
			echo '</h2>';

			echo '<div class="wrap">';
			if ($modulepage != "dashboard" && count($this->modules[$modulepage]) > 0 && ($wlcc_status[$this->modules[$modulepage]['ClassName']] || $this->LicenseStatus($this->modules[$modulepage]['ClassName']) != '1')) {
				do_action('wl-contentcontrol_dashboard', $this->modules[$modulepage]['ClassName'], $this);
			} else {
				$this->Dashboard($wlcc_status);
			}
			echo '</div>';
			include_once($this->plugindir . '/other/tooltips.php');
		}

//default dashboard
		function Dashboard($wlcc_status) {
			global $WishListMemberInstance;
			?>
			<h2><?php _e('WishList Content Control', 'wl-contentcontrol'); ?></h2><br />
			<table class="widefat">
				<thead>
					<tr>
						<th colspan="2"><?php _e('WishList Content Control Modules', 'wishlist-shortcodeplus'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$alt = 0;
					foreach ((array) $this->modules as $module) {

						$latest_wpm_ver = $this->Plugin_Latest_Version($module['ClassName']);
						if (!$latest_wpm_ver)
							$latest_wpm_ver = $this->modules_version[$module['ClassName']];

						$revision = explode(".", $this->modules_version[$module['ClassName']]);
						$version = $revision[0] . '.' . $revision[1];
						$build = $revision[2];
						?>
						<tr valign="top" class="<?php echo $alt++ % 2 ? '' : 'alternate'; ?>">
							<td style="width:20%"><a name="<?php echo $module['ClassName']; ?>"></a><strong><?php echo $module['Name']; ?></strong></th>
							<td style="width:80%"><form method="post"><a name="new"></a>
									<?php echo $module['Description']; ?><br />
									<?php
									if ($wlcc_status[$module['ClassName']]) {
										if (count($wlcc_status) > 1) {
											_e('Status: ', 'wl-contentcontrol');
											echo '<span style="color:green"> Enabled</span>&nbsp;&nbsp;&nbsp;';
											echo '<input type="submit" name="' . $module['ClassName'] . '" id="' . $module['ClassName'] . '" class="button-secondary" value="Disable" />&nbsp;&nbsp;&nbsp;';
										}
									} else {
										if (count($wlcc_status) > 1) {
											_e('Status: ', 'wl-contentcontrol');
											echo '<span style="color:red">Disabled</span>&nbsp;&nbsp;&nbsp;';
											echo '<input type="submit" name="' . $module['ClassName'] . '" id="' . $module['ClassName'] . '" class="button-secondary" value="Enable" />&nbsp;&nbsp;&nbsp;';
										}
									}
									echo "<span class='small'>Version " . $version . " | " . "Build " . $build . "</span>";
									?>
									<br />
									<?php if ($this->Plugin_Is_Latest($latest_wpm_ver,$this->modules_version[$module['ClassName']])): ?>
										<?php printf(__('You have the latest version of ' . $module['Name'] . ' (v%1$s)', 'wishlist-shortcodeplus'), $this->modules_version[$module['ClassName']]); ?>
									<?php else: ?>
										<span style="color:red"><?php printf(__('* Please update your ' . $module['Name'] . ', the most current version is v%1$s.', 'wishlist-shortcodeplus'), $latest_wpm_ver); ?></span>
										<?php printf(__('<a href="%1$s">Download</a>', 'wishlist-shortcodeplus'), $this->Plugin_Download_Url($module['ClassName'])); ?>
									<?php endif; ?>
								</form>
								<?php
								if ($wlcc_status[$module['ClassName']]) {
									if ($this->LicenseStatus($module['ClassName']) != '1'):
										?>
										<p class="submit">
											<a id="<?php echo $module['ClassName'] ?>lnk" href="javascript:void(0);" onclick="wlcc_show('<?php echo $module['ClassName']; ?>','false')" >Activate License</a>
										</p>
									<?php else: ?>
										 <?php if($this->skus[$module['ClassName']] > 0 && !$this->isLocal(strtolower(get_bloginfo('url')))) : ?>
										<form method="post" onsubmit="return confirm('Are you sure that you want to deactivate the license of this plugin for this site?')">
											<p class="submit">
												<input type="hidden" name="wordpress_wishlist_deactivate" value="<?php echo $this->skus[$module['ClassName']]; ?>" />
												<input type="hidden" name="WishListControlModule" value="<?php echo $module['ClassName']; ?>" />
												<input type="hidden" value="DeactivateLicense" name="WishListControlAction" />
												<input type="submit" value="Deactivate License For This Site" name="Submit" />
											</p>
										</form>
										<?php endif; ?>
									<?php
									endif;
									$LicenseKeyOption = $module['ClassName'] . 'LicenseKey';
									$LicenseEmailOption = $module['ClassName'] . 'LicenseEmail';
									?>
									<div id="<?php echo $module['ClassName'] ?>id" style="display:none;">
										<form method="post">
											<table class="form-table">
												<tr valign="top">
													<td colspan="3" style="border:none"><?php _e('Please enter your WishList Products Key and Email below to activate this plugin', 'wl-contentcontrol'); ?></td>
												</tr>
												<tr valign="top">
													<th scope="row" style="border:none;white-space:nowrap;" class="WLRequired"><?php _e('WishList Products Key', 'wl-contentcontrol'); ?></th>
													<td style="border:none"><input type="text" name="LicenseKey" value="<?php echo $WishListMemberInstance->GetOption($LicenseKeyOption); ?>" size="32" /></td>
													<td style="border:none"><?php _e('(This was sent to the email you used during your purchase)', 'wl-contentcontrol'); ?></td>
												</tr>
												<tr valign="top">
													<th scope="row" style="border:none;white-space:nowrap" class="WLRequired"><?php _e('WishList Products Email', 'wl-contentcontrol'); ?></th>
													<td style="border:none"><input type="text" name="LicenseEmail" value="<?php echo $WishListMemberInstance->GetOption($LicenseEmailOption); ?>" size="32" /></td>
													<td style="border:none"><?php _e('(Please enter the email you used during your registration/purchase)', 'wl-contentcontrol'); ?></td>
												</tr>
											</table>
											<p class="submit">
												<input type="hidden" value="0" name="LicenseLastCheck" />
												<input type="hidden" value="SaveLincenseKey" name="WishListControlAction" />
												<input type="hidden" value="<?php echo $module['ClassName']; ?>" name="WishListControlModule" />
												<input type="submit" value="Save WishList Products Key" name="Submit" /> &nbsp;&nbsp;
												<a href="javascript:void(0);" onclick="wlcc_show('<?php echo $module['ClassName']; ?>','true')" >Cancel</a>
											</p>
										</form>
									</div>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<br /><br />
			<?php
		}
		/**
		 * Check the version
		 */
		function Plugin_Is_Latest($latest_ver,$this_version) {

			$ver = $this_version;
			if (preg_match('/^(\d+\.\d+)\.{' . 'GLOBALREV}$/', $this_version, $match)) {
				$ver = $match[1];
				preg_match('/^(\d+\.\d+)\.[^\.]*/', $latest_ver, $match);
				$latest_ver = $match[1];
			}
			return version_compare($latest_ver, $ver, '<=');
		}
		/**
		 * Get Wishlist License Status
		 */
		function LicenseStatus($module) {
			global $WishListMemberInstance;
			$LicenseStatusOption = $module . 'LicenseStatus';
			$LicenseKeyOption = $module . 'LicenseKey';
			$LicenseEmailOption = $module . 'LicenseEmail';
			$WPWLKey = $WishListMemberInstance->GetOption($LicenseKeyOption);
			$WPWLEmail = $WishListMemberInstance->GetOption($LicenseEmailOption);
			
			if(empty($WPWLKey) || empty($WPWLEmail)) {
				return 0;
			}else{
				return $WishListMemberInstance->GetOption($LicenseStatusOption);
			}
		}

		/* Retrieves the tooltip id
		 * @return string Tooltip
		 */

		function Tooltip($tooltpid) {
			$thisTooltip = '<a class="help" rel="#' . $tooltpid . '" href="help"><span><img src="' . $this->pluginurl . '/images/helpicon.png"></span></a>';
			return $thisTooltip;
		}

		/**
		 * Process License Status
		 */
		function ProcessKey($module) {
			global $WishListMemberInstance;

			$LicenseKeyOption = $module . 'LicenseKey';
			$LicenseEmailOption = $module . 'LicenseEmail';
			$LicenseStatusOption = $module . 'LicenseStatus';
			$LastCheckOption = $module . 'LicenseLastCheck';
			$RetriesOption = $module . 'LicenseRets';
			
			//bypass activation for
			if ($this->isLocal(strtolower(get_bloginfo('url')))) {
				$WPWLCheckResponse = '';
				$WishListMemberInstance->SaveOption($LastCheckOption, time());
				$WishListMemberInstance->SaveOption($LicenseStatusOption, 1);
				return;
			}

			$WPWLKey = $WishListMemberInstance->GetOption($LicenseKeyOption);
			$WPWLEmail = $WishListMemberInstance->GetOption($LicenseEmailOption);
			$LicenseStatus=$WishListMemberInstance->GetOption($LicenseStatusOption);
			$Retries=$WishListMemberInstance->GetOption($RetriesOption,true,true)+0;

			$isBetaTester = $WPWLEmail == 'beta@wishlistproducts.com';
			if ($isBetaTester) {
				add_action('admin_notices', array(&$this, 'BetaTester'));
				add_action('the_content', array(&$this, 'BetaTester'));
			}
			
			$WPWLLast = (int)$WishListMemberInstance->GetOption($LastCheckOption);
			$WPWLPID = $this->skus[$module];
			$WPWLCheck = md5("{$WPWLKey}_{$WPWLPID}_" . ($WPWLURL = strtolower(get_bloginfo('url'))));
			$WPWLKeyAction = $_POST['wordpress_wishlist_deactivate'] == $WPWLPID ? 'deactivate' : 'activate';
			$WPWLTime = time();
			$Month=60*60*24*7*30;
			if(empty($WPWLKey) && empty($WPWLEmail)) {
				//do not even try
				return;
			}

			if($WPWLTime-$Month>$WPWLLast || $WPWLKeyAction=='deactivate' || $LicenseStatus != 1){
				$urls=explode(',',self::ActivationURLs);
				$urlargs=array(
					'',
					'',
					urlencode($WPWLKey),
					urlencode($WPWLPID),
					urlencode($WPWLCheck),
					urlencode($WPWLEmail),
					urlencode($WPWLURL),
					urlencode($WPWLKeyAction),
					urlencode($this->modules_version[$module])
				);
				foreach($urls AS &$url){
					$urlargs[0]='http://%s/activ8.php?key=%s&pid=%d&check=%s&email=%s&url=%s&%s=1&ver=%s';
					$urlargs[1]=$url;
					$url=call_user_func_array('sprintf',$urlargs);
				};

								$WPWLStatus = $WPWLCheckResponse = 0;
				if ($WPWLKeyAction == 'deactivate' OR (!empty($WPWLKey) && !empty($WPWLEmail) && trim($WPWLKey) != '' && trim($WPWLEmail) != '')) {
					$WPWLStatus = $WPWLCheckResponse = $this->ReadURL($urls, 5);
				}

		
				if($WPWLStatus===false){
					if($Retries>=self::ActivationMaxRetries || $LicenseStatus!=1){
						$WPWLStatus = $WPWLCheckResponse = 'Unable to contact License Activation Server. <a href="http://wlplink.com/go/activation" target="_blank">Click here for more info.</a>';
					}else{
						$WishListMemberInstance->SaveOption($RetriesOption, $Retries+1, true);
						$WPWLStatus = $WishListMemberInstance->GetOption($LicenseStatusOption);
					}

					//staggered rechecks
					//if there is an error with wlm servers, check after an hour
					//so that we won't keep making requests
					$Month=60*60*24*7*30;
					$checkafter = 60 * 60 * 24 * 7;
					//For testing check after a minute
					//$checkafter = 60;
					$WishListMemberInstance->SaveOption($LastCheckOption,$WPWLTime - $Month + ($checkafter));
				}else{
					$WishListMemberInstance->SaveOption($RetriesOption, 0, true);
					$WishListMemberInstance->SaveOption($LastCheckOption,$WPWLTime);
				}
				
				$WPWLStatus = trim($WPWLStatus);
				$WishListMemberInstance->SaveOption($LicenseStatusOption,$WPWLStatus);

				if($WPWLKeyAction=='deactivate'){
					$WPWLCheckResponse = "License deactivated.";
					$WishListMemberInstance->DeleteOption($LicenseKeyOption,$LicenseEmailOption);
				}
			}
			
			$this->WLCCLicenseNotices[ $module ] = $WPWLCheckResponse;
			
			if ( $Retries > 0 ) {
				$this->WLCCActivationWarningNotices[ $module ] = 1;
				add_action('admin_notices', array(&$this, 'ActivationWarning'), 1 );
			}

			if($WishListMemberInstance->GetOption($LicenseStatusOption)!='1'){
				add_action('admin_notices', array(&$this, 'WLCCLicenseMessage'), 1 );
			}

		}
		/**
		 * Reads the content of a URL using Wordpress WP_Http class if possible
		 * @param string|array $url The URL to read. If array, then each entry is checked if the previous entry fails
		 * @param int $timeout (optional) Optional timeout. defaults to 5
		 * @param bool $file_get_contents_fallback (optional) true to fallback to using file_get_contents if WP_Http fails. defaults to false
		 * @return mixed FALSE on Error or the Content of the URL that was read
		 */
		function ReadURL($url, $timeout=null, $file_get_contents_fallback=null, $wget_fallback=null) {
			$urls = (array) $url;
			if (is_null($timeout))
				$timeout = 30;
			if (is_null($file_get_contents_fallback))
				$file_get_contents_fallback = false;
			if (is_null($wget_fallback))
				$wget_fallback = false;

			$x = false;
			foreach ($urls AS $url) {
				if (class_exists('WP_Http')) {
					$http = new WP_Http;
					$req = $http->request($url, array('timeout' => $timeout));
					$x = (is_wp_error($req) OR is_null($req) OR $req === false) ? false : ($req['response']['code'] == '200' ? $req['body'] . '' : false);
				} else {
					$file_get_contents_fallback = true;
				}

				if ($x === false && ini_get('allow_url_fopen') && $file_get_contents_fallback) {
					$x = file_get_contents($url);
				}

				if ($x === false && $wget_fallback) {
					exec('wget -T ' . $timeout . ' -q -O - "' . $url . '"', $output, $error);
					if ($error) {
						$x = false;
					} else {
						$x = trim(implode("\n", $output));
					}
				}

				if ($x !== false) {
					return $x;
				}
			}
			return $x;
		}

		/**
		 * Displays the license processing status
		 */
		function WLCCLicenseMessage( ) {
			if ( ! current_user_can('manage_options') )  return;
			foreach( $this->WLCCLicenseNotices as $module => $msg ) {
				if ( strlen( $msg ) > 1 ) {
					echo '<div class="updated fade" id="message"><p style="color:#f00"><strong><a href="#' . $module . '">' . $module . '</a><br />' .$msg . '</strong></p></div>';
				}
			}
		}


		function ActivationWarning( ) {
			if ( ! current_user_can('manage_options') )  return;
			global $WishListMemberInstance;
			foreach( $this->WLCCActivationWarningNotices as $module => $val ) {
				$RetriesOption = $module . 'LicenseRets';
				$rets = $WishListMemberInstance->GetOption($RetriesOption,true,true);
				if ( $rets && $rets>0 && $rets<self::ActivationMaxRetries ) {
					echo '<div class="error fade"><p>';
					echo __('Warning: Unable to contact License Activation Server. We will keep on trying. <a href="http://wlplink.com/go/activation" target="_blank">Click here for more info.</a>','wishlist-member');
					echo '</p></div>';
					break;
				}
			}
		}

		/**
		 * Displays the license processing status
		 */
		function WLCCMessage() {
			if ( strlen( $this->WLCCNotices ) > 1 ) {
				echo '<div class="updated fade" id="message"><p style="color:#f00"><strong>' . $this->WLCCNotices . '</strong></p></div>';
			}
		}

		/**
		 * Checks whether a url is possibly local
		 * @param string $url the url to test
		 */
		function isLocal($url) {
			$exceptions = array(
				'home.com',
				'localhost.com',
				'work.com'
			);

			$excludeable_domain = array(
				'home',
				'localhost',
				'work'
			);

			$excludeable_tld = array(
				'loc',
				'dev'
			);

			$res = parse_url($url);

			// not excludeable
			if ($res === false) {
				return false;
			}


			$host = $res['host'];
			if (stripos($host, '.')) {

				$parts = explode('.', $host);
				$tld = $parts[count($parts) - 1];
				$domain = $parts[count($parts) - 2];

				//exception to our rules?
				if (in_array($domain . "." . $tld, $exceptions)) {
					return false;
				}

				if (in_array($domain, $excludeable_domain)) {
					return true;
				}

				if (in_array($tld, $excludeable_tld)) {
					return true;
				}
			} else {
				//empty tld
				return true;
			}
			return false;
		}

		/**
		 * Displays Beta Tester Message
		 */
		function BetaTester($return) {
			global $WishListMemberInstance;
			$aff = $WishListMemberInstance->GetOption('affiliate_id');
			$url = $aff ? 'http://member.wishlistproducts.com/wlp.php?af=' . $aff : 'http://member.wishlistproducts.com/';
			$message = "This is a <strong><a href='{$url}'>WishList Member</a></strong> Beta Test Site.";
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

		function Plugin_Latest_Version($module) {
			global $WishListMemberInstance;

			$varname = $module . '_Latest_Plugin_Version';
			$latest_ver = get_transient($varname);
			if (empty($latest_ver)) {
				$url = 'http://wishlistproducts.com/download/ver.php?' . $this->vercode[$module];
				$latest_ver = $WishListMemberInstance->ReadURL($url);
				set_transient($varname, $latest_ver, 60 * 60 * 24);
			}
			return $latest_ver;
		}

		function Plugin_Download_Url($module) {
			global $WishListMemberInstance;
			$LicenseKeyOption = $module . 'LicenseKey';
			$LicenseStatusOption = $module . 'LicenseStatus';

			if ($WishListMemberInstance->GetOption($LicenseStatusOption) != 1) {
				return false;
			}
			$url = 'http://wishlistproducts.com/download/' . $WishListMemberInstance->GetOption($LicenseKeyOption) . '/==' . base64_encode(pack('i', $this->skus[$module]));
			return $url;
		}

	}

	//End Class WishListContentControl
}//End Class WishListContentControl Checking
?>