<?php

if(!class_exists('WishListTinyMCEPluginAdapter')) {
	class WishListTinyMCEPluginAdapter {
		protected $tmce;
		public function __construct($plugin) {
			global $WLMTinyMCEPluginInstance;
			if (!isset($WLMTinyMCEPluginInstance)) { //instantiate the class only once
				$WLMTinyMCEPluginInstance = new WLMTinyMCEPlugin;
				$WLMTinyMCEPluginInstance->plugin = $plugin;
				add_action('admin_init', array($WLMTinyMCEPluginInstance, 'TNMCE_PluginJS'), 1);
			}
			$this->tmce = $WLMTinyMCEPluginInstance;
		}

		public function add_special_btn($tag_name, $main_group, $sub_group, $title, $value) {

				$d = array(
					'name' => $tag_name,
					'special' => array(
							$main_group => array(
								$sub_group => array(array('title' => $title, 'value' => $value))
							)
						)
					);


				foreach($this->tmce->codes as &$plugin_code) {
					if($plugin_code['name'] == $tag_name) {
						break;
					}
				}

				if(!empty($plugin_code)) {
					$plugin_code['special'] = array_merge_recursive($plugin_code['special'], $d['special']);
				} else {
					$this->tmce->codes[] = $d;
				}


				unset($plugin_code);


		}
		public function add_mergecode_btn($tag_name, $title, $value) {
			$found = false;
			foreach($this->tmce->codes as &$tag) {
				if($tag_name == $tag['name']) {
					$found = true;
					$tag['mergecode'][] = array('title' => $title, 'value' => $value);
				}
			}
			unset($tag);

			if(!$found) {
				//create
				$tag['name'] = $tag_name;
				$tag['mergecode'][] = array('title' => $title, 'value' => $value);
				$this->tmce->codes[] = $tag;
			}
		}
		public function add_shortcode_btn($tag_name, $title, $value) {
			$found = false;
			foreach($this->tmce->codes as &$tag) {
				if($tag_name == $tag['name']) {
					$found = true;
					$tag['shortcode'][] = array('title' => $title, 'value' => $value);
				}
			}
			unset($tag);

			if(!$found) {
				//create
				$tag['name'] = $tag_name;
				$tag['shortcode'][] = array('title' => $title, 'value' => $value);
				$this->tmce->codes[] = $tag;
			}
		}
	}
}
