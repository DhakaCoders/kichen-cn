<footer class="footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
	<div class="footerTop">
		<div class="wrap cf">
			<div class="col col1">
				<div class="col-inner"></div>
			</div>
			<div class="col col2"><div class="col-inner">
				<nav role="navigation">
					<?php wp_nav_menu(array(
					'container' => 'div',                           // enter '' to remove nav container (just make sure .footer-links in _base.scss isn't wrapping)
					'container_class' => 'footer-links cf',         // class of container (should you choose to use it)
					'menu' => __( 'Footer Links', 'bonestheme' ),   // nav name
					'menu_class' => 'nav footer-nav cf',            // adding custom nav class
					'theme_location' => 'footer-links',             // where it's located in the theme
					'before' => '',                                 // before the menu
					'after' => '',                                  // after the menu
					'link_before' => '',                            // before each link
					'link_after' => '',                             // after each link
					'depth' => 0,                                   // limit the depth of the nav
					'fallback_cb' => 'bones_footer_links_fallback'  // fallback function
					)); ?>
				</nav>
			</div></div>
			<div class="col col3"><div class="col-inner"></div></div>
		</div>
	</div>

	<div class="footerBottom">
		<div class="wrap cf">
			<div class="footerBottomInner">
				<p class="source-org copyright">Copyright &copy; <?php echo date('Y'); ?> All Rights Reserved - <?php bloginfo( 'name' ); ?></p>
				<p class="footer-pwd">Website by Pineapple <a style="color:#fff" href="https://www.pineapplewebdesign.co.uk">Web Design Sussex</a></p>
			</div>
		</div>
	</div>
</footer>

</div>

<?php // all js scripts are loaded in library/bones.php ?>
<?php wp_footer(); ?>

</body>

</html> <!-- end of site. what a ride! -->
