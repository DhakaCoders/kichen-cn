<?php
/*
 * Template Name: KS3 Science Interactive Videos Index
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
                                        <p><a href="ks3-science-course-subscription-information">Your KS3 Science Interactive Videos subscription.</a> Please use the links below to access your interactive videos and learning materials. Each series of videos and content are released every 30 days of your recurring subscription.</p>
								    </div>
								
								    <div class="ks3-videos-list-nav">
								        
                                        <?php wp_nav_menu( array( 'menu' => 'KS3 Videos List' ) ); ?>
                                        
								    </div>
								    
								    <div class="ks3-videos-list-upcoming">
								        <?php the_content(); ?>
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
