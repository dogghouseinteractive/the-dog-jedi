<?php
/**
 * The template for displaying all single dog posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

//if ( ! current_user_can( 'manage_options' ) ) {
//	wp_redirect('/client-profile');
//	exit();
//}

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<section class="page-content">
			<div class="container">
				<div id="profile-main" class="profile-main">
					<?php $user_id = get_current_user_id();
					$dog_owner = get_field('owner');
					$owner_info = get_userdata($dog_owner);
					$owner_name = $owner_info->display_name;
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
					<?php if($user_id == $dog_owner || current_user_can('administrator')) { ?>
						<div id="client-dogs" class="client-dogs">
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
										<h3><?php echo $dog_name; ?><?php if(current_user_can('administrator')) { ?><span class="smaller-font">&nbsp;&nbsp;- Owner: <?php echo $owner_name; ?></span><?php } ?></h3>
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
						</div>
					<?php } else { ?>
						<p>You are not authorized to view this dog.</p>
					<?php } ?>
					<div class="clear"></div>
				</div>
			</div>
		</section>
	</main>
</div>
<?php get_footer(); ?>
