<?php
/**
 * The template for displaying all single posts and attachments
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
				<div class="page-header">
					<h1>Our Dogs</h1>
				</div>
				<div id="profile-main" class="profile-main">
					<h3 style="margin-top: -1em;"><a class="smooth" href="#in-memoriam">Jump to "In Memoriam"</a></h3>
					<?php if(have_posts()) { ?>
						<div id="client-dogs" class="client-dogs">
							<?php while(have_posts()) { ?>
								<?php the_post(); ?>
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
								$dog_image_data = wp_get_attachment_image_src($dog_image_id, 'avatar');
								$dog_image = $dog_image_data ? $dog_image_data[0] : '';
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
								<?php if( !has_term( 'in-memoriam', 'rainbow-bridge' ) ) { ?>
									<div class="client-dog">
										<div class="dog-image">
											<?php if($dog_image) { ?>
												<?php if(current_user_can('administrator')) { ?><a href="<?php echo get_permalink(); ?>"><?php } ?>
													<img src="<?php echo $dog_image; ?>">
												<?php if(current_user_can('administrator')) { ?></a><?php } ?>
											<?php } else { ?>
												<?php if(current_user_can('administrator')) { ?><a href="<?php echo get_permalink(); ?>"><?php } ?>
													<img src="<?php echo wp_get_attachment_image_src('150', 'avatar')[0]; ?>">
												<?php if(current_user_can('administrator')) { ?></a><?php } ?>
											<?php } ?>
										</div>
										<div class="dog-basic-info">
											<?php if($dog_name) { ?>
												<h3><?php if(current_user_can('administrator')) { ?><a href="<?php echo get_permalink(); ?>"><?php echo $dog_name; ?></a><?php } else { ?><?php echo $dog_name; ?><?php } ?><?php if(current_user_can('administrator')) { ?><span class="smaller-font">&nbsp;&nbsp;- Owner: <?php echo $owner_name; ?></span><?php } ?></h3>
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
										<?php if($user_id == $dog_owner || current_user_can('administrator')) { ?>
											<div class="dog-add-info">
												<div class="button-container">
													<a class="button" href="/update-dog?dog_id=<?php echo get_the_ID(); ?>">Edit Dog</a>
												</div>
											</div>
										<?php } ?>
										<div class="clear"></div>
									</div>
								<?php } ?>
							<?php } ?>	
							<div class="clear"></div>
						</div>
					<?php } ?>
				</div>
			</div>
		</section>
		<section id="in-memoriam">
			<div class="container">
				<div class="in-memoriam">
					<h2>In Memoriam</h2>
					<h3>To those we have come to love and lost:</h3>
					<p>We will forever cherish the time we were given with you. We are so grateful for the impact you made on our lives, and the place you will forever hold in our hearts. Until we meet again, old friends, may you rest in peace, and forever in love.</p>
				</div>
					<div class="rainbow-bridge">
					<?php $args = array(
						'post_type' => 'dog',
						'tax_query' => array(
							array(
								'taxonomy' => 'rainbow-bridge',
								'field' => 'slug',
								'terms' => 'in-memoriam',
							),
						), 
					);
					$rainbow_bridge = new WP_Query($args); ?>
					<?php if($rainbow_bridge->have_posts()) { ?>
						<?php while($rainbow_bridge->have_posts()) { ?>
							<?php $rainbow_bridge->the_post(); ?>
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
								$dog_image_data = wp_get_attachment_image_src($dog_image_id, 'avatar');
								$dog_image = $dog_image_data ? $dog_image_data[0] : '';
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
										<?php if(current_user_can('administrator')) { ?><a href="<?php echo get_permalink(); ?>"><?php } ?>
											<img src="<?php echo $dog_image; ?>">
										<?php if(current_user_can('administrator')) { ?></a><?php } ?>
									<?php } else { ?>
										<?php if(current_user_can('administrator')) { ?><a href="<?php echo get_permalink(); ?>"><?php } ?>
											<img src="<?php echo wp_get_attachment_image_src('150', 'avatar')[0]; ?>">
										<?php if(current_user_can('administrator')) { ?></a><?php } ?>
									<?php } ?>
								</div>
								<div class="dog-basic-info">
									<?php if($dog_name) { ?>
										<h3><?php if(current_user_can('administrator')) { ?><a href="<?php echo get_permalink(); ?>"><?php echo $dog_name; ?></a><?php } else { ?><?php echo $dog_name; ?><?php } ?><?php if(current_user_can('administrator')) { ?><span class="smaller-font">&nbsp;&nbsp;- Owner: <?php echo $owner_name; ?></span><?php } ?></h3>
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
								<?php if($user_id == $dog_owner || current_user_can('administrator')) { ?>
									<div class="dog-add-info">
										<div class="button-container">
											<a class="button" href="/update-dog?dog_id=<?php echo get_the_ID(); ?>">Edit Dog</a>
										</div>
									</div>
								<?php } ?>
								<div class="clear"></div>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
		</section>
	</main>
</div>
<?php get_footer(); ?>
