<?php
/**
 * Template Name: Testimonials
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php if(has_post_thumbnail()) { ?>
			<div class="featured-image" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);"></div>
		<?php } ?>
		<section class="page-content">
			<div class="container">
				<div class="page-header">
					<h1><?php echo get_the_title(); ?></h1>
				</div>
				<?php $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; ?>
				<?php $args = array(
					'post_type' => 'testimonial',
					'posts_per_page' => 20,
					'orderby' => 'date',
					'order' => 'DESC',
					'paged' => $paged,
				); ?>
				<?php $testimonials = new WP_Query($args); ?>
				<?php if($testimonials->have_posts()) { ?>
					<div id="testimonials">
						<?php while($testimonials->have_posts()) {
							$testimonials->the_post(); ?>
							<div class="testimonial">
								<?php $testimonial_image = get_the_post_thumbnail_url(); ?>
								<?php $testimonial_name = get_the_title(); ?>
								<?php $testimonial_date = get_the_date(); ?>
								<div class="testimonial-left">
									<?php 
										$testimonial_author_id = get_the_author_meta( 'ID' );
										$user_avatar_url = wpupa_get_url(get_the_author_meta( 'ID' ), ['size' => 'full']);
										if(str_contains($user_avatar_url, 'gravatar')) {
											$testimonial_author_id = 1;
										}
									?>
									<div class="testimonial-image" style="background-image: url(<?php if($testimonial_author_id != '1') { echo $user_avatar_url; } else if($testimonial_image) { echo $testimonial_image; } else { echo 'https://thedogjedi.com/wp-content/uploads/2023/04/Collie_dog.webp'; } ?>);"></div>
									<?php if(get_the_author_meta( 'ID' ) == wp_get_current_user()->ID ) { ?>
										<a class="button" href="/leave-a-review/?testimonial_id=<?php echo get_the_ID(); ?>">Edit <span class="mobile-hide">Testimonial</span></a>
									<?php } ?>
									<div class="testimonial-left-holder">
										<div class="testimonial-byline"><h3><?php echo get_the_title(); ?></h3></div>
										<div class="testimonial-date"><?php echo $testimonial_date; ?></div>
										<div class="verified-badge">Verified Stay</div>
										<div class="clear"></div>
									</div>
									<div class="clear"></div>
								</div>
								<div class="testimonial-right">
									<div class="testimonial-content">
										<blockquote><?php the_content(); ?></blockquote>
									</div>
								</div>
								<div class="clear"></div>
							</div>
						<?php } ?>
					</div>
					<?php
						$GLOBALS['wp_query']->max_num_pages = $testimonials->max_num_pages;
						the_posts_pagination( array(
							'mid_size' => 1,
							'prev_text' => __( 'Newer Testimonials', '' ),
							'next_text' => __( 'Older Testimonials', '' ),
							'screen_reader_text' => __( 'Testimonials navigation' )
						));
					?>
				<?php } ?>
				<?php wp_reset_postdata(); ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
