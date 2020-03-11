<?php
/*
 * Template Name: Youtube Video Gallery
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
									    <?php// the_title(); ?>
									    Youtube Video Gallery
									</h1>

									<p class="byline vcard">
										
									</p>

								</header> <?php // end article header ?>

								<section class="entry-content cf" itemprop="articleBody">
								
								    <nav class="yt-video-gallery-nav">
								        
                                        <?php wp_nav_menu( array( 'menu' => 'Youtube Video Gallery Menu' ) ); ?>
                                        
								    </nav>
								
									<?php
										// the content (pretty self explanatory huh)
										the_content();

										wp_link_pages( array(
											'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'bonestheme' ) . '</span>',
											'after'       => '</div>',
											'link_before' => '<span>',
											'link_after'  => '</span>',
										) );
									?>
								</section> <?php // end article section ?>

								<footer class="article-footer cf">

								</footer>

								<?php comments_template(); ?>

							</article>

							<?php endwhile; endif; ?>

						</main>

						<!-- No Blog Sidebar -->
						<?php get_sidebar('standard'); ?>

				</div>

			</div>

<?php get_footer(); ?>
