<?php
/**
 * Database Class for PluginName
 * @package plugin-name
 *
 * @version $Rev$
 * $LastChangedBy$
 * $LastChangedDate$
 */
if (!defined('ABSPATH')
	)die();
if (!class_exists('PluginClassName_DBMethods')) {
	/**
	 * PluginName DB Methods Class
	 * @package plugin-name
	 * @subpackage classes
	 */
	class PluginClassName_DBMethods extends PluginClassName_Core {
		/**
		 * Create Database Tables
		 */
		function CreateDBTables(){
			global $wpdb;
			$structures=array(
				"CREATE TABLE IF NOT EXISTS `{$this->TablePrefix}options` (
					`ID` bigint(20) NOT NULL AUTO_INCREMENT,
					`option_name` varchar(64) NOT NULL,
					`option_value` longtext NOT NULL,
					`autoload` varchar(20) NOT NULL DEFAULT 'yes',
					PRIMARY KEY (`ID`),
					UNIQUE KEY `option_name` (`option_name`),
					KEY `autoload` (`autoload`)
				)"
			);

			/*
			 * add more table structures here if needed
			 */

			/*
			 * end adding table structures
			 */

			/* create tables */
			foreach($structures AS $structure){
				$wpdb->query($structure);
			}

			/* reload table names */
			$this->LoadTables();
		}

		/*
		 * add other database methods
		 * here as needed
		 *
		 * DO NOT PUT WORDPRESS HOOKS HERE
		 */

	}
}
?>