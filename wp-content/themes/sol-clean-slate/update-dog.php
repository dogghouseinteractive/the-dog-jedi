<?php
/**
 * Template Name: Update Dog
 *
 */

$user = wp_get_current_user(); 
$dog_id = (!empty($_GET['dog_id']) ? $_GET['dog_id'] : '');
$dog_owner = get_field('owner', $dog_id);

if(!$user) {
	wp_redirect( wp_login_url() );
	exit;
}

acf_form_head();

$settings = array(
	'id' => 'update_dog',
	'post_id' => $dog_id,
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
	'post_title' => true,
	'return' => add_query_arg( 'dogs-updated', 'true', home_url() . '/client-profile/#profile-main' ), 
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
				<div class="page-header">
					<h1><?php echo get_the_title(); ?></h1>
				</div>
				<?php if($user->ID == $dog_owner || current_user_can('administrator')) { ?>
					<div id="update-dogs">
						<h3>Dog Info</h3>
						<?php acf_form($settings); ?>
					</div>
				<?php } else { ?>
					<h2>You don't have permissions to edit this dog.</h2>	
				<?php } ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
