<?php
/*
 * Template Name: Test Home Page
 *
 * For more info: https://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>

<div id="content">

    <div id="inner-content" class="wrap cf">

            <main id="main" class="cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="https://schema.org/Blog">

                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <section class="mainQoute cf hero-bnr cf m-all t-all d-all">
                    <div class="quoteInner">
                        <span class="quote">As a practising and experienced teacher, I am passionate about helping all children to learn more effectively.</span>
                    </div>
                </section>
        <section class="homeCnSection1 cf">
            <div class="cf m-all t-3of5 d-8of12">
                <div class="widgetBox hasVideo">
                    <div class="widgetBoxTitle"><h3>What's Included:</h3></div>
                    <div class="widgetBoxContent">
                        <div style="width: 100%; padding: 10px 0;" class="wp-video">
                            <!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->
                            <video class="wp-video-shortcode" id="video-320-1" width="100%" height="470" preload="metadata" controls="controls" poster="<?php echo site_url(); ?>/wp-content/themes/bones/library/images/igcse-science-courses-intro-video.jpg">>
                                <source type="video/mp4" src="<?php echo site_url(); ?>/wp-content/uploads/2017/09/iGCSE-Science-Courses-Intro-Video.mp4" />
                                <a href="<?php echo site_url(); ?>/wp-content/uploads/2017/09/iGCSE-Science-Courses-Intro-Video.mp4"><?php echo site_url(); ?>/wp-content/uploads/2017/09/iGCSE-Science-Courses-Intro-Video.mp4</a>
                            </video>
                        </div>                            
                    </div>
                    <div class="videoLinks cf">
                        <a class="vlink1" href="#">Watch Sample Video 1</a>
                        <a class="vlink2" href="#">Watch Sample Video 2</a>
                    </div>
                </div>
            </div>
            <div class="cf m-all t-2of5 d-4of12">
                <div class="widgetBox">
                    <div class="widgetBoxTitle"><h3>What's Included:</h3></div>
                    <div class="widgetBoxContent">
                        <ul>
                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vehicula est leo, id gravida lacus pellentesque sed.</li>
                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vehicula est leo, id gravida lacus pellentesque sed.</li>
                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vehicula est leo, id gravida lacus pellentesque sed.</li>
                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vehicula est leo, id gravida lacus pellentesque sed.</li>
                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vehicula est leo, id gravida lacus pellentesque sed.</li>
                        </ul>
                        <div class="cta">
                            <a href="#"><i class="fa fa-angle-right"></i> Get Instant Access Now!</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

<section class="homeSec2">
    <div class="wrap">
        <div class="homeCourseDo">
            <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
            <ul>
                <li><a href="#">Lorem ipsum dolor sit amet</a></li>
                <li><a href="#">Lorem ipsum dolor sit amet</a></li>
                <li><a href="#">Lorem ipsum dolor sit amet</a></li>
            </ul>
        </div>
    </div>
</section>

<section class="homeTagline">
    <div class="wrap">
        <div class="homeTaglineCn">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vehicula est leo, id gravida lacus pellentesque sed.
        </div>
    </div>
</section>

<section class="homeReviews">
    <div class="wrap">
        <article <?php post_class( 'cf m-all t-all d-all reviews' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">
            <header class="article-header aligncenter">
                <h3>Reviews from our students</h3>
            </header>
            <section class="entry-content cf" itemprop="articleBody">
               <?php do_action('slideshow_deploy', '533'); ?>
            </section>
        </article>
    </div>
</section>

<section class="homeInstantAccess">
    <div class="wrap">
        <article <?php post_class( 'cf m-all t-all d-all sign-up-now' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">
           <header class="article-header">
               <h3>Get All Videos Courses Now For Only &pound;59.99</h3>
           </header>
            <section class="call-to-atn cf m-all t-all d-all">
              <p>For a LIMITED time we are offering All 3 Video Courses for only £59.99!</p>
              <p>Get access to over 70 Biology, Chemistry AND Physics Videos saving you £29.98!</p>
              <p>Click The Button Below To Get Instant Access To Over 70 Science Videos For Just £59.99!</p>
                <button onclick="window.location='https://www.igcsesciencecourses.com/index.php/register/Zmqz12?pid=2ABF7EF66F'" class="wlm-paypal-button">Get Instant Access Now</button>
            </section>                            
        </article>
    </div>
</section>

<section class="HomeCoupon">
    <div class="wrap">
        <article <?php post_class( 'cf m-all t-all d-all' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">
            <?php
                // show content so that coupon code form is visible
                the_content();
            ?>
        </article>
    </div>
</section>

    <?php endwhile; else : ?>

    <?php endif; ?>

</main>

            <!-- No Blog Sidebar -->
            <?php // get_sidebar(); ?>

    </div>

</div>


<?php get_footer(); ?>
