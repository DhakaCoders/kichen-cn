<?php
/**
 * Nav Menu Helper Class
 * @author Ronaldo Reymundo <ronaldo@wishlistproducts.com>
 */

class WishListNavMenu {

	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 1 );
			add_action( 'admin_head-nav-menus.php', array( $this,'add_wlm_metabox') );
			// Switch the admin walker.
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ) );
			// Add new fields via hook.
			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_custom_fields' ), 10, 4 );
			// Save the menu item meta.
			add_action( 'wp_update_nav_menu_item', array( $this, 'nav_update'), 10, 3 );
		} else {
			add_filter( 'wp_setup_nav_menu_item', array( $this,'wlm_setup_nav_menu_item') );
			add_filter( 'wp_nav_menu_objects', array( $this,'wlm_wp_nav_menu_objects') );
		}
	}

	public function admin_enqueue_scripts( $hook ) {	
		global $wp_styles;

		if( $hook == 'nav-menus.php') {
			wlm_enqueue_style( 'select2', 'select2.min.css' );
			wlm_enqueue_style( 'select2-bootstrap', 'select2-bootstrap.min.css' );
			wlm_enqueue_script( 'select2', 'select2.min.js', ['jquery'], '', true );
		}
	}

	public function add_custom_fields( $item_id, $item, $depth, $args ) {

		// If the menu item was added from WLM nav menu inserter then
		// Hide Input text for URL since we're going to use dropdown..
		if($item->classes[0] == 'wlm_login_logout_navs22') {
			echo "<script type='text/javascript'> 
				jQuery( document ).ready(function() {
					jQuery('#edit-menu-item-url-".$item_id."').parent().hide();
					jQuery('.wlm-select').select2();
				});
				</script>";
		} else {
			return; // Only run the rest of the script if it's WLM related nav menus
		}

		global $WishListMemberInstance;
		$pages = get_pages();
		$wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');
		// Set Nonce
		$wlm_nav_nonce = wp_create_nonce( 'wlm-nav-menu-nonce-name' );
		echo '<input type="hidden" name="wlm_nav_nonce" value="'. $wlm_nav_nonce.'" />';
		echo '<input type="hidden" name="nav_item_id[]" value="'. $item_id.'" />';

		switch($item->url) {
			case '#wlm_login#';
				?>
				<p class="description description-wide"><label>URL:</label>
					<select name="input_wlm_nav_menu_<?php echo $item_id; ?>" class="wlm-select"  style="width:98%;" data-placeholder="Select a page">
						<option value="0"><?php _e('WordPress Default', 'wishlist-member'); ?></option>
						<?php foreach ($pages AS $page): 
							$wlm_nav_id = $WishListMemberInstance->GetOption('wlm_nav_menu_item_'.$item_id);
							?>
							<option value="<?php echo $page->ID ?>" <?php echo ($page->ID == $wlm_nav_id) ? ' selected="true"' : ""; ?>><?php echo $page->post_title; ?></option>
						<?php endforeach; ?>
					</select>
					<br>
					<span class="field-move-visual-label" aria-hidden="true">* A Login menu item will only be displayed for users who are not logged in.</span> <br><br>
				</p>
				<?php	
				break;
			case '#wlm_logout#';
				?>
				<p class="description description-wide"><label>URL:</label>
					<select name="input_wlm_nav_menu_<?php echo $item_id; ?>" class="wlm-select"  style="width:98%;" data-placeholder="Select a page">
						<option value="0"><?php _e('WishList Member Default', 'wishlist-member'); ?></option>
						<?php foreach ($pages AS $page): 
							$wlm_nav_id = $WishListMemberInstance->GetOption('wlm_nav_menu_item_'.$item_id);
							?>
							<option value="<?php echo $page->ID ?>" <?php echo ($page->ID == $wlm_nav_id) ? ' selected="true"' : ""; ?>><?php echo $page->post_title; ?></option>
						<?php endforeach; ?>
					</select>
					<br>
					<span class="field-move-visual-label" aria-hidden="true">* A Log Out menu item will only be displayed for users who are already logged in.</span> <br><br>
				</p>
				<?php	
				break;
		}
	}

	public function nav_update( $menu_id, $menu_item_db_id, $menu_item_args ) {

		// Verify this came from our screen and with proper authorization.
		if ( ! isset( $_POST['wlm_nav_nonce'] ) || ! wp_verify_nonce( $_POST['wlm_nav_nonce'], 'wlm-nav-menu-nonce-name' ) ){
			return;
		}

		global $WishListMemberInstance;

		if(isset($_POST['input_wlm_nav_menu_'.$menu_item_db_id])) {
			$page_id = $_POST['input_wlm_nav_menu_'.$menu_item_db_id];
			$WishListMemberInstance->SaveOption('wlm_nav_menu_item_'.$menu_item_db_id, $page_id);
		}
	}

	public function edit_nav_menu_walker( $walker ) {
		if( ! class_exists( 'WLM_Walker_Nav_Menu' ) ){
			require_once(dirname(__FILE__) . '/WishListNavMenuWalker.php');
        }
		return 'WLM_Walker_Nav_Menu';
	}

	/* Replaces the #keyword# by the correct links with nonce ect */
	public function wlm_setup_nav_menu_item( $item ) {

		global $pagenow;

		if ( $pagenow != 'nav-menus.php' && ! defined( 'DOING_AJAX' ) && isset( $item->url ) && strstr( $item->url, '#wlm_' ) != '' ) {

			$item_url = substr( $item->url, 0, strpos( $item->url, '#', 1 ) ) . '#';
			$item_redirect = str_replace( $item_url, '', $item->url );

			if ( $item_redirect == '%currentpage%' ) {
				$item_redirect = $_SERVER['REQUEST_URI'];
			}
			global $WishListMemberInstance;

			switch ( $item_url ) {
				case '#wlm_login#' :
					if ( is_user_logged_in() ) {
						return $item;
					}

					$login_page_id = $WishListMemberInstance->GetOption('wlm_nav_menu_item_'.$item->ID);

					if($login_page_id)
						$login_redirect = get_permalink($WishListMemberInstance->GetOption('wlm_nav_menu_item_'.$item->ID));
					
					if($login_redirect) {
						$item->url = $login_redirect;
					} else {
						$item->url = wp_login_url( $item_redirect );
					}
					break;
				case '#wlm_logout#' :

					if ( ! is_user_logged_in() ) {
						return $item;
					}

					$logout_page_id = $WishListMemberInstance->GetOption('wlm_nav_menu_item_'.$item->ID);

					if($logout_page_id)
						$logout_redirect = get_permalink($WishListMemberInstance->GetOption('wlm_nav_menu_item_'.$item->ID));
					
					if($logout_redirect)
						$item_redirect = $logout_redirect;

					$item->url = wp_logout_url( $item_redirect );
					break;
			}
			$item->url = esc_url( $item->url );
		}
		return $item;
	}

	public function wlm_wp_nav_menu_objects( $sorted_menu_items ) {

		foreach ( $sorted_menu_items as $k => $item ) {
			if ( strstr( $item->url, '#wlm_' ) != '' ) {
				unset( $sorted_menu_items[ $k ] );
			}
		}
		return $sorted_menu_items;
	}

	public function add_wlm_metabox() {
		add_meta_box( 'wlm', __( 'WishList Member', 'wishlist-member' ), array( $this, 'wlm_metabox' ), 'nav-menus', 'side', 'default' );
	}

	public function wlm_metabox() {
		global $nav_menu_selected_id;

		$wlm_elems = array(
			'#wlm_login#'	    => __( 'Login', 'wishlist-member' ),
			'#wlm_logout#'	    => __( 'Log Out', 'wishlist-member' ),
		);
		$wlm_logitems = array(
			'db_id' => 0,
			'object' => 'custom',
			'object_id',
			'menu_item_parent' => 0,
			'type' => 'custom',
			'type_label' => 'Page',
			'title',
			'target' => '',
			'attr_title' => '',
			'template' => '1',
			'classes' => array('wlm_login_logout_navs22'),
			'xfn' => '',

		);

		$wlm_elems_obj = array();
		foreach ( $wlm_elems as $value => $title ) {
			$wlm_elems_obj[ $title ] 		= (object) $wlm_logitems;
			$wlm_elems_obj[ $title ]->object_id	= esc_attr( $value );
			$wlm_elems_obj[ $title ]->title	= esc_attr( $title );
			$wlm_elems_obj[ $title ]->url	= esc_attr( $value );
		}

		$walker = new Walker_Nav_Menu_Checklist( array() );
		?>
		<div id="wlm-login-links" class="loginlinksdiv">

			<div id="tabs-panel-wlm-login-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
				<ul id="wlm-login-linkschecklist" class="list:wlm-login-links categorychecklist form-no-clear">
					<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $wlm_elems_obj ), 0, (object) array( 'walker' => $walker ) ); ?>
				</ul>
			</div>

			<p class="button-controls">
				<span class="add-to-menu">
					<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'wishlist-member' ); ?>" name="add-wlm-login-links-menu-item" id="submit-wlm-login-links" />
					<span class="spinner"></span>
				</span>
			</p>

		</div>
		<?php
	}
}

$wlm_nav = new WishListNavMenu();