<?php
/**
 * Template Name: Testing
 */

$bookings = new Bookings;

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php if(has_post_thumbnail()) { ?>
			<div class="featured-image" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);"></div>
		<?php } ?>
		<section class="page-content">
			<div class="container">
				<?php if(have_posts()) { ?>
					<?php while(have_posts()) {
						the_post(); ?>
 
           <div class="entry-content">
              <?php $bookings->displayLocalBookings(); ?>
						</div>
					<?php }
				} ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();

