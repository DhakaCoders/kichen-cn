<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // force Internet Explorer to use the latest rendering engine available ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<title><?php wp_title(''); ?></title>

		<?php // mobile meta (hooray!) ?>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1"/>

		<?php // icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
		
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon.png">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<?php // or, set /favicon.ico for IE10 win ?>
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">
            <meta name="theme-color" content="#121212">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>

		<?php // drop Google Analytics Here ?>
		<?php // end analytics ?>
		
		<!-- Lightcase image and video lightbox -->
		<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/library/lightcase/lightcase.css">
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/lightcase/lightcase.js"></script>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('a[data-rel^=lightcase]').lightcase();
            });
        </script>
        <!-- End of Lightcase script -->
        
	</head>

	<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

		<div id="container">

			<header class="header" role="banner" itemscope itemtype="http://schema.org/WPHeader">
        <div class="leftPart"></div>    
        <div class="leftPart"></div>    
				<div id="inner-header" class="cf">
                   
                   <div class="header-social-media hide">
                       <a class="header-sm-link header-sm-facebook" href="https://www.facebook.com/ScienceVideoCourses">
                           
                           <span class="fa-stack fa-2x">
                               <i class="fa fa-square fa-stack-2x"></i>
                               <i class="fa fa-facebook fa-stack-1x"></i>
                           </span>
                           <span class="header-sm-text">Facebook</span>
                       </a>
                       <?php /*
                       <a class="header-sm-link header-sm-twitter" href="https://twitter.com/ScienceCourses">
                           
                           <span class="fa-stack fa-2x">
                               <i class="fa fa-square fa-stack-2x"></i>
                               <i class="fa fa-twitter fa-stack-1x"></i>
                           </span>
                           
                           <span class="header-sm-text">Twitter</span>
                       </a> 
                       */ ?>
                   </div>
                  <div class="wrap cf">
                    <div class="header-top clearfix">
                      <div class="left-logo">
                          <div id="logo" class="h1" itemscope itemtype="http://schema.org/Organization">
                              <a href="<?php echo home_url(); ?>" rel="nofollow">
                                <img src="<?php echo THEME_URI; ?>/images/kitchen-sink-science_logo.png">
                              </a>
                          </div>                        
                      </div>
                      <div class="right-tagline">
                        <div class="right-tag-content">
                          <div class="rtc-text">Science videos<br>on demand</div>
                          <div class="rtc-image"></div>
                        </div>
                      </div>
                    </div>
                  </div>


					<?php // if you'd like to use the site description you can un-comment it below ?>
					<?php // bloginfo('description'); ?>

					<nav class="main-top-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
					    <div class="mobile-menu-btn" data-toggle="closed"><i class="fa fa-bars"></i> Menu</div>
						<?php wp_nav_menu(array(
    					         'container' => false,                           // remove nav container
    					         'container_class' => 'menu cf',                 // class of container (should you choose to use it)
    					         'menu' => __( 'The Main Menu', 'bonestheme' ),  // nav name
    					         'menu_class' => 'nav top-nav cf',               // adding custom nav class
    					         'theme_location' => 'main-nav',                 // where it's located in the theme
    					         'before' => '',                                 // before the menu
        			               'after' => '',                                  // after the menu
        			               'link_before' => '',                            // before each link
        			               'link_after' => '',                             // after each link
        			               'depth' => 0,                                   // limit the depth of the nav
    					         'fallback_cb' => ''                             // fallback function (if there is one)
						)); ?>

					</nav>

				</div>

			</header>
