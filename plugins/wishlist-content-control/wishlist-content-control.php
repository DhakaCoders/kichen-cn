<?php
/*
 * Plugin Name: WishList Content Control
 * Plugin URI: http://member.wishlistproducts.com/
 * Description: Content Control is a plugin that works perfectly with <strong>WishList Member</strong>. It allows you to "control" the contents of your site based on your membership levels.   For more Wordpress tools please visit the <a href="http://wishlistproducts.com/blog" target="_blank">WishList Products Blog</a>. Requires at least Wordpress 3.0 and PHP 5.2
 * Author: WishList Products
 * Version: 1.1.28
 * Author URI: http://wishlistproducts.com/support-options/
 * SVN: 28
 * License: GPLv2
 */
require_once(dirname(__FILE__).'/classes.php');

if (!function_exists("wl_contentcontrol_activation")) {
    function wl_contentcontrol_activation() {
        global $WishListMemberInstance;
        if ( ! class_exists('WishListMember') || ! isset( $WishListMemberInstance ) ) {
            deactivate_plugins(__FILE__);
            $msg ='This plugin requires WishList Member. Please install and activate WishList Member in order to use this plugin.<br ><br ><br ><a href="'.$_SERVER['HTTP_REFERER'].'">Go Back</a>';
            wp_die( __( $msg) );
        }
    }
}
if (!function_exists("wl_contentcontrol_deactivation")) {
    function wl_contentcontrol_deactivation() {
        if (class_exists('WishListMember') ) {
            global $WishListMemberInstance;
        }
    }
}
//Initialize the admin panel
if (!function_exists("wl_contentcontrol_menu")){
    function wl_contentcontrol_menu(){
        global $WishListContentControl, $WishListMemberInstance;
        if( ! isset( $WishListMemberInstance ) ) return;
        if(!defined('WPWLTOPMENU')){
            add_menu_page('WishList Plugins', 'WishList Plugins', 'manage_options', 'WPWishList','AdminPage', $WishListContentControl->pluginurl . '/images/WishListIcon.png');
            define('WPWLTOPMENU','WPWishList');
        }
        add_submenu_page(WPWLTOPMENU, 'WishList Content Control', 'WL Content Control','manage_options','ContentControl',array(&$WishListContentControl,'SettingsPage'));
        // Submenu for "Other Tab"
        $found=false;
        foreach($GLOBALS['submenu'] AS $key=>$sm){
            foreach ($sm AS $k=>$m){
                if($m[2]=='WPWLOther'){
                    unset($GLOBALS['submenu'][$key][$k]);
                    $found=true;
                    $GLOBALS['submenu'][$key][]=$m;
                    break;
                }
            }
        }
        if(!$found)add_submenu_page(WPWLTOPMENU,'Other WP WishList Plugins','Other','manage_options','WPWLOther','');
        unset($GLOBALS['submenu']['WPWishList'][0]);
    }
}

if (!function_exists("content_control_folder_migrate")){
    function content_control_folder_migrate() {
        $cleared = false;
        $plugins = get_plugins();
        $plugin_files = preg_grep('/wl-contentcontrol\.php$/', array_keys($plugins));
        if ( count( $plugin_files ) <= 0 ) return;

        foreach($plugin_files AS $plugin) {
            if($plugins[$plugin]['Name'] == 'WishList Content Control') {
                $cleared = true;
                deactivate_plugins($plugin );
                delete_plugins(array($plugin) );
            }
        }
        if($cleared) {
            wp_clean_plugins_cache();
        }
    }
}

//Initialize Content Control Class
if (class_exists("WishListContentControl")){ //check if class exist before calling it.
    $WishListContentControl = new WishListContentControl(__FILE__,'ContentControl'); //set the Content Control class
}
if (isset($WishListContentControl)){ //check if  Content Control class is set
    // *call activation routine
    register_activation_hook(__FILE__, 'wl_contentcontrol_activation');

    // *call deactivation routine
    // *Removed this deactivation hook so that it wont disable modules
    // *I just can remove the code but i think i'll hold on to this a bit
    //register_deactivation_hook(__FILE__,'wl_contentcontrol_deactivation');

    // *call the menu routine
    add_action('admin_menu', 'wl_contentcontrol_menu');
    add_action('init',array(&$WishListContentControl,'Init'));

    //clean the old content control plugin
    add_action('admin_init','content_control_folder_migrate');
}
?>