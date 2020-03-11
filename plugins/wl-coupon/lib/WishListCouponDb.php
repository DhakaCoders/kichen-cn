<?php
class WishListCouponDb extends WishListDb {

	public $promotions;
	public $promotion_contents;
	public $promotion_coupons;
	public function __construct($prefix, $plugin_instance) {
		parent::__construct($prefix, $plugin_instance);
		$this->promotions = $this->prefix.'promotions';
		$this->tracking = $this->prefix.'tracking';
		$this->promotion_coupons = $this->prefix.'promotion_coupons';
	}
	public function create_tables() {
		parent::create_tables();
		global $wpdb;

		$structures = array(
			"CREATE TABLE IF NOT EXISTS `{$this->promotions}` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`name` varchar(64) NOT NULL,
				`default_payment_link` tinytext NOT NULL,
				`content` tinytext,
				`label` varchar(64) DEFAULT NULL,
				`valid_text` varchar(64) DEFAULT NULL,
				`invalid_text` varchar(64) DEFAULT NULL,
				`apply_button_text` varchar(64) DEFAULT NULL,
				`pay_button_text` varchar(64) DEFAULT NULL,
				`style` varchar(64) DEFAULT NULL,
				`creation_date` datetime NOT NULL,
				PRIMARY KEY (`id`)
			)",
			"CREATE TABLE IF NOT EXISTS `{$this->promotion_coupons}` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`promotion_id` bigint(20) NOT NULL,
				`coupon_code` varchar(64) NOT NULL,
				`payment_link` tinytext,
				`valid_date_from` date DEFAULT NULL,
				`valid_date_to` date DEFAULT NULL,
				`valid_num_days_after_reg` int(10) DEFAULT NULL,
				`valid_num_days_after_reg_level` varchar(64) DEFAULT NULL,
				`valid_num_tries` int(10) DEFAULT NULL,
				`valid_num_tries_remaining` int(10) DEFAULT NULL,
				PRIMARY KEY (`id`)
			)",
			"CREATE TABLE IF NOT EXISTS `{$this->tracking}` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`sess_id` varchar(64) NOT NULL,
				`coupon_id` int(10) NOT NULL,
				`user_id` bigint(20) DEFAULT NULL,
				`status` bigint(10) NOT NULL,
				`created` datetime NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1",
		);

		/* create tables */
		foreach($structures AS $structure){
			$wpdb->query($structure);
		}
	}

	public function get_remaining_tries($coupon_id) {
		global $wpdb;

		$q = $wpdb->prepare("SELECT * FROM $this->promotion_coupons WHERE id=%d", $coupon_id);
		$coupon = $wpdb->get_row($q);

		if(empty($coupon->valid_num_tries)) {
			return false;
		}


		$q = "SELECT count(*) FROM $this->tracking WHERE coupon_id=%d AND status=%d";
		$q = $wpdb->prepare($q, $coupon_id, TRCK_STATUS_FINSH);

		$used      = $wpdb->get_var($q);
		$remaining = $coupon->valid_num_tries - $used;

		if($remaining <= 0) {
			return 0;
		}
		return $remaining;

	}
	public function get_coupons($promotion_id=null) {
		global $wpdb;

		if(!empty($promotion_id)) {
			$where = $wpdb->prepare("WHERE p.id = %d", $promotion_id);
		}

		$q = "SELECT *, p.id as promotion_id, p.name as promotion_name, p.content as promotion_content,"
		." pc.id as coupon_id  FROM {$this->promotion_coupons} pc"
		. " LEFT JOIN {$this->promotions} p ON (pc.promotion_id=p.id) $where ORDER BY p.id ASC";

		//error_log($q);

		$res = $wpdb->get_results($q);
		foreach($res as &$r) {
			$r->valid_date_from           = strtotime($r->valid_date_from) <= 0 ? null : date('m-d-Y', strtotime($r->valid_date_from));
			$r->valid_date_to             = strtotime($r->valid_date_to) <= 0? null : date('m-d-Y', strtotime($r->valid_date_to));
			$r->valid_num_tries           = empty($r->valid_num_tries) ? null : $r->valid_num_tries;
			$r->valid_num_days_after_reg  = empty($r->valid_num_days_after_reg) ? null : $r->valid_num_days_after_reg;
			$r->valid_num_tries_remaining = $this->get_remaining_tries($r->coupon_id);

		}
		return $res;
	}

	public function get_coupon($id) {
		global $wpdb;

		$q = $wpdb->prepare("SELECT *, p.id as promotion_id, p.name as promotion_name, p.content as promotion_content,"
		." pc.id as coupon_id FROM {$this->promotion_coupons} pc"
		. " LEFT JOIN {$this->promotions} p ON (pc.promotion_id=p.id) WHERE pc.id=%d ORDER BY p.id ASC", $id);

		$res = $wpdb->get_row($q);
		if(!empty($res)) {
			$res->valid_date_from          = strtotime($res->valid_date_from) <= 0 ? null : date('m-d-Y', strtotime($res->valid_date_from));
			$res->valid_date_to            =  strtotime($res->valid_date_to) <= 0 ? null : date('m-d-Y', strtotime($res->valid_date_to));
			$res->valid_num_tries          = empty($res->valid_num_tries) ? null : $res->valid_num_tries;
			$res->valid_num_days_after_reg = empty($res->valid_num_days_after_reg) ? null : $res->valid_num_days_after_reg;
			$res->valid_num_tries_remaining = $this->get_remaining_tries($res->coupon_id);

		}
		return $res;

	}

	public function get_promotions() {
		global $wpdb;
		$res = $wpdb->get_results("SELECT * FROM {$this->promotions}");
		return $res;
	}

	public function create_promotion($data) {
		global $wpdb;
		$q = $wpdb->prepare("SELECT * FROM {$this->promotions} WHERE name LIKE %s", $data['name'] .'%');
		$res = $wpdb->get_results($q);

		if(count($res) > 0) {
			$data['name'] = sprintf("%s (%s)", $data['name'], count($res));
		}


		$q = $wpdb->insert($this->promotions, $data);
		return $this->get_promotion($wpdb->insert_id);
	}
	public function get_promotion($id) {
		global $wpdb;
		$q = $wpdb->prepare("SELECT * FROM {$this->promotions} WHERE id=%d", $id);
		$res = $wpdb->get_row($q);
		return $res;
	}
	public function redeem_coupon($id) {
		global $wpdb;
		$cpn = $this->get_coupon($id);
		if($cpn->valid_num_tries_remaining < 1) {
			return;
		}

		$q = $wpdb->prepare("UPDATE {$this->promotion_coupons} SET valid_num_tries_remaining=%d WHERE id=%d", $cpn->valid_num_tries_remaining-1, $id);
		$wpdb->query($q);


	}

	public function delete_promotion($id) {
		global $wpdb;
		$wpdb->delete($this->promotion_coupons, array('promotion_id' => $id));
		$wpdb->delete($this->promotions, array('id' => $id));
	}
	public function delete_coupon($id) {
		global $wpdb;
		$wpdb->delete($this->promotion_coupons, array('id' => $id));
	}

	public function create_coupon($data) {
		global $wpdb;
		$q = $wpdb->insert($this->promotion_coupons, $data);
		return $this->get_coupon($wpdb->insert_id);
	}
	public function update_coupon($id, $data) {
		global $wpdb;

		$coupon = $this->get_coupon($id);

		$data['valid_num_tries_remaining'] = $coupon->valid_num_tries_remaining;
		if($coupon->valid_num_tries != $data['valid_num_tries']) {
			$data['valid_num_tries_remaining'] = $data['valid_num_tries'];
		}

		if(!empty($data['valid_date_from'])) {
			$data['valid_date_from'] = date("Y-m-d", strtotime(str_replace('-', '/', $data['valid_date_from'])));
		}
		if(!empty($data['valid_date_to'])) {
			$data['valid_date_to'] = date("Y-m-d", strtotime(str_replace('-', '/', $data['valid_date_to'])));
		}

		if(empty($data['valid_num_days_after_reg'])) {
			$data['valid_num_days_after_reg_level'] = null;
		}

		$q = $wpdb->prepare("UPDATE {$this->promotion_coupons} SET coupon_code=%s,"
			." payment_link=%s, valid_date_from=%s, valid_date_to=%s, valid_num_days_after_reg=%d,"
			." valid_num_days_after_reg_level=%s, valid_num_tries=%d, valid_num_tries_remaining=%d WHERE id=%d",
			$data['coupon_code'],
			$data['payment_link'],
			$data['valid_date_from'],
			$data['valid_date_to'],
			$data['valid_num_days_after_reg'],
			$data['valid_num_days_after_reg_level'],
			$data['valid_num_tries'],
			$data['valid_num_tries_remaining'],
			$id);

		$wpdb->query($q);
	}

	public function update_promotion($id, $data) {
		global $wpdb;

		$filtered['name'] = $data['name'];
		$filtered['valid_text'] = $data['valid_text'];
		$filtered['invalid_text'] = $data['invalid_text'];
		$filtered['apply_button_text'] = $data['apply_button_text'];
		$filtered['pay_button_text'] = $data['pay_button_text'];
		$filtered['default_payment_link'] = $data['default_payment_link'];
		$filtered['style'] = $data['style'];
		$filtered['label'] = $data['label'];

		$wpdb->update($this->promotions, $filtered, array('id' => $id));
		$wpdb->query($q);
	}
	public function update_tracking_status($status, $coupon_id) {
		global $wpdb, $current_user;
		$sess_id = session_id();

		$where = array(
			'sess_id'   => $sess_id,
			'status'    => $status,
			'coupon_id' => $coupon_id
		);

		$q = $wpdb->prepare("SELECT * FROM {$this->tracking} WHERE sess_id=%s AND status=%d AND coupon_id=%d",
			$where['sess_id'], $where['status'], $where['coupon_id']);

		//error_log(serialize($q));

		$exists = $wpdb->get_row($q);

		$data = array(
			'sess_id'   => $sess_id,
			'status'    => $status,
			'coupon_id' => $coupon_id,
			'user_id'   => $current_user->ID,
			'created'   => date('Y-m-d H:i:s')
		);

		if(!empty($exists)) {
			error_log('update');
			$wpdb->update($this->tracking, $data, array('id' => $exists->id));
		} else {
			error_log('insert');
			$wpdb->insert($this->tracking, $data);
		}


	}
	public function validate_coupon($code, $promotion_id, $more_args = array()) {
		$coupons = $this->get_coupons($promotion_id);

		$coupon = false;
		foreach($coupons as $c) {
			if(strtoupper($code) == strtoupper($c->coupon_code)) {
				$coupon = $c;
			}
		}
		$promotion = $this->get_promotion($promotion_id);
		$err = array('status' => false, 'msg' => $promotion->invalid_text);

		if(empty($coupon)) {
			//error_log('coupon does not exist');
			return $err;
		}

		//do not let the same user apply the coupon after a previous one has already completed
		$status = $this->plugin_instance->set_tracking_status(TRCK_STATUS_APPLY, $coupon->coupon_id);
		if($status !== true) {
			//error_log('coupon already redeemed');
			return $err;
		}

		//check for remaining coupon items
		if($coupon->valid_num_tries > 0 && $coupon->valid_num_tries_remaining < 1) {
			//error_log('no coupons remaining');
			return $err;
		}

		//check for date range
		$current         = time();
		$valid_date_from = strtotime(str_replace('-', '/', $coupon->valid_date_from));
		$valid_date_to   = strtotime(str_replace('-', '/', $coupon->valid_date_to));

		if($valid_date_from && $current < $valid_date_from) {
			//error_log('promotion has not started');
			return $err;
		}

		if($valid_date_to && $current > $valid_date_to) {
			//error_log('promotion has already ended');
			return $err;
		}

		//check for valid reg date
		if(!empty($coupon->valid_num_days_after_reg)) {
			if(!is_user_logged_in()) {
				return $err;
			}

			global $current_user;
			global $WishListMemberInstance;
			$ts = $WishListMemberInstance->UserLevelTimestamp($current_user->ID, $coupon->valid_num_days_after_reg_level);
			if(empty($ts)) {
				return $err;
			}

			$days = (time() - $ts) / (60*60*24);
			if($days > $coupon->valid_num_days_after_reg) {
				return $err;
			}

		}
		return array('status' => true,  'msg' => $coupon->valid_text, 'cpnid' => $coupon->coupon_id);
	}

	public function get_report($filters = array()) {
		global $wpdb;

		$where = null;
		if(!empty($filters['status'])) {
			$where[] = $wpdb->prepare('status=%d', $filters['status']);
		}

		if(!empty($filters['date_from'])) {
			$where[] = $wpdb->prepare('created > %s', date('Y-m-d', strtotime($filters['date_from'])));
		}

		if(!empty($filters['date_to'])) {
			$where[] = $wpdb->prepare('created < %s', date('Y-m-d', strtotime($filters['date_to'])));
		}

		if(!empty($filters['promotion'])) {
			$where[] = $wpdb->prepare('p.id=%d', $filters['promotion']);
		}
		if(!empty($filters['coupon'])) {
			$where[] = $wpdb->prepare('c.id=%d', $filters['coupon']);
		}

		$where[] = $wpdb->prepare('status <> %d', TRCK_STATUS_VRFYD);

		if(!empty($where)) {
			$where = ' WHERE ' . implode(" AND ", $where);
		}


		$limit = $wpdb->prepare("LIMIT %d, %d", $filters['limit'] * ($filters['offset'] - 1), $filters['limit']);


		$q  = "SELECT SQL_CALC_FOUND_ROWS t.*, u.*, p.name as promotion, c.coupon_code FROM {$this->tracking} t "
		." LEFT JOIN $wpdb->users u ON (u.ID=t.user_ID)"
		." JOIN {$this->promotion_coupons} c ON (c.id=t.coupon_id)"
		." JOIN {$this->promotions} p ON (c.promotion_id=p.id)"
		." $where ORDER BY t.created DESC $limit ";

		$res['result'] = $wpdb->get_results($q);
		$res['count'] = $wpdb->get_var("SELECT FOUND_ROWS()", 0, 0);

		return $res;
	}
	public function remove_style($id) {
		$styles = $this->get_available_styles();
		$style = $styles[$id];
		@unlink($style['filename']);
	}
	public function clone_style($id) {
		$styles = $this->get_available_styles();

		$count = 1;
		foreach($styles as $style) {
			if(preg_match("/$id/", $style['name'])) {
				$count++;
			}
		}

		$style           = $styles[$id];
		$post['name']    = $style['name'] . " (".$count.")";
		$post['content'] = $style['content'];

		$file['screenshot']['error']    = 0;
		$file['screenshot']['name']     = basename($style['screenshot']);
		$file['screenshot']['tmp_name'] = $style['screenshot'];
		return $this->create_style($post, $file);
 	}

 	public function update_style($id, $post, $file) {
 		$styles    = $this->get_available_styles();
		$old_style = $styles[$id];


		if($id != trim($post['name'])) {
			$check_style = $this->get_style($post['name']);
			if(!empty($check_style) && $check_style['name'] != $old_style['name']) {
				return false;
			}
		}


		$name      = trim(stripslashes($post['name']));
		$content   = stripslashes($post['content']);

		if(!empty($file) && $file['screenshot']['error'] == 0) {
			$status = wp_upload_bits( $file['screenshot']['name'], null, file_get_contents($file['screenshot']['tmp_name']));
			$screenshot = $status['url'];
		} else {
			$screenshot = $old_style['screenshot'];
		}



		$header = sprintf("<!--\nname: %s\nscreenshot: %s\n-->", $name, $screenshot);
		$filename = preg_replace('/\W/', '-', $name)  . '.php';


		$ifp = @ fopen( $this->plugin_instance->get_plugin_dir() .'res/styles/'.$filename , 'wb' );
		if ( ! $ifp ) {
			return array( 'error' => sprintf( __( 'Could not write file %s' ), $new_file ) );
		}



		@fwrite( $ifp, $header );
		@fwrite( $ifp, $content );
		fclose( $ifp );

		if($filename != basename($old_style['filename'])) {
			unlink($old_style['filename']);
		}

		return $name;
	}
	public function get_style($name) {
		$styles = $this->get_available_styles();
		foreach($styles as $s) {
			if(strtolower($s['name']) == strtolower($name)) {
				return $s;
			}
		}
		return false;
	}
	public function create_style($post, $file) {
		$name    = trim(stripslashes($post['name']));

		if($this->get_style($name)) {
			return false;
		}


		$content = stripslashes($post['content']);
		if(!empty($file) && $file['screenshot']['error'] == 0) {
			$status = wp_upload_bits( $file['screenshot']['name'], null, file_get_contents($file['screenshot']['tmp_name']));
			$screenshot = $status['url'];
		}

		$header = sprintf("<!--\nname: %s\nscreenshot: %s\n-->", $name, $screenshot);
		$filename = preg_replace('/\W/', '-', $name)  . '.php';


		$ifp = @ fopen( $this->plugin_instance->get_plugin_dir() .'res/styles/'.$filename , 'wb' );
		if ( ! $ifp ) {
			return array( 'error' => sprintf( __( 'Could not write file %s' ), $new_file ) );
		}

		@fwrite( $ifp, $header );
		@fwrite( $ifp, $content );
		fclose( $ifp );
		return $name;
	}
	private function _style_sorter($a, $b) {
		return strcmp($a['name'], $b['name']);
	}
	public function get_available_styles() {
		$styles_dir = $this->plugin_instance->get_plugin_dir() .'res/styles/*.php';
		$styles     = array();

		foreach(glob($styles_dir) as $s) {
			$file = file_get_contents($s);

			preg_match('/name:(.*)/', $file, $matches_name);
			preg_match('/screenshot:(.*)/', $file, $matches_screenshot);
			preg_match('/default:(.*)/', $file, $matches_default);

			$content = explode('-->', $file, 2);
			$content = trim($content[1]);


			$screenshot = trim($matches_screenshot[1]);
			if(empty($screenshot)) {
				$screenshot = $this->plugin_instance->get_plugin_url() . 'assets/images/no-screenshot.png';
			}
			if(stripos($screenshot, 'http') === false) {
				$screenshot = $this->plugin_instance->get_plugin_url() . 'res/styles/'. $screenshot;
			}
			$style = array(
				'filename'   	=> $s,
				'name'       	=> trim($matches_name[1]),
				'screenshot' 	=> $screenshot,
				'default'    	=> !empty($matches_default[1]),
				'content'		=> $content
			);

			$styles[trim($matches_name[1])] = $style;
		}
		uasort($styles, array($this, '_style_sorter'));
		return $styles;
	}
}
