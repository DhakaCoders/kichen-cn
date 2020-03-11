<?php
/*
 * Plugin Name: WishList Member&trade; 3.1
 * Plugin URI: http://member.wishlistproducts.com/
 * Description: <strong>WishList Member&trade; 3.1</strong> is the most comprehensive membership plugin for WordPress users. It allows you to create multiple membership levels, protect desired content and much more. For more WordPress tools please visit the <a href="http://wishlistproducts.com/blog" target="_blank">WishList Products Blog</a>. Requires at least WordPress 4.0 and PHP 5.4
 * Author: WishList Products
 * Version: 3.1.6649
 * Author URI: https://wishlistproducts.com/
 * License: GPLv2
 * Text Domain: wishlist-member
 * SVN: 6649
 */


define( 'WLM3_MIN_WP_VERSION', '4.0' );
define( 'WLM3_MIN_PHP_VERSION', '5.4' );
define( 'WLM3_SKU', '8901' );
define( 'WLM_ROLLBACK_PATH', WP_CONTENT_DIR . '/wishlist-rollback/wishlist-member/' );

if ( class_exists( 'WishListMember' ) || class_exists( 'WishListMember3' ) ) {
	wp_die( sprintf( '<p>Another version of WishList Member is already running.</p><p><a href="%s">Go Back</a></p>', $_SERVER['HTTP_REFERER'] ) );
}

if(!require_once( 'versioncheck.php' ) ) {
	return;
}

require_once( 'legacy/wpm.php' ); // legacy WLM

require_once( 'classes/wishlist-member3-pagination.php' );
require_once( 'classes/wishlist-member3-core.php' );
require_once( 'classes/wishlist-member3-actions.php' );
require_once( 'classes/wishlist-member3-hooks.php' );
require_once( 'classes/wishlist-member3.php' );

if ( class_exists( 'WishListMember3' ) ) {
	$WishListMemberInstance = new WishListMember3( __FILE__, WLM3_SKU, 'WishListMember', 'WishList Member', 'WishListMember' );
	require_once( 'legacy/init.php' );
}