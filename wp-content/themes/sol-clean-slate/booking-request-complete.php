<?php
/**
 * Template Name: Booking Request Complete
 */

$service = (empty($_GET['service']) ? '' : $_GET['service']);
$dogs = (empty($_GET['dogs']) ? '' : $_GET['dogs']);
$start_date = (empty($_GET['start_date']) ? '' : $_GET['start_date']);
$end_date = (empty($_GET['end_date']) ? '' : $_GET['end_date']);

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
						the_post(); ?>
						<div class="entry-content">
							<?php if($service && $dogs && $start_date && $end_date) { ?>
								<?php if($start_date == $end_date) { ?>
									<p>Your booking request for <strong><?php echo $service; ?></strong> for <strong><?php echo $dogs; ?></strong> on <strong><?php echo date('F j, Y', strtotime($start_date)); ?></strong> is complete.</p>
								<?php } else { ?>
									<p>Your booking request for <strong><?php echo $service; ?></strong> for <strong><?php echo $dogs; ?></strong> from <strong><?php echo date('F j Y', strtotime($start_date)); ?></strong> to <strong><?php echo date('F j, Y', strtotime($end_date)); ?></strong> is complete.</p>
								<?php } ?>
							<?php } ?>
							<?php the_content(); ?>
						</div>
					<?php }
				} ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php if(get_current_user_id() != 0) { ?>
	<script type="text/javascript">
		<?php $dates = json_encode(get_blackout_dates()); ?>
		gform.addFilter( 'gform_datepicker_options_pre_init', function( optionsObj, formId, fieldId ) {
			if ( formId == 1 && fieldId == 3 ) {
					var disabledDays = JSON.parse('<?php echo $dates; ?>');
					optionsObj.beforeShowDay = function(date) {
							var checkdate = jQuery.datepicker.formatDate('yy-mm-dd', (new Date(date)));
							return [disabledDays.indexOf(checkdate) == -1];
					};
					console.log( optionsObj.beforeShowDay( '2023-06-13' ) );
			}
			return optionsObj;
		});
	</script>
<?php } ?>

<?php get_footer();
