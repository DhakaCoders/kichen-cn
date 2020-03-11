<?php
/*
 * Template Name: Course In A Box Page
 *
 * For more info: https://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap cf">

						<main id="main" class="m-all t-2of3 d-5of7 cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="https://schema.org/Blog">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                            <header class="article-header-standalone">
                                <h1 class="page-title"><?php the_title(); ?></h1>
                            </header>

                            <?php endwhile; endif; ?>

                            <div class="cf m-all t-all d-all">
                                <article <?php post_class( 'cf intro-video' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">
                                    <section class="entry-content cf no-padding">
                                        <div style="width: 100%; padding: 10px 0;" class="wp-video">
                                            <!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->
                                            <video class="wp-video-shortcode" id="video-320-1" width="100%" height="400" preload="metadata" controls="controls" autoplay>
                                                <source type="video/mp4" src="<?php echo site_url(); ?>/wp-content/uploads/2015/12/Intro-to-Science-in-a-Box.mp4?_=1" />
                                                <a href="<?php echo site_url(); ?>/wp-content/uploads/2015/12/Intro-to-Science-in-a-Box.mp4"><?php echo site_url(); ?>/wp-content/uploads/2015/12/Intro-to-Science-in-a-Box.mp4</a>
                                            </video>
                                        </div>

                                        <div class="video-text">
                                            <h2>Learning Plans</h2>
                                            <p>This short video introduces you to the Learning Plans. I will give you a little background to the plans and then take you through a specific plan and how to use it for maximum benefit.</p>
                                            <h3>Further Content In Development...</h3>
                                            <p>The Learning Plans are an ongoing project and will be updated and improved as time allows. For example, the practical videos and links are still under development, so please be patient and keep checking for updates. Where possible I have included links to other websites where you might find suitable materials.</p>
                                       </div>
                                    </section>
                                </article>
                            </div>

				            <span id="courses"></span>

                            <article <?php post_class( 'cf m-all t-all d-all column-grp' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">
                                <header class="group-title">
									<h2>Edexcel iGCSE Courses in a Box - Updated</h2>
								</header>

								<section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>Edexcel Biology</h3>
								    </header>
								    <div>
								        <div class="call-to-atn">
                                            <a href="<?php echo site_url(); ?>/complete-course-in-a-box/edexcel-biology-2018/" class="btn">
                                                <span>Get Access Now</span>
                                            </a>
								        </div>
								    </div>
								</section>

                               <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>Edexcel Chemistry</h3>
								    </header>
								    <div>
								        <div class="call-to-atn">
                                            <a href="<?php echo site_url(); ?>/complete-course-in-a-box/edexcel-chemistry-2018/" class="btn">
                                                <span>Get Access Now</span>
                                            </a>
								        </div>
								    </div>
								</section>

                               <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>Edexcel Physics (2019)</h3>
								    </header>
								    <div>
										<div class="call-to-atn">
                                            <a href="<?php echo site_url(); ?>/complete-course-in-a-box/edexcel-physics-2019/" class="btn">
                                                <span>Get Access Now</span>
                                            </a>
								        </div>
								    </div>
								</section>
							</article>

                            <!--
				            <article <?php post_class( 'cf m-all t-all d-all column-grp' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">
								<header class="group-title">
									<h2>CiE iGCSE Courses in a Box - Updated 2018</h2>
								</header>

								<section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>CiE Biology</h3>
								    </header>
								    <div>
								        <div class="btn-unavailable">
                                            <span>Not Yet Available</span>
								        </div>
								    </div>
								</section>

                               <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>CiE Chemistry</h3>
								    </header>
								    <div>
								        <div class="btn-unavailable">
                                            <span>Not Yet Available</span>
								        </div>
								    </div>
								</section>

                               <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>CiE Physics</h3>
								    </header>
								    <div>
								        <div class="btn-unavailable">
                                            <span>Coming Soon</span>
								        </div>
								    </div>
								</section>
							</article>
                            -->

							<article <?php post_class( 'cf m-all t-all d-all column-grp' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">
                                <header class="group-title">
									<h2>Edexcel iGCSE Courses in a Box - Legacy</h2>
								</header>

								<section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>Edexcel Biology</h3>
								    </header>
								    <div>
								        <div class="call-to-atn">
                                            <a href="<?php echo site_url(); ?>/complete-course-in-a-box/edexcel-biology-legacy/" class="btn">
                                                <span>Get Access Now</span>
                                            </a>
								        </div>
								    </div>
								</section>

                               <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>Edexcel Chemistry</h3>
								    </header>
								    <div>
								        <div class="call-to-atn">
                                            <a href="<?php echo site_url(); ?>/complete-course-in-a-box/edexcel-chemistry-legacy/" class="btn">
                                                <span>Get Access Now</span>
                                            </a>
								        </div>
								    </div>
								</section>

                               <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>Edexcel Physics</h3>
								    </header>
								    <div>
								        <div class="call-to-atn">
                                            <a href="<?php echo site_url(); ?>/complete-course-in-a-box/edexcel-physics-legacy/" class="btn">
                                                <span>Get Access Now</span>
                                            </a>
								        </div>
								    </div>
								</section>
							</article>

				            <article <?php post_class( 'cf m-all t-all d-all column-grp' ); ?> role="article" itemscope itemtype="https://schema.org/BlogPosting">
								<header class="group-title">
									<h2>CiE iGCSE Courses in a Box - Legacy</h2>
								</header>

								<section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>CiE Biology</h3>
								    </header>
								    <div>
								        <div class="btn-unavailable">
                                            <span>Not Yet Available</span>
								        </div>
								    </div>
								</section>

                               <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>CiE Chemistry</h3>
								    </header>
								    <div>
								        <div class="btn-unavailable">
                                            <span>Not Yet Available</span>
								        </div>
								    </div>
								</section>

                               <section class="entry-content cf m-all t-1of3 d-1of3 no-padding" itemprop="articleBody">
								    <header class="section-title">
								        <h3>CiE Physics</h3>
								    </header>
								    <div>
								        <div class="call-to-atn">
                                            <a href="<?php echo site_url(); ?>/complete-course-in-a-box/cie-physics-legacy/" class="btn">
                                                <span>Get Access Now</span>
                                            </a>
								        </div>
								    </div>
								</section>
							</article>

						</main>

						<!-- No Blog Sidebar -->
						<?php get_sidebar('standard'); ?>
				</div>

			</div>


<?php get_footer(); ?>
