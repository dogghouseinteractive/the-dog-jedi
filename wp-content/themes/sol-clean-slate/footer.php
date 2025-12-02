<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */

?>

<footer>
	<div class="container">
		<div class="footer-left">
			<div class="site-info">Copyright <?php echo date('Y'); ?>, <a href="<?php echo home_url(); ?>"><?php echo get_bloginfo('name'); ?></a>. All rights reserved. A <a href="https://dogghouseinteractive.com" target="_blank">Dogghouse Interactive</a> site. A <a href="https://wishbox.love" target="_blank">Wishbox.Love</a> company.</div>
		</div>
		
		<div class="footer-right">
			<a href="/privacy-policy">Privacy Policy</a>
			<a href="/terms">Terms of Services</a>
			<?php if ( has_nav_menu( 'social' ) ) : ?>
				<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Social Links Menu', 'twentyseventeen' ); ?>">
					<?php
						wp_nav_menu( array(
							'theme_location' => 'social',
							'menu_class'     => 'social-links-menu',
							'depth'          => 1,
							'link_before'    => '<span class="screen-reader-text">',
							'link_after'     => '</span>',
						) );
					?>
				</nav><!-- .social-navigation -->
			<?php endif; ?>
		</div>
		<div class="clear"></div>
	</div><!-- .container -->
</footer>
	
<?php wp_footer(); ?>

</body>
</html>
