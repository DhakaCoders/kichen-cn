 <!-- Standard Page Side Content -->
<aside id="sidebar-standard" class="sidebar m-all t-1of3 d-2of7 last-col cf" role="complementary">

    <?php
        $ks3_user = false;

        if(is_user_logged_in()) {
            if (is_active_sidebar( 'sidebar-standard' ) ) {
                dynamic_sidebar( 'sidebar-standard' );
            }

            /*
            // Get all levels
            $all_wlm_levels = wlmapi_get_levels();

            // print levels data
            foreach( $all_wlm_levels['levels'] as $wlm_level ) {
                echo '<pre>';
                var_dump($wlm_level[0]);
                echo '</pre>';
            }
            */

            // get the current user level from WP more important is global $user.
            $curr_wp_user = wp_get_current_user();

            // Check if user is a member before showing sidebar
            if(
                wlmapi_is_user_a_member( 1367590333, $curr_wp_user->ID ) // biology
                || wlmapi_is_user_a_member( 1367590361, $curr_wp_user->ID ) // chemistry
                || wlmapi_is_user_a_member( 1421408863, $curr_wp_user->ID ) // physics
                || wlmapi_is_user_a_member( 1366925074, $curr_wp_user->ID ) // biology and chemistry
                || wlmapi_is_user_a_member( 1421408922, $curr_wp_user->ID ) // biology, chemistry & physics
            ) {

                echo '<div class="widget widget_nav_menu">
                        <h4 class="widgettitle">Your iGCSE Courses</h4>';

                wp_nav_menu( array( 'menu' => 'Download Your Lessons' ) );

                echo '</div>';

            }

            // Check if user is a member before showing sidebar
            if(
                wlmapi_is_user_a_member( 1467203965, $curr_wp_user->ID ) // ks3
                || wlmapi_is_user_a_member( 1500041790, $curr_wp_user->ID ) // ks3 (continuous)
            ) {

                $ks3_user = true;

                echo '<div class="widget widget_nav_menu">
                            <h4 class="widgettitle">Your KS3 Course</h4>';

                wp_nav_menu( array( 'menu' => 'KS3 Videos Sidebar' ) );

                echo '</div>';
            }
        }

    ?>

    <div class="ks3-advertise-sidebar-link hentry">
        <div class="entry-content cf no-padding course-box">
            <header class="side-title">
                <h3>KS2 Science Video Course</h3>
            </header>
            <div class="ks3-sub-header">
                <span>Biology</span>
                <span>Chemistry</span>
                <span>Physics</span>
            </div>
            <figure class="cf m-1of2 t-1of2 d-6of12 padded-img aligncenter vertcenter ks3-advertise-img">
                <img src="<?= site_url(); ?>/wp-content/themes/bones/library/images/advertise-image-ks2.png" alt="KS2 Science Complete Video Course">
            </figure><div class="cf m-1of2 t-1of2 d-6of12 vertcenter cb-list">
                <ul>
                    <li>National KS2 curriculum</li>
                    <li>Access all 69 videos</li>
                </ul>
            </div>
            <div class="cf call-to-atn margin-top no-margin-btm">
                <a href="https://www.ks2sciencecourses.com" target="_blank" class="btn full-width">
                    <span>Find out more</span>
                </a>
            </div>
        </div>
    </div>

    <?php

        if(!$ks3_user) {
            echo '<div class="ks3-advertise-sidebar-link hentry">
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
                            <img src="'.site_url().'/wp-content/themes/bones/library/images/advertise-image-ks3.png" alt="KS3 Science Complete Video Course">
                        </figure><div class="cf m-1of2 t-1of2 d-6of12 vertcenter cb-list">
                            <ul>
                                <li>Interactive Questions</li>
                                <li>Access all 120 videos</li>
                            </ul>
                        </div>
                        <div class="cf call-to-atn margin-top no-margin-btm">
                            <a href="https://www.ks3sciencecourses.com" target="_blank" class="btn full-width">
                                <span>Find out more</span>
                            </a>
                        </div>
                    </div>
                </div>';
        }
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
