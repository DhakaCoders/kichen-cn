<?php

if(!class_exists('WishListXHRHandler')) {
	class WishListXHRHandler {
		public $plugin_instance;
		public function __construct($plugin_instance) {
			$this->plugin_instance = $plugin_instance;
			add_action('wp_head', array($this, 'init'));
		}
		public function init() {
			global $wishlist_xhr_handler_loaded;

			//run once
			if(!empty($wishlist_xhr_handler_loaded)) {
				return;
			}
			?>
			<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
			$wishlist_xhr_handler_loaded = true;
		}
	}
}