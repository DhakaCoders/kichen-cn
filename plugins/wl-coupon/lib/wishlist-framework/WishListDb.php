<?php

if(!class_exists('WishListDb')) {
	class WishListDb {
		public $plugin_instance;
		public $prefix;

		//tables
		public $options;

		public function __construct($prefix, $plugin_instance=null) {
			global $wpdb;
			$this->plugin_instance = $plugin_instance;
			$this->prefix          = $wpdb->prefix . $prefix;
			$this->options         = $this->prefix.'options';
		}
		public function create_tables() {
			global $wpdb;

			if (!empty($wpdb->charset))
				$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
			if (!empty($wpdb->collate))
				$charset_collate .= " COLLATE {$wpdb->collate}";

			$structures=array(
				"CREATE TABLE IF NOT EXISTS `{$this->prefix}options` (
					`ID` bigint(20) NOT NULL AUTO_INCREMENT,
					`option_name` varchar(64) NOT NULL,
					`option_value` longtext NOT NULL,
					`autoload` varchar(20) NOT NULL DEFAULT 'yes',
					PRIMARY KEY (`ID`),
					UNIQUE KEY `option_name` (`option_name`),
					KEY `autoload` (`autoload`)
				){$charset_collate};"
			);


			/* create tables */
			foreach($structures AS $structure){
				$wpdb->query($structure);
			}
		}
	}
}