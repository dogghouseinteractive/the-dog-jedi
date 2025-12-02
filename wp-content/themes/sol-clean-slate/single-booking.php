<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

//if ( ! current_user_can( 'manage_options' ) ) {
	wp_redirect('/client-profile');
	exit();
//}

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php $dogs_involved = get_field_object('dogs_involved'); ?>
		<?php print_r($dog_owner); ?>
		<br>
		<?php echo get_current_user_id(); ?>
	</main>
</div>
<?php get_footer(); ?>
