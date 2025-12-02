<?php
/**
 * Template Name: Update Profile
 *
 */

$user = wp_get_current_user(); 

acf_form_head();

$settings = array(
	'id' => 'add_dog',
	'post_id' => 'new_post',
	'post_title' => true,
	'post_content' => false,
	'new_post' => array(
			'post_type' => 'dog',
			'post_status' => 'publish'
	),
	'fields' => array(
		'field_841a03646dacd',
		'field_841a05b8e34ff',
		'field_841a05c1e3500',
		'field_841a022fd2760',
		'field_841a0249d2761',
		'field_841a028ed2762',
		'field_841a02bbd2763',
		'field_841a02e9d2764',
		'field_841a0318d2765',
		'field_841a032ad2766',
		'field_841a0343d2767',
		'field_841a0ca3543db',
		'field_841a0cd8543dc',
		'field_841a0ce5543dd',
		'field_841a0d11543de',
  ),
	'form' => true, 
	'return' => add_query_arg( 'dog-added', 'true', home_url() . '/client-profile/#profile-main' ), 
	'html_before_fields' => '',
	'html_after_fields' => '',
	'uploader' => 'basic',
	'submit_value' => 'Save Dog Info' 
);

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<section class="page-content">
			<div class="container">
				<?php if($user->ID == 0) { ?>
					<div class="page-header">
						<h1>You must be logged in to view this page.</h1>
						<p><a href="/login">Go to login</a>.</p>
					</div>
				<?php } else { ?>
					<div class="page-header">
						<h1><?php echo get_the_title(); ?></h1>
					</div>
					<h3>Client Info</h3>
					<?php echo do_shortcode('[user_profile_avatar_upload]'); ?>
					<?php echo do_shortcode('[gravityform id="3" title="false"]'); ?>
					<div class="clear"></div>
					<div id="update-dogs">
						<h3>Add Dog</h3>
						<?php acf_form($settings); ?>
					</div>
				<?php } ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
