<?php
/**
 * Template Name: Leave a Review
 */

$user = wp_get_current_user(); 

if(!$user) {
	wp_redirect( wp_login_url() );
	exit;
}

acf_form_head();

$user_first_name = get_user_meta( $user->ID, 'first_name', true );
$user_last_name = get_user_meta( $user->ID, 'last_name', true );
$user_last_initial = substr( $user_last_name, 0, 1 );

$existing_testimonial = (!empty($_GET['testimonial_id']) ? $_GET['testimonial_id'] : '');

$settings = array(
  'post_title'    => false,
  'post_content'  => true,
	'form' => true, 
	'html_before_fields' => '',
	'html_after_fields' => '',
	'uploader' => 'basic',
);

if($existing_testimonial) {
	$settings['post_id'] = $existing_testimonial;
	$settings['submit_value'] = 'Update Review';
	$settings['return'] = add_query_arg( 'testimonial_updated', 'true', home_url() . '/testimonials' ); 
} else {
	$settings['post_id'] = 'new_post';
  $settings['new_post'] = array(
    'post_type' => 'testimonial',
    'post_status' => 'publish',
  );
	$settings['return'] = add_query_arg( 'testimonial_created', 'true', home_url() . '/leave-a-tip' );
	$settings['submit_value'] = 'Submit Review';
}

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
							<h3>Hi, <?php echo $user_first_name . ' ' . $user_last_initial; ?>.</h3>
							<?php the_content(); ?>
							<?php acf_form($settings); ?>
						</div>
					<?php }
				} ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
