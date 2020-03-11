<?php
/*
 * Template Name: Online Tuition Sign Up Page
 *
 * For more info: https://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>

<div id="content">

    <div id="inner-content" class="wrap cf">

            <main id="main" class="m-all t-2of3 d-5of7 cf online-tuition-page" role="main" itemscope itemprop="mainContentOfPage" itemtype="https://schema.org/Blog">

                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <?php endwhile; endif; ?>
                    
                <div class="cf m-all t-all d-all">
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'cf no-border' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">
                        
                        <header class="article-header-standalone">
                            <h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
                        </header> <?php // end article header ?>

                        <section class="cf ot-intro-1">
                           <div class="ot-icons">
                               <i class="fa fa-user ot-user-1"></i>
                               <i class="fa fa-arrows-h"></i>
                               <i class="fa fa-user ot-user-2"></i>
                           </div>
                           
                            <h2>Now Available!</h2>
                           
                            <p>On a very limited basis I am now able to offer one-to-one tuition online.</p>
                            <p>I use Skype to link with my students.</p>
                            
                        </section>

                        <section class="ot-description cf">
                           <div class="center-block-inner">
                               <h2 class="aligncenter">Personal tuition adapted to suit your needs</h2>
                               <div class="ot-number-points">
                                    <ol>
                                        <li>
                                            <div class="li-content">
                                                <span class="li-heading">Struggling with a particular concept?</span>
                                                <span class="li-sub">I will help you overcome any sticking points.</span>
                                            </div>
                                        </li>
                                        <li>
                                           <div class="li-content">
                                                <span class="li-heading">Need more than the video content?</span>
                                                <span class="li-sub">Be stretched further and taken through challenging questions.</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="li-content">Recap the content of a particular video</div></li>
                                        <li>
                                            <div class="li-content">Go through a particular exam paper</div></li>
                                        <li>
                                            <div class="li-content">Tackle individual questions with a white board and screen sharing</div></li>
                                    </ol>
                                </div>
                            </div>
                        </section>

                        <section class="cf ot-intro-2 ot-pricing-1">
                            <h2>The online tuition charge is normally £25 per hour</h2>
                        </section>

                    </article>
                </div>
                       
                <div class="cf m-all t-all d-all">

                   <section class="cf ot-enquiry">
                       <h2 class="section-title ot-enquiry-title">Find Out More</h2>
                           
                        <p>If you are interested, require further details or have specific questions then please contact me using the form below.</p>
                        <div class="cf ot-contact-form">
                            <?php echo do_shortcode( '[contact-form-7 id="753" title="Online Tuition Sign-up Contact Form"]' ); ?>
                        </div>
                    </section>

                    <!--
                        Create form in wordpress admin panel
                        -- Contact Form 7

                    <!--
                    <section class="online-tuition-sign-up aligncenter">
                        <p>Subscribe below </p>
                        <div class="form-outer">
                            <form method="post" class="form-inner" accept-charset="UTF-8" action="https://www.aweber.com/scripts/addlead.pl" target="_blank">
                                <div style="display: none;">
                                    <input type="hidden" name="meta_web_form_id" value="1232757684">
                                    <input type="hidden" name="meta_split_id" value="">
                                    <input type="hidden" name="listname" value="awlist4329917">
                                    <input type="hidden" name="redirect" value="https://www.aweber.com/thankyou-coi.htm?m=text" id="redirect_341aed775a38aacae412646ac9f42aa0">
                                    <input type="hidden" name="meta_adtracking" value="Online_Tuition_Notification_Subscription_Form">
                                    <input type="hidden" name="meta_message" value="1">
                                    <input type="hidden" name="meta_required" value="name,email">
                                    <input type="hidden" name="meta_tooltip" value="">
                                </div>
                                <div class="form-all-controls-outer">
                                    <div class="form-group-outer">
                                        <label class="form-group-part" for="otsu-name">Name</label>
                                        <div class="form-group-part form-control-outer">
                                            <input id="otsu-name" type="text" name="name" class="form-control" value="">
                                        </div>
                                        <div class="af-clear"></div>
                                    </div>
                                    <div class="form-group-outer">
                                        <label class="form-group-part" for="otsu-email">Email</label>
                                        <div class="form-group-part form-control-outer">
                                            <input class="form-control" id="otsu-email" type="text" name="email" value="">
                                        </div>
                                        <div class="af-clear"></div>
                                    </div>
                                    <div class="form-submit-btn-outer">
                                        <button type="submit" class="btn submit-btn otsu-btn">SIGN UP</button>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>
                    -->
                </div>

            </main>

            <!-- No Blog Sidebar -->
            <?php get_sidebar('standard'); ?>

    </div>

</div>

<?php get_footer(); ?>
