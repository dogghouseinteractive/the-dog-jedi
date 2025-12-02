<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<header class="site-header" role="banner">
	<div class="container">
		<?php $custom_logo = get_custom_logo(); ?>
		<div id="logo"<?php if(!is_archive() && !is_single() && has_post_thumbnail()) { ?> class="has-featured-image"<?php } ?>>
			<?php if(!empty($custom_logo)) { 
				 echo $custom_logo;
			} else { ?>
				<a href="<?php echo home_url(); ?>">
					<h1>The Dog Jedi</h1>
				</a>
			<?php } ?>
		</div>
		<div id="hamburger">
			<div class="top-bun fadeIn"></div>
			<div class="patty fadeIn"></div>
			<div class="bottom-bun fadeIn"></div>
		</div>
		<div id="primary-nav-container"<?php if(!is_archive() && !is_single() && has_post_thumbnail()) { ?> class="has-featured-image"<?php } ?>>
			<?php if ( has_nav_menu( 'top' ) ) {
				wp_nav_menu( array( 'theme_location' => 'top', 'menu_class' => 'nav-menu', 'menu_id' => 'primary-menu' ) );
			} ?>
		</div> <!-- #primary-nav-container -->
		<div id="hamburger-toggle-menu">
			<div class="container">
				<?php if ( has_nav_menu( 'top' ) ) {
					wp_nav_menu( array( 'theme_location' => 'top', 'menu_class' => 'menu-toggle-container', 'menu_id' => 'toggle-nav' ) );
				} ?>
			</div><!-- .container -->
		</div>
		<div class="clear"></div>
	</div>
</header>