<?php

/**
 * WishList Member ShortCodes
 * @author Mike Lopez <mjglopez@gmail.com>
 * @package wishlistmember
 *
 * @version $Rev: 6424 $
 * $LastChangedBy: mike $
 * $LastChangedDate: 2019-10-16 10:06:12 -0400 (Wed, 16 Oct 2019) $
 */
class WishListMemberShortCode {

	var $shortcodes = array(
		// array('wlm_profilephoto'), 'Profile Photo', 'userinfo',
		array('wlm_firstname', 'wlmfirstname', 'firstname'), 'First Name', 'userinfo',
		array('wlm_lastname', 'wlmlastname', 'lastname'), 'Last Name', 'userinfo',
		array('wlm_email', 'wlmemail', 'email'), 'Email Address', 'userinfo',
		array('wlm_memberlevel', 'wlmmemberlevel', 'memberlevel'), 'Membership Levels', 'userinfo',
		array('wlm_username', 'wlmusername', 'username'), 'Username', 'userinfo',
		array('wlm_profileurl', 'wlmprofileurl', 'profileurl'), 'Profile URL', 'userinfo',
		array('wlm_password', 'wlmpassword', 'password'), 'Password', 'userinfo',
		array('wlm_autogen_password'), 'Auto Generated Password', 'userinfo',
		array('wlm_website', 'wlmwebsite', 'website'), 'URL', 'userinfo',
		array('wlm_aim', 'wlmaim', 'aim'), 'AIM ID', 'userinfo',
		array('wlm_yim', 'wlmyim', 'yim'), 'Yahoo ID', 'userinfo',
		array('wlm_jabber', 'wlmjabber', 'jabber'), 'Jabber ID', 'userinfo',
		array('wlm_biography', 'wlmbiography', 'biography'), 'Biography', 'userinfo',
		array('wlm_company', 'wlmcompany', 'company'), 'Company', 'userinfo',
		array('wlm_address', 'wlmaddress', 'address'), 'Address', 'userinfo',
		array('wlm_address1', 'wlmaddress1', 'address1'), 'Address 1', 'userinfo',
		array('wlm_address2', 'wlmaddress2', 'address2'), 'Address 2', 'userinfo',
		array('wlm_city', 'wlmcity', 'city'), 'City', 'userinfo',
		array('wlm_state', 'wlmstate', 'state'), 'State', 'userinfo',
		array('wlm_zip', 'wlmzip', 'zip'), 'Zip', 'userinfo',
		array('wlm_country', 'wlmcountry', 'country'), 'Country', 'userinfo',
		array('wlm_loginurl', 'wlm_loginurl', 'loginurl'), 'Login URL', 'userinfo',
		array('wlm_rss', 'wlmrss'), 'RSS Feed URL', 'rss',
		array('wlm_expiration', 'wlm_expiry', 'wlmexpiry'), 'Level Expiry Date', 'levelinfo',
		array('wlm_joindate', 'wlmjoindate'), 'Level Join Date', 'levelinfo',
		array('wlm_payperpost'), 'Registered Pay Per Post', 'registered_payperpost',
	);
	var $custom_user_data = array();
	var $shortcode_functions = array();
	var $wpm_levels = array();

	function __construct() {
		global $WishListMemberInstance, $wpdb;
		if ( isset($WishListMemberInstance) ) {
		    
		    $this->wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');
		    $wpm_levels = &$this->wpm_levels;
		    $wpm_levels = $wpm_levels ? $wpm_levels : array(); //make sure the $wpm_levels is an array

		    // Initiate custom registration fields array
		    //$this->custom_user_data = $wpdb->get_col("SELECT DISTINCT SUBSTRING(`option_name` FROM 8) FROM `{$WishListMemberInstance->Tables->user_options}` WHERE `option_name` LIKE 'custom\_%' AND `option_name` <> 'custom_'");
		    $this->custom_user_data = $wpdb->get_col("SELECT SUBSTRING(`option_name` FROM 8) FROM `{$WishListMemberInstance->Tables->user_options}` WHERE `option_name` LIKE 'custom\_%' AND `option_name` <> 'custom\_' GROUP BY `option_name`");

		    // User Information
		    $shortcodes = $this->shortcodes;
		    for ($i = 0; $i < count($shortcodes); $i = $i + 3) {
			    foreach ((array) $shortcodes[$i] AS $shortcode) {
				    $this->_add_shortcode($shortcode, array(&$this, $shortcodes[$i + 2]));
			    }
		    }

		    // Get and Post data passed on Registration
		    $shortcodes = array(
			    'wlmuser', 'wlm_user'
		    );
		    foreach ($shortcodes AS $shortcode) {
			    $this->_add_shortcode($shortcode, array(&$this, 'get_and_post'));
		    }

		    // Powered By WishList Member
		    $shortcodes = array(
			    'wlm_counter', 'wlmcounter'
		    );
		    foreach ($shortcodes AS $shortcode) {
			    add_shortcode($shortcode, array(&$this, 'counter'));
		    }

		    $shortcodes = array('wlm_min_passlength', 'wlmminpasslength');

		    foreach ($shortcodes as $shortcode) {
			    add_shortcode($shortcode, array($this, 'min_password_length'));
		    }

		    // Login Form
		    $shortcodes = array(
			    'wlm_loginform', 'wlmloginform', 'loginform'
		    );
		    foreach ($shortcodes AS $shortcode) {
			    add_shortcode($shortcode, array(&$this, 'login'));
		    }

		    // Login Form
		    $shortcodes = array(
			    'wlm_profileform'
		    );
		    foreach ($shortcodes AS $shortcode) {
			    add_shortcode($shortcode, array(&$this, 'profile_form'));
		    }

		    // Membership level with access to post/page
		    $shortcodes = array(
			    'wlm_contentlevels', 'wlmcontentlevels'
		    );
		    foreach ($shortcodes AS $shortcode) {
			    add_shortcode($shortcode, array(&$this, 'content_levels_list'));
		    }

		    // Custom Registration Fields
		    $shortcodes = array(
			    'wlm_custom', 'wlmcustom'
		    );
		    foreach ($shortcodes AS $shortcode) {
			    $this->_add_shortcode($shortcode, array(&$this, 'custom_registration_fields'));
		    }

		    // Is Member and Non Member
		    $shortcodes = array(
			    'wlm_ismember', 'wlmismember'
		    );
		    foreach ($shortcodes AS $shortcode) {
			    $this->_add_shortcode($shortcode, array(&$this, 'ismember'));
		    }

		    $shortcodes = array(
			    'wlm_nonmember', 'wlmnonmember'
		    );
		    foreach ($shortcodes AS $shortcode) {
			    $this->_add_shortcode($shortcode, array(&$this, 'nonmember'));
		    }

		    $invalid_shortcode_chars = '@[<>&/\[\]\x00-\x20]@';

		    $shortcodes = array(
			    'wlm_register', 'wlmregister' , 'register'
		    );

		    // Disable old register shotrtcodes if configured
		    // This will reduce the number of shortcodes WLM is registering, 
		    // Specially helpful with sites with large number of levels
		    if(!$WishListMemberInstance->GetOption('disable_legacy_reg_shortcodes')) {
			    // Registration Form Tags
			    foreach ($wpm_levels AS $level) {
				    if (!preg_match($invalid_shortcode_chars, $level['name'])) {
					    $shortcodes[] = 'wlm_register_' . urlencode($level['name']);
					    // $shortcodes[] = 'wlmregister_' . $level['name'];
				    }
			    }
			   
		    }
		    
		    foreach ($shortcodes AS $shortcode) {
				$this->_add_shortcode($shortcode, array(&$this, 'regform'));
			}

		    //has access
		    $shortcodes = array('has_access', 'wlm_has_access');

		    foreach ($shortcodes AS $shortcode) {
			    $this->_add_shortcode($shortcode, array(&$this, 'hasaccess'));
		    }

		    //has no access
		    $shortcodes = array('has_no_access', 'wlm_has_no_access');

		    foreach ($shortcodes AS $shortcode) {
			    $this->_add_shortcode($shortcode, array(&$this, 'hasnoaccess'));
		    }

		    // Private Tags
		    $shortcodes = array(
			    'wlm_private', 'wlmprivate' , 'private'
		    );
		    // Disable old private tags if configured
		    // This will reduce the number of shortcodes WLM is registering, 
		    // Specially helpful with sites with large number of levels
		    if(!$WishListMemberInstance->GetOption('disable_legacy_private_tags')) {
			    foreach ($wpm_levels AS $level) {
				    if (!preg_match($invalid_shortcode_chars, $level['name'])) {
					    $shortcodes[] = 'wlm_private_' . $level['name'];
	    				// $shortcodes[] = 'wlmprivate_' . $level['name'];
				    }
			    }
		    }
		    foreach ($shortcodes AS $shortcode) {
			    $this->_add_shortcode($shortcode, array(&$this, 'private_tags'));
		    }

	    	// Reverse Private Tag
		    $shortcodes = array(
			    '!wlm_private', '!wlmprivate' , '!private'
		    );
		    // Disable old private tags if configured
		    // This will reduce the number of shortcodes WLM is registering, 
		    // Specially helpful with sites with large number of levels
		    if(!$WishListMemberInstance->GetOption('disable_legacy_private_tags')) {
			    foreach ($wpm_levels AS $level) {
				    if (!preg_match($invalid_shortcode_chars, $level['name'])) {
					    $shortcodes[] = '!private_' . $level['name'];
					    $shortcodes[] = '!wlm_private_' . $level['name'];
					    // $shortcodes[] = '!wlmprivate_' . $level['name'];
				    }
			    }
			}
		    foreach ($shortcodes AS $shortcode) {
			    $this->_add_shortcode($shortcode, array(&$this, 'reverse_private_tags'));
		    }

		    //User Payperpost
		    $shortcodes = array(
			    'wlm_userpayperpost', 'wlmuserpayperpost'
		    );
		    foreach ($shortcodes AS $shortcode) {
			    $this->_add_shortcode($shortcode, array(&$this, 'user_payperpost'));
		    }

		    // Process our shortcodes in the sidebar too!
		    if (!is_admin())
			    add_filter('widget_text', 'do_shortcode', 11);


			//fix where shortcodes are not supported in input tag value attribute
			//https://make.wordpress.org/core/2015/07/23/changes-to-the-shortcode-api/
			add_filter('wp_kses_allowed_html', array(&$this, 'wlm_kses_allowed_tags'), 10, 2);
	    }
	}

	function wlm_kses_allowed_tags( $allowed_tags, $context ) {
		if ( is_admin() || !in_the_loop() ) return $allowed_tags;
		if (  $context == "post" && is_array( $allowed_tags ) ) {
			if ( ! isset( $allowed_tags['input'] ) ) {
				$allowed_tags['input'] = array( "value"=>true );
			} else {
				//other might have added some attributes for input
				//this will prevent from overwriting other attributes
				if (  ! isset( $allowed_tags['input']['value'] ) || ! $allowed_tags['input']['value']  ) {
					$allowed_tags['input']['value'] = true;
				}
			}
		}
		return $allowed_tags;
	}

	function ismember($atts, $content, $code) {
		global $WishListMemberInstance;
		global $wp_query;

		$is_userpost = false;

		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}

		if (wlm_arrval($current_user->caps,'administrator')) {
			return do_shortcode($content);
		}


		if ($WishListMemberInstance->GetOption('payperpost_ismember')) {
			$is_userpost = in_array($wp_query->post->ID, $WishListMemberInstance->GetMembershipContent($wp_query->post->post_type, 'U-' . $current_user->ID));
		}

		$user_levels = $WishListMemberInstance->GetMembershipLevels($current_user->ID, null, true, null, true);
		if (count($user_levels) || $is_userpost) {
			return do_shortcode($content);
		} else {
			return '';
		}
	}

	function nonmember($atts, $content, $code) {
		global $WishListMemberInstance;

		global $wp_query;

		$is_userpost = false;

		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}

		if (wlm_arrval($current_user->caps,'administrator')) {
			return do_shortcode($content);
		}

		if ($WishListMemberInstance->GetOption('payperpost_ismember')) {
			$is_userpost = in_array($wp_query->post->ID, $WishListMemberInstance->GetMembershipContent($wp_query->post->post_type, 'U-' . $current_user->ID));
		}

		$user_levels = $WishListMemberInstance->GetMembershipLevels($current_user->ID, null, true, null, true);
		if (count($user_levels) || $is_userpost) {
			return '';
		} else {
			return do_shortcode($content);
		}
	}

	function regform($atts, $content, $code) {
		global $WishListMemberInstance;

		if(in_array($code, array('wlm_register', 'wlmregister', 'register'))) {
			$level_name = implode(' ', $atts);
		} else {
			if (substr($code, 0, 12) == 'wlm_register') {
				$level_name = substr($code, 13);
			} else {
				$level_name = substr($code, 12);
			}
		}

		foreach ($this->wpm_levels AS $level_id => $level) {
			if (trim(strtoupper($level['name'])) == trim(strtoupper(html_entity_decode($level_name)))) {
				return do_shortcode($WishListMemberInstance->RegContent($level_id, true));
			}
		}
		return '';
	}

	function private_tags($atts, $content, $code) {
		global $WishListMemberInstance;
		$atts = is_array( $atts ) ? array(implode(" ", $atts)) : $atts; //lets glue attributes together for level names with spaces

		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}

		if (wlm_arrval($current_user->caps,'administrator')) {
			return do_shortcode($content);
		}

		$user_levels = $WishListMemberInstance->GetMembershipLevels($current_user->ID, null, true, null, true);

		$level_names = array();

		if ($code == 'wlm_private' OR $code == 'wlmprivate') {
			foreach ($atts AS $key => $value) {
				$value = trim($value,"'");
				if (is_int($key)) {
					$level_names = array_merge($level_names, explode('|', $value));
					unset($atts[$key]);
				}
			}
		} else {
			if (substr($code, 0, 11) == 'wlm_private') {
				$level_names[] = substr($code, 12);
			} else {
				$level_names[] = substr($code, 11);
			}
		}

		$level_names=array_map('trim',$level_names);
		$level_ids = array();

		foreach ($this->wpm_levels AS $level_id => $level) {
			$level_ids[$level['name']] = $level_id;
		}

		$match = false;
		foreach ($level_names AS $level_name) {
			$level_id = $level_ids[$level_name];
			if (in_array($level_id, $user_levels)) {
				$match = true;
				break;
			}
		}

		if ($match) {
			return do_shortcode($content);
		} else {
			$protectmsg = $WishListMemberInstance->GetOption('private_tag_protect_msg');
			$protectmsg = str_replace('[level]', implode(', ', $level_names), $protectmsg);
			$protectmsg = do_shortcode($protectmsg);
			return $protectmsg;
		}
	}

    function reverse_private_tags($atts, $content, $code) {
		global $WishListMemberInstance;
		$atts = is_array( $atts ) ? array(implode(" ", $atts)) : $atts; //lets glue attributes together for level names with spaces

		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}

		if (wlm_arrval($current_user->caps,'administrator')) {
			return do_shortcode($content);
		}

		$user_levels = $WishListMemberInstance->GetMembershipLevels($current_user->ID, null, true, null, true);
		$level_names = array();

		if ($code == '!private' OR $code == '!wlm_private') {
			foreach ($atts AS $key => $value) {
				$value = trim($value,"'");
				if (is_int($key)) {
					$level_names = array_merge($level_names, explode('|', $value));
					unset($atts[$key]);
				}
			}
		} else {
			if (substr($code, 0, 8) == '!private') {
				$level_names[] = substr($code, 9);
			} else {
				$level_names[] = substr($code, 13);
			}
		}

		$level_names=array_map('trim',$level_names);

		//lets get the valid levels in the tag
		$tag_levels = array();
		foreach ($this->wpm_levels AS $level_id => $level) {
			if ( in_array( $level['name'], $level_names ) ) {
				 $tag_levels[] = $level_id;
			}
		}

		//now we have the users level and the levels in the tag
		//lets check if one of levels in the tag is in users level
		$user_match_level = array_intersect( $tag_levels, $user_levels );
               
		if( count( $user_match_level ) > 0 ) { //if theres a level in the tag that users have
			//display the message
			$protectmsg = $WishListMemberInstance->GetOption('reverse_private_tag_protect_msg');
			$protectmsg = str_replace('[level]', implode(', ', $level_names), $protectmsg);
			return $protectmsg;
           
		} else { //if user does not have all levels in the tag, return the content
             return do_shortcode($content);
		}
	}

	function userinfo($atts, $content, $code) {
		global $WishListMemberInstance, $wlm_cookies;

		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}

		$wpm_useraddress = $WishListMemberInstance->Get_UserMeta($current_user->ID, 'wpm_useraddress');
		static $password = null;
		switch ($code) {
			/*
			case 'wlm_profilephoto':
				$src = trim( $WishListMemberInstance->Get_UserMeta( $current_user->ID, 'profile_photo' ) ) ?: $WishListMemberInstance->pluginURL3 . '/assets/images/grey.png';

				$size = wlm_arrval( $atts, 'size' );
				if( $size < 1 ) $size = 150;

				$span_style = 'height: ' . $size . 'px; width: ' . $size . 'px; border-radius: 100%; overflow: hidden; display: inline-block';

				$image_style = 'max-width: none; max-height: 100%; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);';

				$photo = sprintf( '<span style="%s" class="wlm-profile-photo"><img style="%s" src="%s" alt="Profile photo of %s" border="0"></span>', $span_style, $image_style, $src, $current_user->display_name );
				return $photo;
				break;
			*/
			case 'firstname':
			case 'wlm_firstname':
			case 'wlmfirstname':
				return $current_user->first_name;
				break;
			case 'lastname':
			case 'wlm_lastname':
			case 'wlmlastname':
				return $current_user->last_name;
				break;
			case 'email':
			case 'wlm_email':
			case 'wlmemail':
				return $current_user->user_email;
				break;
			case 'memberlevel':
			case 'wlm_memberlevel':
			case 'wlmmemberlevel':
				$user_levels=$WishListMemberInstance->GetMembershipLevels($current_user->ID, $names = TRUE, $activeOnly = null, $no_cache = null, $no_userlevels = true );
				if ($user_levels ){
					return $user_levels;
				}else{
					return __('No Membership Level', 'wishlist-member');
				}
				
				break;
			case 'username':
			case 'wlm_username':
			case 'wlmusername':
				return $current_user->user_login;
				break;
			case 'profileurl':
			case 'wlm_profileurl':
			case 'wlmprofileurl':
				return get_bloginfo('wpurl') . '/wp-admin/profile.php';
				break;
			case 'password':
			case 'wlm_password':
			case 'wlmpassword':
				/* password shortcode retired to prevent security issues */
				return '********';
				break;
			case 'wlm_autogen_password':
				return empty($wlm_cookies->wlm_autogen_pass) ? '********' : $wlm_cookies->wlm_autogen_pass;
				break;
			case 'website':
			case 'wlm_website':
			case 'wlmwebsite':
				return $current_user->user_url;
				break;
			case 'aim':
			case 'wlm_aim':
			case 'wlmaim':
				return $current_user->aim;
				break;
			case 'yim':
			case 'wlm_yim':
			case 'wlmyim':
				return $current_user->yim;
				break;
			case 'jabber':
			case 'wlm_jabber':
			case 'wlmjabber':
				return $current_user->jabber;
				break;
			case 'biography':
			case 'wlm_biography':
			case 'wlmbiography':
				return $current_user->description;
				break;
			case 'company':
			case 'wlm_company':
			case 'wlmcompany':
				return $wpm_useraddress['company'];
				break;
			case 'address':
			case 'wlm_address':
			case 'wlmaddress':
				$address = $wpm_useraddress['address1'];
				if (!empty($wpm_useraddress['address2'])) {
					$address.='<br />' . $wpm_useraddress['address2'];
				}
				return $address;
				break;
			case 'address1':
			case 'wlm_address1':
			case 'wlmaddress1':
				return $wpm_useraddress['address1'];
				break;
			case 'address2':
			case 'wlm_address2':
			case 'wlmaddress2':
				return $wpm_useraddress['address2'];
				break;
			case 'city':
			case 'wlm_city':
			case 'wlmcity':
				return $wpm_useraddress['city'];
				break;
			case 'state':
			case 'wlm_state':
			case 'wlmstate':
				return $wpm_useraddress['state'];
				break;
			case 'zip':
			case 'wlm_zip':
			case 'wlmzip':
				return $wpm_useraddress['zip'];
				break;
			case 'country':
			case 'wlm_country':
			case 'wlmcountry':
				return $wpm_useraddress['country'];
				break;
			case 'loginurl':
			case 'wlm_loginurl':
			case 'wlmloginurl':
				return wp_login_url();
				break;
		}
	}

	function get_and_post($atts, $content, $code) {
		global $WishListMemberInstance;
		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}

		switch ($atts) {
			case 'post':
				$userpost = (array) $WishListMemberInstance->WLMDecrypt($current_user->wlm_reg_post);
				if ($atts[1]) {
					return $userpost[$atts[1]];
				} else {
					return nl2br(print_r($userpost, true));
				}
				break;
			case 'get':
				$userpost = (array) $WishListMemberInstance->WLMDecrypt($current_user->wlm_reg_get);
				if ($atts[1]) {
					return $userpost[$atts[1]];
				} else {
					return nl2br(print_r($userpost, true));
				}
				break;
		}
	}

	function rss($atts, $content, $code) {
		return get_bloginfo('rss2_url');
	}

	function levelinfo($atts, $content, $code) {
		global $WishListMemberInstance;
		static $wpm_levels = null, $wpm_level_names = null;

		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}

		if (is_null($wpm_levels)) {
			$wpm_levels = (array) $WishListMemberInstance->GetOption('wpm_levels');
		}

		if (is_null($wpm_level_names)) {
			$wpm_level_names = array();
			foreach ($wpm_levels AS $id => $level) {
				$wpm_level_names[trim($level['name'])] = $id;
			}
		}
		switch ($code) {
			case 'wlm_expiry':
			case 'wlmexpiry':
			case 'wlm_expiration';
				if ($atts['format']) {
					$format = $atts['format'];
					unset($atts['format']);
				} else {
					$format = get_option('date_format');
				}

				$level_name = trim(implode(' ', $atts));
				$level_id = $wpm_level_names[$level_name];
				$expiry_date = $WishListMemberInstance->LevelExpireDate($level_id, $current_user->ID);
				if ($expiry_date !== false) {
					return date_i18n($format, $expiry_date);
				}
				break;
			case 'wlm_joindate':
			case 'wlmjoindate':
				if ($atts['format']) {
					$format = $atts['format'];
					unset($atts['format']);
				} else {
					$format = get_option('date_format');
				}

				$level_name = trim(implode(' ', $atts));
				$level_id = $wpm_level_names[$level_name];
				$join_date = $WishListMemberInstance->UserLevelTimestamp($current_user->ID, $level_id);
				if ($join_date !== false) {
					return date_i18n($format, $join_date);
				}
				break;
		}
		return '';
	}

	function counter($atts, $content, $code) {
		global $WishListMemberInstance;
		$x = $WishListMemberInstance->ReadURL('http://wishlistactivation.com/wlm-sites.txt');
		if ($x !== false && $x > 0) {
			$WishListMemberInstance->SaveOption('wlm_counter', $x);
		} else {
			$x = $WishListMemberInstance->GetOption('wlm_counter');
		}
		return $x;
	}

	function login($atts, $content, $code) {
		global $WishListMemberInstance, $wp;
		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}

		if (!$current_user->ID) {
			if (trim( $code ) == 'wlm_profileform') {
				$redirect = home_url( add_query_arg( array(), $wp->request ) );
			} elseif ($WishListMemberInstance->GetOption('enable_login_redirect_override')) {
				$redirect = !empty($_GET['wlfrom']) ? esc_attr(stripslashes($_GET['wlfrom'])) : 'wishlistmember';
			} else {
				$redirect = '';
			}
			$loginurl = esc_url(site_url( 'wp-login.php', 'login_post' ));
			$loginurl2 = wp_lostpassword_url();

			$txtLost = __('Lost your Password?', 'wishlist-member');

			$username_field = wlm_form_field( [
				'label' => __( 'Username', 'wishlist-member' ),
				'type' => 'text',
				'name' => 'log',
			] );
			$password_field = wlm_form_field( [
				'label' => __( 'Password', 'wishlist-member' ),
				'type' => 'password',
				'name' => 'pwd',
			] );
			$remember_field = wlm_form_field( [
				'type' => 'checkbox',
				'name' => 'rememberme',
				'options' => [ 'forever' => __( 'Remember Me', 'wishlist-member' ) ],
			] );
			$submit_button = wlm_form_field( [
				'type' => 'submit',
				'name' => 'wp-submit',
				'value' => __( 'Login', 'wishlist-member' ),
			] );

			$form = <<<STRING
<form action="{$loginurl}" method="post" class="wlm_inpageloginform">
	<input type="hidden" name="wlm_redirect_to" value="{$redirect}" />
	<input type="hidden" name="redirect_to" value="{$redirect}" />
	<div class="wlm3-form">
		{$username_field}
		{$password_field}
		{$remember_field}
		{$submit_button}
		<p>
			<a href="{$loginurl2}">{$txtLost}</a>					
		</p>
	</div>
</form>
STRING;
		} else {
			$form = $WishListMemberInstance->Widget(array(), true);
		}
		$form = "<div class='WishListMember_LoginMergeCode'>{$form}</div>";
		return $form;
	}

	function profile_form( $atts, $content, $code ) {
		global $wp;
		global $WishListMemberInstance;
		static $processed;
		if( !empty( $processed ) ) return ''; // process only once
		$processed = true;

		if( !is_user_logged_in() ) {
			if( !empty( $atts['nologin'] ) ) {
				return '';
			} else {
				return $this->login( [], $content, 'wlm_profileform' );
			}
		}

		$user = wp_get_current_user();
		$options = [
			$user->user_login => $user->user_login,
			$user->nickname => $user->nickname,
		];
		if( $user->first_name ) $options[ $user->first_name ] = $user->first_name;
		if( $user->last_name ) $options[ $user->last_name ] = $user->last_name;
		if( $user->first_name && $user->last_name ) {
			$fl = implode(' ', [ $user->first_name, $user->last_name ] );
			$lf = implode(' ', [ $user->last_name, $user->first_name ] );
			$options[ $fl ] = $fl;
			$options[ $lf ] = $lf;
		}

		$required = [];
		if( isset( $_GET['wlm_required'] ) && is_array( $_GET['wlm_required'] ) && $_GET['wlm_required'] ) {
			foreach( $_GET['wlm_required'] AS $r ) {
				switch( $r ) {
					case 'nickname':
						$required[] = sprintf('<p>%s</p>', __( 'Nickname required', 'wishlist-member' ) );
					break;
					case 'user_email':
						$required[] = sprintf('<p>%s</p>', __( 'Email required', 'wishlist-member' ) );
					break;
					case 'new_pass':
						$required[] = sprintf('<p>%s</p>', __( 'Password not accepted', 'wishlist-member' ) );
					break;
				}
			}
		}

		$fields = '';
		$fields .= wlm_form_field( ['type' => 'hidden', 'name' => '_wlm3_nonce', 'value' => wp_create_nonce( 'update-profile_' . $user->ID ) ] );
		$fields .= wlm_form_field( ['type' => 'hidden', 'name' => 'referrer', 'value' => $_SERVER['REQUEST_URI'] ] );
		$fields .= wlm_form_field( ['type' => 'hidden', 'name' => 'WishListMemberAction', 'value' => 'UpdateUserProfile' ] );
		/*
		 * profile photo
		$fields .= wlm_form_field( ['type' => 'media_uploader', 'name' => 'profile_photo', 'label' => __( 'Profile Photo', 'wishlist-member' ), 'value' => $WishListMemberInstance->Get_UserMeta( $user->ID, 'profile_photo' )] );
		 */
		$fields .= wlm_form_field( ['type' => 'text', 'name' => 'first_name', 'onchange' => 'wlm3_update_displayname(this)', 'value' => $user->first_name, 'label' => __( 'First Name', 'wishlist-member' ) ] );
		$fields .= wlm_form_field( ['type' => 'text', 'name' => 'last_name', 'onchange' => 'wlm3_update_displayname(this)', 'value' => $user->last_name, 'label' => __( 'Last Name', 'wishlist-member' ) ] );
		$fields .= wlm_form_field( ['type' => 'text', 'name' => 'nickname', 'onchange' => 'wlm3_update_displayname(this)', 'value' => $user->nickname, 'label' => __( 'Nickname', 'wishlist-member' ) ] );
		$fields .= wlm_form_field( ['type' => 'select', 'name' => 'display_name', 'value' => $user->display_name, 'options' => $options, 'label' => __( 'Display Name', 'wishlist-member' ) ] );
		$fields .= wlm_form_field( ['type' => 'email', 'name' => 'user_email', 'value' => $user->user_email, 'label' => __( 'Email', 'wishlist-member' ) ] );
		$fields .= wlm_form_field( ['type' => 'checkbox', 'name' => 'wlm_subscribe', 'value' => (int) ( !(bool) $WishListMemberInstance->Get_UserMeta( $user->ID, 'wlm_unsubscribe' ) ), 'options' => [ '1' => __( 'Subscribed to Mailing List', 'wishlist-member' ) ] ] );
		$fields .= wlm_form_field( ['type' => 'password_generator', 'name' => 'new_pass', 'value' => '', 'label' => __( 'New Password', 'wishlist-member' ) ] );
		$fields .= wlm_form_field( ['type' => 'submit', 'name' => 'save-profile', 'value' => __( 'Update Profile', 'wishlist-member' ) ] );

		$javascript = <<<STRING
<script type="text/javascript">
function wlm3_update_displayname(elm) {
	if(!elm.value.trim()) return;
	var elms = document.forms['wishlist-member-profile-form'].elements;
	var options = elms.display_name.options;
	if(elm.name == 'nickname') {
		options[options.length] = new Option(elm.value);
	}
	if(elm.name == 'first_name' || elm.name == 'last_name') {
		options[options.length] = new Option(elm.value);
		var fn = elms.first_name.value.trim();
		var ln = elms.last_name.value.trim();
		if(fn && ln) {
			options[options.length] = new Option(fn + ' ' + ln);
			options[options.length] = new Option(ln + ' ' + fn);
		}
	}
}
</script>
STRING;

		if( $required ) {
			$required = sprintf( '<div class="wlm3-profile-error">%s</div>', implode( '', $required ) );
		} else {
			$required = '';
		}

		if( wlm_arrval( $_REQUEST, 'wlm_profile' ) == 'saved' ) {
			$required = sprintf( '<div class="wlm3-profile-ok"><p>%s</p></div>', __( 'Profile saved', 'wishlist-member' ) );
		} else {
			$message = '';
		}

		return sprintf('<form name="wishlist-member-profile-form" method="POST" action="%s"><div id="wishlist-member-profile-form" class="wlm3-form">%s%s%s</div></form>%s', user_admin_url(), $message, $required, $fields, $javascript);
	}

	function content_levels_list($atts, $content, $code){
		global $WishListMemberInstance;
		$wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');
		$type_list = array('comma','ol','ul');
		if( !is_array( $atts ) ) {
		    $atts = array();
		}
		$atts['link_target']  = isset($atts['link_target'] ) ? $atts['link_target'] :"_blank";
		$atts['type']  = isset($atts['type'] ) ? $atts['type'] :"comma";
		$atts['class'] = isset($atts['class'] ) ? $atts['class']: 'wlm_contentlevels';
		$atts['show_link'] = isset($atts['show_link'] ) ? $atts['show_link']: 1;
		$atts['salespage_only'] = isset($atts['salespage_only'] ) ? $atts['salespage_only']: 1;

		$atts['type'] = in_array($atts['type'],$type_list) ? $atts['type']: 'comma';
		$atts['link_target'] = $atts['link_target'] != "" ? "target='{$atts['link_target']}'": "";
		$atts['class'] = $atts['class'] != "" ? $atts['class']: 'wlm_contentlevels';
		$atts['show_link'] = $atts['show_link'] == 0 ? false: true;
		$atts['salespage_only'] = $atts['salespage_only'] == 0 ? false: true;

		$redirect = !empty($_GET['wlfrom']) ? $_GET['wlfrom'] : false;
		$post_id = url_to_postid($redirect);
		$ret = array();
		if($redirect && $post_id !== 0){
			$ptype=get_post_type($post_id);
			$levels = $WishListMemberInstance->GetContentLevels($ptype,$post_id);
			foreach($levels as $level){
				$salespage = trim(wlm_arrval($wpm_levels[$level], 'salespage'));
				$enable_salespage = (bool) wlm_arrval($wpm_levels[$level], 'enable_salespage');
				if(isset($wpm_levels[$level])){
					if($atts['show_link'] && $salespage != "" && $enable_salespage){
						$ret[]= "<a class='{$atts['class']}_link' href='{$wpm_levels[$level]['salespage']}' {$atts['link_target']}>{$wpm_levels[$level]['name']}</a>";
					}else{
						if(!$atts['salespage_only']){
							$ret[] = $wpm_levels[$level]['name'];
						}
					}
				}
			}
		}
		if($ret){
			if($atts['type'] == 'comma'){
				$holder = implode(",",$ret);
				$holder = trim($holder,",");
			}else{
				$holder = "<{$atts['type']} class='{$atts['class']}'><li>";
				$holder .= implode("</li><li>",$ret);
				$holder .= "</li></{$atts['type']}>";
			}
			$ret = $holder;
		}
		return $ret;
	}

	function custom_registration_fields($atts, $content, $code) {
		global $WishListMemberInstance, $wpdb;
		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}

		$atts = array_values($atts);
		if (!is_array($atts[0])) {
			switch ($atts[0]) {
				case '':
					$query = $wpdb->prepare("SELECT * FROM `{$WishListMemberInstance->Tables->user_options}` WHERE `user_id`=%d AND `option_name` LIKE 'custom\_%%'", $current_user->ID);
					$results = $wpdb->get_results($query);
					$results = $WishListMemberInstance->GetUserCustomFields($current_user->ID);
					if (!empty($results)) {
						$output = array();
						foreach ($results AS $key => $value) {
							$output[] = sprintf('<li>%s : %s</li>', $key, implode('<br />', (array) $value));
						}
						$output = trim(implode('', $output));
						if ($output) {
							return '<ul>' . $output . '</ul>';
						}
					}
					break;
				default:
					$field = 'custom_' . $atts[0];
					return trim($WishListMemberInstance->Get_UserMeta($current_user->ID, $field));
					return implode('<br />', (array) $WishListMemberInstance->Get_UserMeta($current_user->ID, $field));
			}
		}
	}

	function _add_shortcode($shortcode, $function) {
		$this->shortcode_functions[$shortcode] = $function;
		add_shortcode($shortcode, $function);
	}

	function manual_process($user_id, $content, $dataonly = false) {
		$user = get_userdata($user_id);
		if ($user->ID) {
			$GLOBALS['wlm_shortcode_user'] = $user;
			$pattern = get_shortcode_regex();
			preg_match_all('/' . $pattern . '/s', $content, $matches, PREG_SET_ORDER);
			if (is_array($matches) && count($matches)) {
				$data = array();
				foreach ($matches AS $match) {
					$scode = $match[2];
					$code = $match[0];
					if (isset($this->shortcode_functions[$scode])) {
						if (!isset($data[$code])) {
							$data[$code] = do_shortcode_tag($match);
						}
					}
				}
				if ($dataonly == false) {
					$content = str_replace(array_keys($data), $data, $content);
				} else {
					$content = $data;
				}
			}
		}
		return $content;
	}

	function min_password_length() {
		global $WishListMemberInstance, $wpdb;
		$min_value = $WishListMemberInstance->GetOption('min_passlength');
		if (!$min_value) {
			$min_value = 8;
		}
		return $min_value;
	}
	function hasaccess($atts, $content) {
		extract(shortcode_atts(array(
			'post' => null
		), $atts));

		$pid = $post;
		if(empty($pid)) {
			global $post;
			$pid = $post->ID;
		}

		global $current_user;
		global $WishListMemberInstance;

		if($WishListMemberInstance->HasAccess($current_user->ID, $pid)) {
			return $content;
		}
		return null;
	}
	function hasnoaccess($atts, $content) {
		extract(shortcode_atts(array(
			'post' => null
		), $atts));

		$pid = $post;
		if(empty($pid)) {
			global $post;
			$pid = $post->ID;
		}

		global $current_user;
		global $WishListMemberInstance;

		if($WishListMemberInstance->HasAccess($current_user->ID, $pid)) {
			return null;
		}
		return $content;
	}

	function user_payperpost($atts){
		global $WishListMemberInstance;
		if (wlm_arrval(wlm_arrval($GLOBALS,'wlm_shortcode_user'),'ID')) {
			$current_user = $GLOBALS['wlm_shortcode_user'];
		} else {
			$current_user = wlm_arrval($GLOBALS,'current_user');
		}
		$ppp_uid = "U-" . $current_user->ID;
		$user_ppplist = $WishListMemberInstance->GetUser_PayPerPost($ppp_uid);
		$ppp_list = '<ul>';
			foreach ($user_ppplist as $list) {
				$link = get_permalink($list->content_id);
				$ppp_list .= '<li><a href="' . $link . '">' . get_the_title($list->content_id). '</a></li>';
			}

		$ppp_list .= '</ul>';
		return "" . $ppp_list ."";
	}

	function registered_payperpost( $atts ) {
		global $WishListMemberInstance;
		$ppp = trim( wlm_arrval( $_GET, 'l' ) );
		if( !$ppp || !$WishListMemberInstance->IsPPPLevel( $ppp ) || !preg_match('/\d+$/', $ppp, $match ) ) return '';

		$title = get_the_title( $match[0] );
		$url = get_permalink( $match[0] );

		if( !$url ) return '';
		if( !$title ) $title = $url;

		return sprintf( '<a href="%s">%s</a>', $url, $title );
	}

}

?>