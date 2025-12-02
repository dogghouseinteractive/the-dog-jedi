<?php
/**
 * Template Name: Leave a Tip
 */

$reviewed = (!empty($_GET['testimonial_created']) && $_GET['testimonial_created'] == 'true' ? true : false);

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php if(has_post_thumbnail()) { ?>
			<div class="featured-image" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);"></div>
		<?php } ?>
		<section class="page-content">
			<div class="container">
				<?php if(have_posts()) { ?>
					<div class="page-header">
						<h1><?php echo get_the_title(); ?></h1>
					</div>
					<?php while(have_posts()) {
						the_post(); ?>
						<div class="entry-content">
							<?php if($reviewed) { ?>
								<p>Thank you for your review! <a href="/testimonials">Click Here</a> to see that now.</p>
								<?php the_content(); ?>
							<?php } else { ?>
								<?php the_content(); ?>
								<p>If you have not yet done so, please consider <a href="/leave-a-review">leaving us a review</a>.</p>
							<?php } ?>
						</div>
					<?php }
				} ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
