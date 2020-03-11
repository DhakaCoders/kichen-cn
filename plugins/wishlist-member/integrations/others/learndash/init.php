<?php // initialization
if(!class_exists('WLM3_LearnDash_Hooks')) {
	class WLM3_LearnDash_Hooks {
		var $wlm;
		function __construct() {
			global $WishListMemberInstance;
			$this->wlm = $WishListMemberInstance;
			add_action('wp_ajax_wlm3_learndash_check_plugin', array($this, 'check_plugin'));
		}
		function check_plugin() {
			// extract($_POST['data']);
			$data = array(
				'status' => false,
				'message' => '',
				'courses' => array(),
				'groups' => array(),
			);
			// connect and get info
			try {
				$active_plugins  = wlm_get_active_plugins();
				if ( in_array( 'LearnDash LMS', $active_plugins ) || isset($active_plugins['sfwd-lms/sfwd_lms.php']) || is_plugin_active('sfwd-lms/sfwd_lms.php') ) {
					$data["status"] = true;
					$data["message"] = "LearnDash plugin is isntalled and activated";
					$the_posts = new WP_Query(array( 'post_type' =>  'sfwd-courses','nopaging'=>true));
					$courses = [];
					if ( count($the_posts->posts) ) {
						foreach ( $the_posts->posts as $key => $c ) {
							$courses[$c->ID] = $c->post_title;
						}
						$data["courses"] = $courses;
					} else {
						$data["message"] = "You need to create a LearnDash course in order proceed";
					}
					$the_groups = new WP_Query(array( 'post_type' =>  'groups','nopaging'=>true));
					$groups = [];
					if ( count($the_groups->posts) ) {
						foreach ( $the_groups->posts as $key => $c ) {
							$groups[$c->ID] = $c->post_title;
						}
						$data["groups"] = $groups;
					}

					if ( !function_exists('ld_update_course_access') ) {
						$data["status"] = false;
						$data["message"] = "LearnDash LMS is activated but the functions needed are missing. Please contact support.";
					}
				} else {
					$data["message"] = "Please install and activate your LearnDash plugin";
				}
			} catch(Exception $e) {
				$data['message'] = $e->getMessage();
			}
			wp_die( json_encode($data) );
		}
	}
	new WLM3_LearnDash_Hooks;
}