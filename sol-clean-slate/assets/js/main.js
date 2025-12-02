jQuery(document).ready(function() {
	// Smooth Scroll
	jQuery('a.smooth').click(function() {
		// On-page links
    if (
      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
      && 
      location.hostname == this.hostname
    ) {
      // Figure out element to scroll to
      var target = jQuery(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      // Does a scroll target exist?
      if (target.length) {
        // Only prevent default if animation is actually gonna happen
        event.preventDefault();
        jQuery('html, body').animate({
          scrollTop: target.offset().top
        }, 500, function() {
          // Callback after animation
          // Must change focus!
          var $target = jQuery(target);
          $target.focus();
          if ($target.is(":focus")) { // Checking if the target was focused
            return false;
          } else {
            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
            $target.focus(); // Set focus again
          };
        });
      }
    }
	});
	
	//Handle the Hamburger open/close logic
	jQuery('#hamburger').on('click', function() {
		if(jQuery(this).hasClass('clicked')) {
			jQuery(this).removeClass('clicked');
			jQuery('body').removeClass('nav-is-open');
		} else {
			jQuery(this).addClass('clicked');
			jQuery('body').addClass('nav-is-open');
		}
		if(jQuery('.top-bun, .patty, .bottom-bun').hasClass('animated')) {
			jQuery('.top-bun, .patty, .bottom-bun').removeClass('animated').css('opacity', '1.0');
		}
		jQuery('#hamburger-toggle-menu').toggleClass('clicked');
	});
	
	var windowWidth = jQuery(window).width();
	var windowHeight = jQuery(window).height();
	
	jQuery('#update-dogs #acf-form .acf-repeater .acf-table tbody .acf-row').each(function() {
		var thisDog = jQuery(this);
		var removeButton = '<div class="acf-field remove-dog"><div class="acf-label"><label></label></div><div class="acf-input"><a class="button" href="#">Remove Dog</a></div></div>';
		thisDog.children('td.acf-fields').append(removeButton);
	});
	jQuery(document).on('click', '.remove-dog .button', function() {
		jQuery(this).closest('.acf-row').find('td.remove').find('a[data-event="remove-row"]').trigger('click').click();
	});
	jQuery('.acf-form-submit input[type="submit"]').on('click', function() {
		setTimeout(function() {
			jQuery('.acf-form-submit input[type="submit"]').click();
		}, 10);
	});
});

function updatePricing() {
	var numDogs = jQuery('#field_7_11 input').filter(':checked').length;
	var price = 0;
	var year = (new Date).getFullYear();
	var nextYear = year + 1;
	var holidays = [year+'-01-01',+year+'-01-17',+year+'-01-18',+year+'-01-19',+year+'-01-20',+year+'-02-14',+year+'-02-15',+year+'-02-16',+year+'-04-18',+year+'-04-19',+year+'-04-20',+year+'-05-11',+year+'-05-23',+year+'-05-24',+year+'-05-25',+year+'-05-26',+year+'-07-04',+year+'-07-05',+year+'-07-06',+year+'-10-10',+year+'-10-11',+year+'-10-12',+year+'-10-13',+year+'-11-21',+year+'-11-22',+year+'-11-23',+year+'-11-24',+year+'-11-25',+year+'-11-26',+year+'-11-27',+year+'-11-28',+year+'-11-29',+year+'-11-30',+year+'-12-19',+year+'-12-20',+year+'-12-21',+year+'-12-22',+year+'-12-23',+year+'-12-24',+year+'-12-25',+year+'-12-26',+year+'-12-27',+year+'-12-28',+year+'-12-29',+year+'-12-30',+year+'-12-31',+nextYear+'-01-01'];
	var startDate = jQuery('#input_7_3').val();
	var endDate = jQuery('#input_7_4').val();
	var duration = (Date.parse(endDate) - Date.parse(startDate)) / 86400000;
	
	let start = Date.parse( startDate );
	let end = Date.parse( endDate );
	let isHoliday = false;
	var selectedHolidays = [];
	jQuery.each(holidays, function( index, holidayDate ) {
		let holiday = Date.parse( holidayDate );
		if(holiday >= start && holiday <= end) {
			isHoliday = true;
			selectedHolidays.push(holiday);
		}
	});
	
	if(jQuery('#input_7_18 :selected').text() == 'Dog Boarding') {
		var basePrice = 70.00;
		var addPrice = 20.00;
		if(isHoliday && jQuery('#field_7_11 input').filter(':checked').length > 0) {
			jQuery('#booking-messages').addClass('active').empty().append('One or more dates for this booking occurs during a Holiday peak. Pricing will be updated accordingly.');
		} else {
			jQuery('#booking-messages').removeClass('active').empty();
		}
	} else if(jQuery('#input_7_18 :selected').text() == 'Doggy Daycare') {
		if(isHoliday && jQuery('#field_7_11 input').filter(':checked').length > 0) {
			jQuery('#booking-messages').addClass('active').empty().append('One or more dates for this booking falls on a Holiday. Pricing will be updated accordingly.');
			var basePrice = 50.00;
			var addPrice = 20.00;
		} else {
			jQuery('#booking-messages').removeClass('active').empty();
			var basePrice = 40.00;
			var addPrice = 15.00;
		}
	}
	
	// 1. Create an empty array to hold the values.
	var selectedDogIds = [];

	// 2. Select only the inputs that are currently ":checked" within your field.
	//    Note: The code in the doc uses #input_7_11, which is the direct container. 
	//    #field_7_11 also works.
	jQuery('#field_7_11 input[type="checkbox"]:checked').each(function() {

		// 3. For each checked box, push its value into the array.
		selectedDogIds.push(jQuery(this).val());
	});

	// Now, the 'selectedDogIds' variable holds an array of the selected IDs.
	// For example: [101, 105] or just [103]
	console.log(selectedDogIds); 
	
	var userID = jQuery('#field_7_28 input').attr('value');
	console.log(userID);

	// Check if any selected dog name matches "Dolly" (case-insensitive)
	var hasDolly = selectedDogIds.some(function(dogId) {
		return String(dogId).toLowerCase() === 'dolly';
	});

	if(userID == 10 && hasDolly) {
		var price = 0.00;
		// Exit early to prevent any pricing calculations
		jQuery('#input_7_14').attr('value', '$0.00').val('$0.00').change();
		jQuery('#input_7_19').attr('value', '$0.00').val('$0.00').change();
		return;
	} else if(numDogs > 1) {
		var price = basePrice + ( ( numDogs - 1 ) * addPrice );
	} else if(numDogs == 1) {
		var price = basePrice;
	} else {
		var price = '0.00';
	}

	if(duration > 0) {
		if(selectedHolidays.length > 0) {
			var addons = 0;
			if(numDogs > 1) {
				addons = 10 + ( ( numDogs - 1 ) * 5 );
			} else {
				addons = 10;
			}
			price = ( price * duration ) + ( (selectedHolidays.length - 1) * addons );
		} else {
			price = price * duration;
		}
	}
	var dropoffTime = jQuery('#input_7_5_1').val();
	dropoffTime += jQuery('#input_7_5_2').val();
	dropoffTime = parseInt(dropoffTime);
	if(jQuery('#input_7_5_3').val() == 'pm') {
		dropoffTime = parseInt(dropoffTime) + 1200;
	}
	var pickupTime = jQuery('#input_7_6_1').val();
	pickupTime += jQuery('#input_7_6_2').val();
	pickupTime = parseInt(pickupTime);
	if(jQuery('#input_7_6_3').val() == 'pm' && jQuery('#input_7_6_1').val() != 12 ) {
		pickupTime = parseInt(pickupTime) + 1200;
	} 
	if(jQuery('#input_7_6_3').val() == 'am' && jQuery('#input_7_6_1').val() == 12 ) {
		pickupTime = parseInt(pickupTime) + 1200;
	} 
	var extendedStay = pickupTime - dropoffTime;
	if(extendedStay >= 100 && jQuery('#input_7_18 :selected').text() == 'Dog Boarding') {
		extendedStay = extendedStay / 100;
		if(extendedStay > 4) {
			extendedStay = ( extendedStay - 4 ) * 5;
			if(extendedStay >= 70) {
				extendedStay = 70;
			}
		} else {
			extendedStay = 0;
		}
		price = price + extendedStay;
	} else if(extendedStay >= 100 && jQuery('#input_7_18 :selected').text() == 'Doggy Daycare') {
		extendedStay = extendedStay / 100;
		if(extendedStay > 8) {
			extendedStay = ( extendedStay - 8 ) * 5;
			if(extendedStay >= 30) {
				extendedStay = 30;
			}
		} else {
			extendedStay = 0;
		}
		price = price + extendedStay;
	}
	jQuery('#input_7_14').attr('value', '$' + price).val('$' + price).change();
	jQuery('#input_7_19').attr('value', '$' + price).val('$' + price).change();
	
//	// Ensure Stripe.js has loaded and elements are ready
//  var stripe = Stripe('pk_live_51OAfpJEXr50M9CXFQE04iBV8kRDpBObFLM7h1A17FWPNKtJMozsBphhcWyfcn6mWDHpyPL0BZ2OOdoGZFBlzvvkc0061Fkic0L'); 
//
//  // Get Stripe Payment Intent ID (This will depend on how Gravity Forms provides it)
//  var paymentIntentId = gforms_stripe_frontend_strings.create_payment_intent_nonce;
//	
//	console.log(stripe);
//	console.log(paymentIntentId);
//	
//	// Update Stripe Payment Intent
//	stripe.paymentIntents.update(paymentIntentId, {
//		amount: price,
//	});
}

jQuery(document).on('gform_post_render', function(event, form_id, current_page) {
	updatePricing();
	
	jQuery('#field_7_11 .gchoice input').click('change', function() {
		updatePricing();
	});
		
	jQuery('#input_7_6_1, #input_7_6_2, #input_7_6_3, #input_7_5_1, #input_7_5_2, #input_7_5_3, #input_7_18').on('change', function() {
		updatePricing();
	});
	
	jQuery('#input_7_3').on('change', function() {	
		var startDate = jQuery('#input_7_3').val();
		var endDate = jQuery('#input_7_4').val();
		if(endDate < startDate) {
			jQuery('#input_7_4').datepicker('option', 'minDate', startDate).datepicker('setDate', startDate);
		} else {
			jQuery('#input_7_4').datepicker('option', 'minDate', startDate);
		}
		updatePricing();
	});
	
	jQuery('#input_7_4').on('change', function() {
		var startDate = jQuery('#input_7_3').val();
		var endDate = jQuery('#input_7_4').val();
		if(endDate < startDate) {
			alert('Service end date cannot be before start date. Please try again.');
			jQuery('#input_7_4').val(startDate);
		}
		updatePricing();
	});
});


jQuery(window).resize(function() {
	var windowWidth = jQuery(window).width();
	var windowHeight = jQuery(window).height();
});