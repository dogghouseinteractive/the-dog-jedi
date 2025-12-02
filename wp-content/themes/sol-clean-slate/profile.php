<?php
/**
 * Template Name: Profile
 */

$user = wp_get_current_user(); 

$service = (empty($_GET['service']) ? '' : $_GET['service']);
$dogs = (empty($_GET['dogs']) ? '' : $_GET['dogs']);
$start_date = (empty($_GET['start_date']) ? '' : $_GET['start_date']);
$end_date = (empty($_GET['end_date']) ? '' : $_GET['end_date']);

$tipped = (empty($_GET['tip_received']) || $_GET['tip_received'] != 'true' ? false : true );

get_header(); ?>

<?php if($tipped) { ?>
	<a id="show-confirmation-message" data-fancybox data-src="#confirmation-message" href="javascript:;"></a>
	<div id="confirmation-message" class="modal-content">
		<p>Thank you for your generosity! Your tip has been received and Troy will be notified shortly.</p>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('a#show-confirmation-message').click();
		});
	</script>
<?php } ?>

<?php if($service && $dogs && $start_date) { ?>
	<a id="show-confirmation-message" data-fancybox data-src="#confirmation-message" href="javascript:;"></a>
	<div id="confirmation-message" class="modal-content">
		<p>Your booking request for <strong><?php echo $service; ?></strong> for <strong><?php echo $dogs; ?></strong><?php if($end_date && $end_date != $start_date) { ?> from <?php } else { ?> on <?php } ?><strong><?php echo date('F j Y', strtotime($start_date)); ?></strong> <?php if($end_date && $end_date != $start_date) { ?> to <strong><?php echo date('F j, Y', strtotime($end_date)); ?></strong><?php } ?> is complete.</p>
		<p>We will be in touch once we have confirmed this booking request, and we will let you know before we charge your card for this booking. Please contact Troy at 678-768-6866 if you have any questions, or need to cancel this request.</p>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('a#show-confirmation-message').click();
		});
	</script>
<?php } ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<section class="page-content">
			<div class="container">
				
				<?php if($user->ID == 0) { ?>
	
					<div class="page-header">
						<h1>You need to be logged in to view this page.</h1>
						<p><a href="/login">Got to login</a></p>
					</div>
				
				<?php } else if(current_user_can('administrator') || current_user_can('customer')) { ?>
				
					<div class="page-header">
						<?php $updated = (empty($_GET['updated']) || $_GET['updated'] != 'true' ? false : true); ?>
						<?php if($updated) { ?>
							<div class="updated-message"><p style="margin-top: 0.25em;">Your profile was successfully updated.</p></div>
						<?php } ?>
						<h1>Client Profile</h1>
					</div>
					<?php
						$name = $user->display_name;
						$email = $user->user_email;
						$profile_image = do_shortcode('[user_profile_avatar user_id="'.$user->id.'" size="avatar"]');
						$phone = get_field('phone', 'user_' . $user->ID);
					?>
					<div class="profile-top">
						<?php if($profile_image) { ?>
							<div class="profile-image">
								<?php echo $profile_image; ?>
	<!--							<p><?php //echo $update_profile_image; ?></p>-->
							</div>
						<?php } ?>
						<div class="profile-details">
							<?php if($name) { ?>
								<h2><?php echo $name; ?></h2>
							<?php } ?>
							<?php if($email) { ?>
								<p><?php echo $email; ?></p>
							<?php } ?>
							<?php if($phone) { ?>
								<p><?php format_phone_number($phone); ?></p>
							<?php } ?>
							<div class="button-container">
								<a class="button" href="/update-client-profile">Edit Profile</a>
								<a class="button" href="<?php echo wp_logout_url(); ?>">Logout</a>
							</div>
						</div>
						<div class="client-bookings">
							<?php $upcoming_bookings = array(); ?>
							<?php $past_bookings = array(); ?>
							<?php $args = array(
								'post_type' => 'booking',
								'posts_per_page' => -1,
								'post_status' => array('publish', 'draft'),
								'author' => get_current_user_id(),
							); ?>
							<?php $client_bookings = new WP_Query($args); ?>
							<?php while($client_bookings->have_posts()) { ?>
								<?php $client_bookings->the_post(); ?>
								<?php 
									$booking_start_date = get_field('booking_start_date');																		$booking_end_date = get_field('booking_end_date');
									$booking_dropoff_time = get_field('drop-off_time');
									$booking_pickup_time = get_field('pick-up_time');
									$service = get_field('service');
									$dogs_involved = get_field('dogs_involved');	
									$pending = get_post_status();																				 
								?>
								<?php if((strtotime('today') <= strtotime($booking_end_date) && $booking_end_date != '1970-01-01') || ($service == 'daycare' || $service == 'Doggy Daycare') && strtotime('today') <= strtotime($booking_start_date)) { ?>
									<?php $upcoming_booking = array();
												$upcoming_booking['start_date'] = $booking_start_date;
												$upcoming_booking['end_date'] = $booking_end_date;
												$upcoming_booking['dropoff_time'] = $booking_dropoff_time;
												$upcoming_booking['pickup_time'] = $booking_pickup_time;
												$upcoming_booking['service'] = $service;
												$upcoming_booking['dogs_involved'] = $dogs_involved;
												$upcoming_booking['status'] = get_post_status();
												$upcoming_bookings[] = $upcoming_booking;
									?>																															
								<?php } else { ?>
									<?php $past_booking = array();
												$past_booking['start_date'] = $booking_start_date;
												$past_booking['end_date'] = $booking_end_date;
												$past_booking['service'] = $service;
												$past_booking['dogs_involved'] = $dogs_involved;
												$past_bookings[] = $past_booking;
									?>	
								<?php } ?>
							<?php } ?>
							<?php wp_reset_postdata(); ?>
							<div class="upcoming-bookings">
								<h2>Upcoming Bookings</h2>
								<?php if(!empty($upcoming_bookings)) { ?>
									<?php foreach($upcoming_bookings as $upcoming_booking) { ?>
										<?php $dogs = array(); ?>
										<?php foreach($upcoming_booking['dogs_involved'] as $dog_id) {
											$dogs[] = get_the_title($dog_id);
										} ?>
										<div class="booking <?php echo $upcoming_booking['status']; ?>">
											<p><strong><?php echo ucfirst($upcoming_booking['service']); ?> for <?php echo implode(', ', $dogs); ?></strong><?php if($upcoming_booking['status'] == 'draft') { ?> <span class="smaller-font">(PENDING)</span><?php } ?><br>
											<?php if($upcoming_booking['service'] == 'Dog Boarding' || $upcoming_booking['service'] == 'boarding') { ?>
												Drop-off: <?php echo date('g:i a', strtotime($upcoming_booking['dropoff_time'])); ?> on <?php echo date('F j, Y', strtotime($upcoming_booking['start_date'])); ?> - Pick-up: <?php echo date('g:i a', strtotime($upcoming_booking['pickup_time'])); ?> on <?php echo date('F j, Y', strtotime($upcoming_booking['end_date'])); ?>
											</p>
											<?php } else { ?>
												<?php echo date('F j, Y', strtotime($upcoming_booking['start_date'])); ?> - <?php echo date('g:i a', strtotime($upcoming_booking['dropoff_time'])); ?> - <?php echo date('g:i a', strtotime($upcoming_booking['pickup_time'])); ?></p>
											<?php } ?>
										</div>
									<?php } ?>  									 
								<?php } else { ?>
									<p>You have no upcoming bookings at this time.</p>
								<?php } ?>
								<div class="button-container">
									<a class="button" href="/book-a-service">Book a service</a>	
								</div>
							</div>
							<div class="past-bookings">
								<h2>Past Bookings</h2>
								<?php if(!empty($past_bookings)) { ?>
									<div class="button-container">
										<a class="button" href="/leave-a-review">Leave a Review</a>
										<a class="button" href="/leave-a-tip">Leave a Tip</a>
									</div>
									<?php foreach($past_bookings as $past_booking) { ?>
										<?php $dogs = array(); ?>
										<?php foreach($past_booking['dogs_involved'] as $dog_id) {
											$dogs[] = get_the_title($dog_id);
										} ?>
										<div class="booking">
											<p><strong><?php echo ucfirst($past_booking['service']); ?> for <?php echo implode(', ', $dogs); ?></strong><br>
											<?php if($past_booking['service'] == 'Dog Boarding' || $upcoming_booking['service'] == 'boarding') { ?>
												<?php echo date('F j, Y', strtotime($past_booking['start_date'])) . ' - ' . date('F j, Y', strtotime($past_booking['end_date'])); ?></p>
											<?php } else { ?>
												<?php echo date('F j, Y', strtotime($past_booking['start_date'])); ?></p>
											<?php } ?>
										</div>
									<?php } ?> 
								<?php } else { ?>
									<p>You have no past bookings.</p>
								<?php } ?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div id="profile-main" class="profile-main">
						<h2>Client Dogs</h2>
						<div class="button-container">
							<a class="button" href="/update-client-profile/#update-dogs">Add Dog(s)</a>
						</div>
						<?php $dogs_updated = (empty($_GET['dogs-updated']) || $_GET['dogs-updated'] != 'true' ? false : true); ?>
						<?php if($dogs_updated) { ?>
							<div class="dogs-updated-message"><p style="margin-top: 0.25em;">Your dog was successfully updated.</p></div>
						<?php } ?>
						<?php $dog_added = (empty($_GET['dog-added']) || $_GET['dog-added'] != 'true' ? false : true); ?>
						<?php if($dog_added) { ?>
							<div class="dogs-updated-message"><p style="margin-top: 0.25em;">Your dog was successfully added.</p></div>
						<?php } ?>
						<div class="clear"></div>
						<?php
							$current_user_id = get_current_user_id();
							$args = array(
								'post_type' => 'dog',
								'posts_per_page' => -1,
								'meta_query'     => array(
									array(
										'key'     => 'owner',
										'value'   => $current_user_id,
									),
								),
							); ?>
							<?php $dogs = new WP_Query($args); ?>
							<?php if($dogs->have_posts()) { ?>
								<div id="client-dogs" class="client-dogs">
								<?php while($dogs->have_posts()) {
									$dogs->the_post();
									$dog_name = get_the_title();
									$dog_image_id = '';
									if(has_post_thumbnail()) {
										$dog_image_id = get_post_thumbnail_id();
									} else {
										$dog_image_id = get_field('image');
									}
									$dog_image = wp_get_attachment_image_src($dog_image_id, 'avatar')[0];
									$dog_breed = get_field('breed');
									$dog_weight = get_field('weight');
									$dog_gender = get_field('gender');
									$birthdate = get_field('birthdate');
									//get age from date or birthdate
									$age = getAge($birthdate);
									//$age = $birthdate;
									$fixed = (!empty(get_field('fixed') && get_field('fixed') == 'yes' ? true : false));
									$chipped = (!empty(get_field('microchipped') && get_field('microchipped') == 'yes' ? true : false));
									$dog_friendly = (!empty(get_field('dog_friendly') && get_field('dog_friendly') == 'yes' ? true : false));
									$people_friendly = (!empty(get_field('people_friendly') && get_field('people_friendly') == 'yes' ? true : false));
									$potty_trained = (!empty(get_field('potty_trained') && get_field('potty_trained') == 'yes' ? true : false));
									$crate_trained = (!empty(get_field('crate_trained') && get_field('crate_trained') == 'yes' ? true : false));
									$medications = get_field('medications');
									$feeding_instructions = get_field('feeding_instructions');
									$vet_info = get_field('veterinarian_information');
									$vaccination_records = get_field('vaccination_records'); ?>
									<div class="client-dog">
										<div class="dog-image">
											<?php if($dog_image) { ?>
												<img src="<?php echo $dog_image; ?>">
											<?php } else { ?>
												<img src="<?php echo wp_get_attachment_image_src('150', 'avatar')[0]; ?>">
											<?php } ?>
										</div>
										<div class="dog-basic-info">
											<?php if($dog_name) { ?>
												<h3><?php echo $dog_name; ?></h3>
											<?php } ?>
											<?php if($dog_breed) { ?>
												<p><?php echo $dog_breed; ?></p>
											<?php } ?>
											<?php if($dog_weight || $dog_gender) { ?>
												<p>
													<?php if($dog_weight) { ?>
														<?php echo $dog_weight . ' lbs.'; ?>
													<?php } ?>
													<?php if($dog_gender && !$dog_weight) { ?>
														<?php echo $dog_gender; ?>
													<?php } else if($dog_gender && $dog_weight) { ?>
														<?php echo '&nbsp;|&nbsp;&nbsp;' . $dog_gender; ?>
													<?php } ?>
												</p>
											<?php } ?>
											<?php if($age) { ?>
												<p><?php echo $age; ?></p>
											<?php } ?>
										</div>
										<div class="dog-add-info">
											<p>
												<?php if($fixed) { ?>
													Fixed
												<?php } else { ?>
													Unfixed
												<?php } ?>
												<?php if($chipped) { ?>
													&nbsp;| &nbsp;Chipped
												<?php } else { ?>
													&nbsp;| &nbsp;Not Chipped
												<?php } ?>
											</p>
											<p>
												<?php if($dog_friendly) { ?>
													Dog Friendly
												<?php } else { ?>
													Not Dog Friendly
												<?php } ?>
												<?php if($people_friendly) { ?>
													&nbsp;| &nbsp;People Friendly
												<?php } else { ?>
													&nbsp;| &nbsp;Not People Friendly
												<?php } ?>
											</p>
											<p>
												<?php if($potty_trained) { ?>
													Potty Trained
												<?php } else { ?>
													Not Potty Trained
												<?php } ?>
												<?php if($crate_trained) { ?>
													&nbsp;| &nbsp;Crate Trained
												<?php } else { ?>
													&nbsp;| &nbsp;Not Crate Trained
												<?php } ?>
											</p>
											<div class="button-container">
												<a class="button" href="/update-dog?dog_id=<?php echo get_the_ID(); ?>">Edit Dog</a>
											</div>
										</div>
										<div class="clear"></div>
										<div class="dog-detailed-info">
											<?php if($medications) { ?>
												<div class="medications">
													<h4>Medications:</h4>
													<?php echo wpautop($medications); ?>
												</div>
											<?php } ?>
											<?php if($feeding_instructions) { ?>
												<div class="feeding-instructions">
													<h4>Feeding Instructions:</h4>
													<?php echo wpautop($feeding_instructions); ?>
												</div>
											<?php } ?>
											<?php if($vet_info) { ?>
												<div class="vet-info">
													<h4>Vet Info:</h4>
													<?php echo wpautop($vet_info); ?>
												</div>
											<?php } ?>
											<?php if($vaccination_records) { ?>
												<div class="vaccination-records">
													<h4>Vaccination Records</h4>
													<p><a target="_blank" href="<?php echo $vaccination_records; ?>">View your uploaded document</a>.</p>
												</div>
											<?php } ?>
										</div>
										<div class="clear"></div>
									</div>
								<?php } ?>
							</div>
						<?php } else { ?>
							<p>You have not yet added any dogs to your profile.</p>
						<?php } ?>
						<?php wp_reset_postdata(); ?>
					</div>
					<div class="clear"></div>
				<?php } else { ?>
					<div class="closing-notice">
						<p><strong>The Dog Jedi has officially closed its doors, effective 09/22/2024.</strong> We will miss seeing your dogs, but after nearly 4 years providing safe, intimate boarding for more than 150 different dogs, the time has come for Troy to become more available for the things in life that are important to him. Thank you so much for your business these past several years.</p>

						<p>Please reach out to Troy directly, via SMS, if you need recommendations on other dog sitters in the area.</p>
					</div>
				<?php } ?>
			</div>
		</section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
