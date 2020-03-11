<?php
/*
 * Template Name: KS3 Science Interactive Videos Subscription Information
 *
 * For more info: https://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>

<div id="content">

    <div id="inner-content" class="wrap cf">

            <main id="main" class="m-all t-2of3 d-5of7 cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="https://schema.org/Blog">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">

								<header class="article-header">
                                
									<h1 class="page-title" itemprop="headline">
									    <?php the_title(); ?>
									</h1>

									<p class="byline vcard">
										
									</p>

								</header> <?php // end article header ?>
								
								<section class="entry-content cf" itemprop="articleBody">
								
								    <div>
								        <h2>Subscription Details</h2>
                                        <p>Here is information about your KS3 Science Interactive Videos subscription. Your subscription consists of a recurring payment every 30 days. Each series of videos and content are released every 30 days of your recurring subscription.</p>
                                        
                                        <div>
                                        <?php
                                        // the_content();
                                        ?>
                                        </div>
                                        
                                        <p><a href="ks3-science-course-view-my-lessons">View your KS3 videos and learning materials.</a></p>
                                    </div>
                                    
                                </section>

								<section class="entry-content cf" itemprop="articleBody">
								
								    <div>
                                        <h2>Unlimited Access (After 10 Payments)</h2>
								        <p>Once you have paid the full course of 10 payments (e.g. taken once every 30 days), you will have unlimited access to the KS3 content. You should no longer be charged subscription payments after the 10 payments. However, in some rare cases, if your PayPal account continues to take payments for the KS3 course, you may need to cancel the payments manually from your PayPal account.</p>
								    </div>
								
								</section>
								
								<section class="entry-content cf" itemprop="articleBody">
								
								    <div>
                                        <h2>Cancelling Your Subscription (Before 10 Payments)</h2>
								        <p>If you wish to cancel your subscription before the full course of 10 payments is completed, you may do so, but please be aware that your KS3 membership will be cancelled and you will no longer have access to the KS3 course content.</p>
								    </div>
								
								</section> <?php // end article section ?>

							</article>

							<?php endwhile; endif; ?>

						</main>


            <!-- No Blog Sidebar -->
            <?php get_sidebar('login'); ?>

    </div>

</div>

<?php get_footer(); ?>
