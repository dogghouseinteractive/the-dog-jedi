<?php
/**
 * The front page template file
 *
 * If the user has selected a static page for their homepage, this is what will
 * appear.
 * Learn more: https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); 

if(!get_current_user_id()) {
	wp_redirect('/login');
	exit;
} else {
	wp_redirect('/client-profile');
	exit;
}

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<section class="page-content">
			<div class="container">
				<?php if(have_posts()) { ?>
					<div class="page-header">
					</div>
					<?php while(have_posts()) {
						the_post(); ?>
						<div class="entry-content">
							<?php if(!get_current_user_id()) { ?>
								<?php //echo do_shortcode('[gravityform id="2" title="true"]'); ?>
								<?php wp_login_form(); ?>
							<?php } else if(current_user_can('administrator') || (current_user_can('customer'))) { ?>
								<?php wp_redirect('/client-profile');
								exit; ?>
							<?php } else { ?>
								<div class="closing-notice">
									<p><strong>The Dog Jedi has officially closed its doors, effective 09/22/2024.</strong> We will miss seeing your dogs, but after nearly 4 years providing safe, intimate boarding for more than 150 different dogs, the time has come for Troy to become more available for the things in life that are important to him. Thank you so much for your business these past several years.</p>
										
									<p>Please reach out to Troy directly, via SMS, if you need recommendations on other dog sitters in the area.</p>
								</div>
							<?php } ?>
						</div>
<!--
					<section id="booking-block">
						<div class="video-container">
							<iframe src="https://www.youtube-nocookie.com/embed/it7KxnCbnkY?controls=0&modestbranding=1&autoplay=1&list=PLLbbF41Nr6tgDFnAphBWDiAZTkfEQ0b_4&loop=1&playsinline=1&mute=1" title="The Dog Jedi - The Dogghouse" frameborder="0" allow="autoplay;" allowfullscreen></iframe>
							<div class="clear"></div>
						</div>
					</section>
-->
				<?php } ?>
			<?php } ?>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
