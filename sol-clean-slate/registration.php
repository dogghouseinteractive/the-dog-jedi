<?php
/**
 * Template Name: Registration
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<section class="page-content">
			<div class="container">
				<?php if(have_posts()) { ?>
					<div class="page-header">
						<h1><?php echo get_the_title(); ?></h1>
					</div>
					<?php while(have_posts()) {
						the_post();
						the_content();
					}
				} ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
