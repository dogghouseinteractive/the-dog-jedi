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
		<p>You do not have sufficient priviledges to access this page.</p>
	</main>
</div>
<?php get_footer(); ?>
