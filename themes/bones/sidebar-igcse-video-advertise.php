 <!-- Standard Page Side Content -->
<aside id="sidebar-standard" class="sidebar m-all t-1of3 d-2of7 last-col cf" role="complementary">
 
    <?php 
        if(is_user_logged_in()) {
            if (is_active_sidebar( 'sidebar-standard' ) ) {
                dynamic_sidebar( 'sidebar-standard' );
            }
            
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
    <?php
    /*
    <!--
    <div class="ks3-advertise-sidebar-link hentry">
        <div class="entry-content cf no-padding course-box">
            <header class="side-title">
                <h3>KS3 Science Video Course</h3>
            </header>
            <div class="ks3-sub-header">
                <span>Biology</span>
                <span>Chemistry</span>
                <span>Physics</span>
            </div>
            <figure class="cf m-1of2 t-1of2 d-6of12 padded-img aligncenter vertcenter ks3-advertise-img">
                <img src="<?php echo site_url(); ?>/wp-content/themes/bones/library/images/ks3-video-advertise.png" alt="iGCSE Science Complete Course in a Box">   
            </figure><div class="cf m-1of2 t-1of2 d-6of12 vertcenter cb-list">
                <ul>
                    <li>Interactive Questions</li>    
                    <li>New Videos Every 30 Days</li>
                </ul>
            </div>
            <div class="cf call-to-atn margin-top no-margin-btm">
                <a href="<?php echo site_url(); ?>/ks3-science-interactive-videos/" class="btn full-width">
                    <span>Find out more</span>
                </a>
            </div>
        </div>
    </div>
    -->
    */
    ?>
    <?php
        // science news
        if (is_active_sidebar( 'sidebar-news' ) ) {
            dynamic_sidebar( 'sidebar-news' );
        }
    ?>
    
   <!--
    <div class="hentry science-news">
       <div class="sidebar-header">
            <h3>Latest Science News</h3>
        </div>
        <ul class="post-sidebar-list">
           <?php
            $args = array( 'numberposts' => 2, 'post_status'=>"publish",'post_type'=>"post",'orderby'=>"post_date",'category_name'=>"Science News");
            $postslist = get_posts($args);
            foreach ($postslist as $post) :  setup_postdata($post); ?> 
                <li>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title();?>"> <?php the_title(); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    -->
    

    <div class="hentry latest-blog-posts">
        <div class="sidebar-header">
            <h3>Latest Blog Posts</h3>
        </div>

        <ul class="post-sidebar-list">
           <?php
            $args = array( 'numberposts' => 2, 'post_status'=>"publish",'post_type'=>"post",'orderby'=>"post_date",'category__not_in'=>array(5));
            $postslist = get_posts($args);
            foreach ($postslist as $post) :  setup_postdata($post); ?> 
                <li>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title();?>"> <?php the_title(); ?></a>
                    <?php the_excerpt(); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

</aside>