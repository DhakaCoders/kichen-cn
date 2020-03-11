<?php
/*
 * Template Name: Test Home Page
 *
 * For more info: http://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>

<div id="content">

    <div id="inner-content" class="wrap cf">

            <main id="main" class="cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <section class="cf hero-bnr cf m-all t-all d-all">
                    <div>
                        <span class="quote">As a practising and experienced teacher, I am passionate about helping all children to learn more effectively.</span>
                    </div>
                </section>

                <div class="cf m-all t-3of5 d-8of12">
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'cf intro-video' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
                        <section class="entry-content cf no-padding" itemprop="articleBody">
                           <div class="welcome-heading">
                               <h1>Welcome to iGCSE Science Courses</h1>
                           </div>
                           
                            <div style="width: 100%; padding: 10px 0;" class="wp-video">
                                <!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->
                                <video class="wp-video-shortcode" id="video-320-1" width="100%" height="400" preload="metadata" controls="controls">
                                    <source type="video/mp4" src="<?php echo site_url(); ?>/wp-content/uploads/2015/11/introvideoIGCSE.mp4?_=1" />
                                    <a href="<?php echo site_url(); ?>/wp-content/uploads/2015/11/introvideoIGCSE.mp4"><?php echo site_url(); ?>/wp-content/uploads/2015/11/introvideoIGCSE.mp4</a>
                                </video>
                            </div>

                            <div class="reasons1_v2">
                                <ul class="tick">
                                    <li>Enable your child to achieve success in their GCSE examinations</li>
                                    <li>Research has shown the benefits of learning through videos</li>
                                    <li>Your child will want to watch my videos over and over again</li>
                                    <li>Hundreds of satisfied parents and overwhelmingly positive feedback</li>
                                </ul>
                            </div>
                        </section>

                        <section class="call-to-atn cf m-all t-all d-all">
                            <div>
                                <a class="btn" href="#courses">
                                    <span>View Courses Now!</span>
                                </a>
                            </div>
                        </section>
                    </article>
                </div>

                <div class="cf m-all t-2of5 d-4of12">
                    <article <?php post_class( 'cf free-revision aligncenter' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
                        <section class="entry-content cf no-padding" itemprop="articleBody">
                            <header class="side-title">
                                <h3><span class="title-block-part">Effective Revision &amp; Study Skills</span></h3>
                            </header>
                            <figure>
                                <img style="width: 100%;" src="<?php echo site_url(); ?>/wp-content/themes/bones/library/images/studyreviseimage.jpg" alt="Free Study Skills and Revision Guide">
                                <span class="img-over-text">FREE 7 Part Video Series!</span>
                            </figure>

                            <!-- free revision videos sign up -->
                            <?php get_sidebar('study-skills'); ?>

                        </section>
                    </article>
                    <article <?php post_class( 'cf course-box' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
                        <section class="entry-content cf no-padding" itemprop="articleBody">
                            <header class="side-title">
                                <h3>COMPLETE Course in a Box</h3>
                            </header>
                            <figure class="cf m-1of2 t-1of2 d-6of12 padded-img aligncenter vertcenter" style="padding-top:10px;">
                                <img src="<?php echo site_url(); ?>/wp-content/themes/bones/library/images/packages4.svg" alt="iGCSE Science Complete Course in a Box" style="width:150px">   
                            </figure><div class="cf m-1of2 t-1of2 d-6of12 vertcenter cb-list">
                                <ul>
                                    <li>Learning Plans</li>
                                    <li>Resources</li>
                                    <li>Talking Papers</li>
                                </ul>
                            </div>
                            <div class="cf call-to-atn margin-top no-margin-btm">
                                <a href="<?php echo site_url(); ?>/complete-course-in-a-box/" class="btn full-width">
                                    <span>Find out more</span>
                                </a>
                            </div>
                        </section>
                    </article>
                    <div class="cf online-tuition-homepage">
                        <div class="oth-question"><span>Do You Need One-to-One Tuition?</span></div>
                        <div class="call-to-atn">
                            <a href="<?php echo site_url(); ?>/online-one-to-one-tuition/" class="oth-button btn">
                                <span>Find out more</span>
                            </a>
                        </div>
                    </div>
                </div>

                <span id="courses"></span>						
                <article <?php post_class( 'cf m-all t-all d-all column-grp' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

                    <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
                        <header class="section-title">
                            <h3><i class="fa fa-pagelines"></i>&nbsp; Biology</h3>
                        </header>
                        <div>
                            <div>
                                <ul>
                                    <li>Cover the entire Biology specifications</li>
                                    <li>Complete access to over 20 MP4 video tutorials</li>
                                </ul>
                            </div>
                            <div class="course-example">
                                <figure>
                                    <img src="<?php echo site_url(); ?>/wp-content/themes/bones/library/images/biology.jpg" alt="Biology Course Example">
                                    <div class="blue-filter">20+ VIDEOS</div>
                                </figure>
                            </div>
                            <div class="watch-video-btn">
                                <a href="<?php echo site_url(); ?>/wp-content/uploads/2015/10/biologysamplevideo.mp4" class="btn" data-rel="lightcase" title="iGCSE Biology Sample Video">
                                    <span>View Sample Video</span>
                                </a>
                            </div>
                            <div class="call-to-atn">
                                <a href="<?php echo site_url(); ?>/biology/" class="btn">
                                    <span>Find Out More</span>
                                </a>
                            </div>
                        </div>
                    </section>

                    <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
                        <header class="section-title">
                            <h3><i class="fa fa-flask"></i>&nbsp; Chemistry</h3>
                        </header>
                        <div>
                            <div>
                                <ul>
                                    <li>Cover the entire Chemistry specifications</li>
                                    <li>Complete access to over 20 MP4 video tutorials</li>
                                </ul>
                            </div>
                            <div class="course-example">
                                <figure>
                                    <img src="<?php echo site_url(); ?>/wp-content/themes/bones/library/images/chemistry.jpg" alt="Chemistry Course Example">
                                    <div class="blue-filter">20+ VIDEOS</div>
                                </figure>
                            </div>
                            <div class="watch-video-btn">
                                <a href="<?php echo site_url(); ?>/wp-content/uploads/2015/10/chemistrysamplevideo.mp4" class="btn" data-rel="lightcase" title="iGCSE Chemistry Sample Video">
                                    <span>View Sample Video</span>
                                </a>
                            </div>
                            <div class="call-to-atn">
                                <a href="<?php echo site_url(); ?>/chemistry/" class="btn">
                                    <span>Find Out More</span>
                                </a>
                            </div>
                        </div>
                    </section>

                    <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
                        <header class="section-title">
                            <h3><i class="fa fa-balance-scale"></i>&nbsp; Physics</h3>
                        </header>
                        <div>
                            <div>
                                <ul>
                                    <li>Cover the entire Physics specifications</li>
                                    <li>Complete access to over 30 MP4 video tutorials</li>
                                </ul>
                            </div>
                            <div class="course-example">
                                <figure>
                                    <img src="<?php echo site_url(); ?>/wp-content/themes/bones/library/images/physics.jpg" alt="Physics Course Example">
                                    <div class="blue-filter">30+ VIDEOS</div>
                                </figure>
                            </div>
                            <div class="watch-video-btn">
                                <a href="<?php echo site_url(); ?>/wp-content/uploads/2015/10/physicssamplevideo.mp4" class="btn" data-rel="lightcase" title="iGCSE Physics Sample Video">
                                    <span>View Sample Video</span>
                                </a>
                            </div>
                            <div class="call-to-atn">
                                <a href="<?php echo site_url(); ?>/physics/" class="btn">
                                    <span>Find Out More</span>
                                </a>
                            </div>
                        </div>
                    </section>

                    <!--
                    <footer class="article-footer"></footer>
                    -->
                </article>

                <section class="hero-bnr cf m-all t-all d-all experienced-teacher">
                    <div>
                        <span>Experienced teacher in your home with complete online iGCSE Courses</span>
                    </div>
                </section>

                <article <?php post_class( 'cf m-all t-all d-all reviews' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

                    <header class="article-header aligncenter">
                        <h3>Reviews from our students</h3>
                    </header>


                    <section class="entry-content cf" itemprop="articleBody">

                       <?php do_action('slideshow_deploy', '533'); ?>

                    </section>


                </article>

                <article <?php post_class( 'cf m-all t-all d-all sign-up-now' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
                   <header class="article-header">
                       <h3>Get All Videos Courses Now For Only &pound;59.99</h3>
                   </header>
                    <section class="call-to-atn cf m-all t-all d-all">
                      <p>For a LIMITED time we are offering All 3 Video Courses for only £59.99!</p>
                      <p>Get access to over 70 Biology, Chemistry AND Physics Videos saving you £29.98!</p>
                      <p>Click The Button Below To Get Instant Access To Over 70 Science Videos For Just £59.99!</p>


                        <button onclick="window.location='http://www.igcsesciencecourses.com/index.php/register/Zmqz12?pid=2ABF7EF66F'" class="wlm-paypal-button">Get Instant Access Now</button>
                    </section>                            
                </article>

                <article <?php post_class( 'cf m-all t-all d-all' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
                    <?php
                        // show content so that coupon code form is visible
                        the_content();
                    ?>
                </article>

                <?php endwhile; else : ?>

                <?php endif; ?>

            </main>

            <!-- No Blog Sidebar -->
            <?php // get_sidebar(); ?>

    </div>

</div>


<?php get_footer(); ?>
