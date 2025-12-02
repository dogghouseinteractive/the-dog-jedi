<?php
/**
 * The template for displaying archive pages
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
		<section class="page-content">
			<div class="container">
				<?php if (have_posts()) { ?>
					<div class="archive-header">
						<h1><?php echo single_cat_title(); ?></h1>
					</div>
					<?php while(have_posts()) {
						the_post(); ?>
						<a href="<?php echo get_permalink(); ?>"><h2><?php echo get_the_title(); ?></h2></a>
						<?php the_excerpt(); ?>
					<?php }

					the_posts_pagination( array(
						'prev_text' => '<span class="screen-reader-text">' . __( 'Previous page', 'twentyseventeen' ) . '</span>',
						'next_text' => '<span class="screen-reader-text">' . __( 'Next page', 'twentyseventeen' ) . '</span>',
						'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentyseventeen' ) . ' </span>',
					) );

				} else {
					echo 'No posts found.';
				} ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php get_footer();
