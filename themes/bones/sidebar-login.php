 <!-- Standard Page Side Content -->
<aside id="sidebar-login" class="sidebar m-all t-1of3 d-2of7 last-col cf" role="complementary">
 
    <?php 
    /*
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        */
    
        if (is_active_sidebar( 'sidebar-standard' ) ) {
            dynamic_sidebar( 'sidebar-standard' );
        }
    
        // if user logged in show download videos menu
        if(is_user_logged_in()) {
            
            // get the current user level from WP more important is global $user.
            $curr_wp_user = wp_get_current_user();

            // Get user levels from WishlistMembers
            $curr_wpu_levels = WLMAPI::GetUserLevels($curr_wp_user->ID);
            
            
            // array of all igcse videos members levels
            $igcse_levels_arr = array(
                'iGCSE Biology Member', 
                'iGCSE Chemistry Member', 
                'iGCSE Physics Member', 
                'iGCSE Biology And Chemistry Member', 
                'iGCSE Biology And Chemistry And Physics Member'
            );
            
            
            // check if current user levels include igcse levels
            $igcse_arr_check = array_intersect($igcse_levels_arr, $curr_wpu_levels);
            
            // if so, show video download menu
            if(!empty($igcse_arr_check)) {
            
                echo '<div class="widget widget_nav_menu">
                        <h4 class="widgettitle">Your iGCSE Courses</h4>';

                wp_nav_menu( array( 'menu' => 'Download Your Lessons' ) );

                echo '</div>';
                
            }
            
            $ks3_levels_arr = array('KS3 Interactive Videos', 'KS3 Interactive Videos (Continuous)');
            $ks3_arr_check = array_intersect($ks3_levels_arr, $curr_wpu_levels);
                        
            if(!empty($ks3_arr_check)) {

                echo '<div class="widget widget_nav_menu">
                            <h4 class="widgettitle">Your KS3 Course</h4>';
                
                wp_nav_menu( array( 'menu' => 'KS3 Videos Sidebar' ) );
                            
                echo '</div>';
            }
        }
    ?>

</aside>