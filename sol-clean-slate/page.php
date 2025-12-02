<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php if(has_post_thumbnail()) { ?>
			<div class="featured-image" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);"></div>
		<?php } ?>
		<section class="page-content">
			<div class="container">
				<?php if(have_posts()) { ?>
					<?php if(!is_page('197') && !is_page('306')) { ?>
						<div class="page-header">
							<h1><?php echo get_the_title(); ?></h1>
						</div>
					<?php } ?>
					<?php while(have_posts()) {
						the_post(); ?>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
					<?php }
				} ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
