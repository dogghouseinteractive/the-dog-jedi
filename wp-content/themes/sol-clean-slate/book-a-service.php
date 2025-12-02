<?php
/**
 * Template Name: Book a Service
 */

$user = wp_get_current_user(); 

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php if(has_post_thumbnail()) { ?>
			<div class="featured-image" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);"></div>
		<?php } ?>
		<section class="page-content">
			<div class="container">
				<?php if($user->ID != 0 && current_user_can('administrator') || current_user_can('customer')) { ?>
					<?php if(have_posts()) { ?>
						<?php while(have_posts()) {
							the_post(); ?>
							<div class="entry-content">
								<?php the_content(); ?>
							</div>
						<?php }
					} ?>
				<?php } else { ?>
					<div class="closing-notice">
						<p><strong>The Dog Jedi has officially closed its doors, effective 09/22/2024.</strong> We will miss seeing your dogs, but after nearly 4 years providing safe, intimate boarding for more than 150 different dogs, the time has come for Troy to become more available for the things in life that are important to him. Thank you so much for your business these past several years.</p>

						<p>Please reach out to Troy directly, via SMS, if you need recommendations on other dog sitters in the area.</p>
					</div>
				<?php } ?>
				<div class="clear"></div>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php if($user->ID != 0) { ?>
<?php $dates = json_encode(array_values(get_blackout_dates())); ?>

	<script type="text/javascript">
		// Disable Blackout Dates from Datepickers
		gform.addFilter( 'gform_datepicker_options_pre_init', function( optionsObj, formId, fieldId ) {
			if ( formId == 7 ) {
					var disabledDays = JSON.parse('<?php echo $dates; ?>');
					// console.log(disabledDays);
					optionsObj.beforeShowDay = function(date) {
							var checkdate = jQuery.datepicker.formatDate('yy-mm-dd', (new Date(date)));
							//console.log(checkdate);
							var isDisabled = (jQuery.inArray(checkdate, disabledDays) != -1);
							return [!isDisabled];
					}
			}
			return optionsObj;
		});
		// Alert if a Blackout date is included within the booking start and end date range
		jQuery('#input_7_3, #input_7_4').on('change', function () {
			var disabledDays = JSON.parse('<?php echo $dates; ?>');
			var startDate = jQuery('#input_7_3').val();
			var endDate = jQuery('#input_7_4').val();
			var duration = (Date.parse(endDate) - Date.parse(startDate)) / 86400000;
			let start = Date.parse( startDate );
			let end = Date.parse( endDate );
			let isBlackout = false;
			var selectedBlackouts = [];
			jQuery.each(disabledDays, function( index, blackoutDate ) {
				let blackout = Date.parse( blackoutDate );
				if(blackout >= start && blackout <= end) {
					isBlackout = true;
					selectedBlackouts.push(blackout);
				}
			});
			if(isBlackout) {
				let formattedBlackouts = [];
				jQuery(selectedBlackouts).each(function() {
					formattedBlackouts.push(jQuery.datepicker.formatDate( 'MM d', new Date(this) ) );
				});
				var msg = '';
				if(formattedBlackouts.length > 1) {
					formattedBlackouts = formattedBlackouts.join( ", " );
					msg = formattedBlackouts + ' fall within your requested booking dates, but are UNAVAILABLE. Please adjust your requested dates accordingly, or contact Troy directly for any questions. Should you continue to complete this booking request with the currently selected dates, please note that it will NOT be accepted. Thank you.';
				} else {
					formattedBlackouts = formattedBlackouts.join( ", " );
					msg = formattedBlackouts + ' falls within your requested booking dates, but is UNAVAILABLE. Please adjust your requested dates accordingly, or contact Troy directly for any questions. Should you continue to complete this booking request with the currently selected dates, please note that it will NOT be accepted. Thank you.';
				}
				alert(msg);	
			}
		});
	</script>
<?php } ?>

<?php get_footer();
