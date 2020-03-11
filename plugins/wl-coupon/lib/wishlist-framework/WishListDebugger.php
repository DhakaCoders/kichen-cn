<?php
if(!class_exists('WishListDebugger')) {
	class WishListDebugger {

		private $plugin;
		private $log_file;
		public function __construct($plugin) {
			$this->plugin = $plugin;
			$this->log_file = $plugin->get_plugin_dir() . strtolower($plugin->get_name()) . '.log';
		}
		public function build_log_message($message) {
			$doing_cron = '(site)';
			if(defined('DOING_CRON') && DOING_CRON) {
				$doing_cron = '(cron)';
			}

			$pid = getmypid();

			$time = date('Y-m-d H:i:s');
			$log_msg = sprintf("\n[%s]%s(%s): %s", $time, $doing_cron, $pid, $message);
			return $log_msg;

		}
		public function log($message) {
			$log_file = $this->log_file;
			$enabled = $this->plugin->get_option('enable_debug');
			$enabled = true;
			if(!$enabled) {
				return false;
			}

			$log_msg = $this->build_log_message($message);
			$fp = @fopen($log_file, 'a+');
			if(!$fp) {
				//switch to  database logging
				//going to be slow as log grows in size
				$log_msg = $this->plugin->get_option('wishlist_debug_str').$log_msg;
				$this->plugin->update_option('debug_str', $log_msg);
			} else {
				//file logging
				fwrite($fp, $log_msg);
				fclose($fp);
			}

		}
		public function fetch_logs() {
			$log_file = $this->log_file;
			if(is_writable($log_file)) {
				return file_get_contents($log_file);
			}
			return $this->plugin->get_option('debug_str');
		}
		public function clear_logs() {
			$log_file = $this->log_file;
			if(is_writable($log_file)) {
				fopen($log_file, 'w');
				fclose($fp);

			} else {
				$this->plugin->update_option('debug_str', null);
			}
		}
	}
}
