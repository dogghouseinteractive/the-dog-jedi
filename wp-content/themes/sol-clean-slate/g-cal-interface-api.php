<?php

/**
 * Template Name: Sync to Google Calendar
 */

$sessionLifetime = ini_set('session.gc_maxlifetime', 30*24*60*60); // 30 days in seconds
session_start(); 
$tokenExpiryTimestamp = time() + $sessionLifetime;

if (isset($_SESSION['access_token']) /* && isset($_SESSION['refresh_token'] ) */ ) {
//	$currentTime = time(); 
//	if ($tokenExpiryTimestamp <= $currentTime) {
//    // Token expired - Refresh
//    $newTokens = $client->refreshToken($refreshToken);
//    $newTokens['access_token'] = $_SESSION['access_token'];
//    // You might also want to update your $tokenExpiryTimestamp if you're storing it
//	}
	echo 'Access token' . /* and refresh token */ ' exist(s) <br><br>';
	
	require_once get_home_path() . 'vendor/autoload.php';
	$client = new Google_Client();
	$client->setClientId('1080532167388-evot5avjms5ld5qcq1br1gdak7s6v380.apps.googleusercontent.com');
	$client->setClientSecret('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');
	$client->setRedirectUri('https://thedogjedi.com/sync-to-google-calendar/'); 
	$client->addScope(Google_Service_Calendar::CALENDAR);
	$client->setAccessToken($_SESSION['access_token']);
	
	$google_service = new Google_Service_Calendar($client);

	// Get event data from your PHP calendar
	$booking_dates = array();
	$args = array(
		'post_type' => 'booking',
		'posts_per_page' => -1,
	);
	$bookings = new WP_Query($args);
	$booking_ids = array();
	$events = array();
	if($bookings->have_posts()) {
		while($bookings->have_posts()) {
			$bookings->the_post();
			$booking_ids[] = get_the_ID();
			$service = get_field('service');
			$dogs = get_field('dogs_involved');
			$start_date = get_field('booking_start_date');
			$end_date = get_field('booking_end_date');
			if(!$end_date) {
				$end_date = $start_date;
			}
			$dropoff_time = date('H:i:s', strtotime(get_field('drop-off_time')));
			$pickup_time = date('H:i:s', strtotime(get_field('pick-up_time')));
			if(!$dropoff_time || $dropoff_time == '00:00:00') {
				$dropoff_time = '08:00:00';
			}
			if(!$pickup_time || $pickup_time == '00:00:00') {
				$pickup_time = '08:00:00';
			}
			$user_bookings = array();
			$user_bookings['service'] = $service;
			$dog_names = array();
			$dog_links = array();
			foreach($dogs as $dog_id) {
				$dog_names[] = get_the_title($dog_id);
				$dog_links[] = get_the_permalink($dog_id);
			}
			$user_bookings['dogs'] = implode(', ', $dog_names);
			$user_bookings['dog_links'] = ($dog_links);
			$user_bookings['start_date'] = $start_date;
			$user_bookings['end_date'] = $end_date;
			$user_bookings['dropoff_time'] = $dropoff_time;
			$user_bookings['pickup_time'] = $pickup_time;
			$user_bookings['color'] = $color;
			$booking_dates[] = $user_bookings;

			$eventTitle = $user_bookings['dogs'];
			$eventDescription = 'tdj-booking-'.get_the_ID();
			$eventStart = $user_bookings['start_date'] . 'T' . $user_bookings['dropoff_time'] . '-05:00'; // Timezone example
			$eventEnd = $user_bookings['end_date'] . 'T' . $user_bookings['pickup_time'] . '-05:00';

			// Create Google Calendar Event object
			$events[] = new Google_Service_Calendar_Event([
					'summary' => $eventTitle,
					'description' => $eventDescription,
					'start' => ['dateTime' => $eventStart],
					'end' => ['dateTime' => $eventEnd],
			]);		
		}
	}

	// Query for existing Google Calendar Events (this will be paginated by 20)
	$search_results = $google_service->events->listEvents('troy@dogghouseinteractive.com');

	// Build an array that includes all pages of results 
	$allEvents = $search_results->getItems(); // Store initial results
	while($nextPageToken = $search_results->getNextPageToken()) {
		$search_results = $google_service->events->listEvents('troy@dogghouseinteractive.com', [
				'pageToken' => $nextPageToken
		]);
		$allEvents = array_merge($allEvents, $search_results->getItems()); // Append new results
	}

	// Store the original TDJ Post IDs (stored in the event's description) so that they can be checked against later
	$tdj_bookings = array();
	foreach($allEvents as $google_cal_event) {
		if(str_starts_with($google_cal_event->description, 'tdj-booking-')) {
			$tdj_bookings[] = $google_cal_event->description;
		}
	}

	// Create an array of events from the Google Calendar which no longer exist in TDJ
	$tdj_deleted_events = array();
	foreach($tdj_bookings as $tdj_booking) {
		$formatted_id = str_replace('tdj-booking-', '', $tdj_booking);
		if(!in_array($formatted_id, $booking_ids)) {
			$tdj_deleted_events[] = 'tdj-booking-' . $formatted_id;
		}
	}

	// Update existing TDJ events in Google Calendar
	foreach($allEvents as $gcal_event) {
		if(in_array($gcal_event->description, $tdj_bookings)) {
			// event is already in Google Calendar, check if title needs updating
			$booking_id = str_replace('tdj-booking-', '', $gcal_event->description);
			$dogs = get_field('dogs_involved', $booking_id);
			$dog_names = array();
			foreach($dogs as $dog) {
				$dog_names[] = get_the_title($dog);
			}
			$new_title = implode(', ', $dog_names);

			// Check if title needs updating
			if($gcal_event->summary != $new_title) {
				try {
					$gcal_event->setSummary($new_title);
					$updatedEvent = $google_service->events->update('troy@dogghouseinteractive.com', $gcal_event->getId(), $gcal_event);
					echo 'Event title updated to "' . $new_title . '" for booking ' . $gcal_event->description . '<br><br>';
				} catch (Exception $e) {
					echo "When attempting to update event title, an error occurred: " . $e->getMessage() . '<br><br>';
				}
			}
		}
	}

	// Add new events from TDJ to Google Calendar
	foreach($events as $new_event) {
		if(!in_array($new_event->description, $tdj_bookings)) {
			try {
				$reminderOverrides = [
						[
								'method' => 'popup',   // Reminder delivery method (email, popup, etc.)
								'minutes' => 1440        // Minutes before the event starts
						],
						// You can add more overrides if needed with different methods and minutes
				];
				$reminders = new Google_Service_Calendar_EventReminders();
				$reminders->setUseDefault(false);
				$reminders->setOverrides($reminderOverrides);
				$new_event->setReminders($reminders);
				$createdEvent = $google_service->events->insert('troy@dogghouseinteractive.com', $new_event);
				echo "Event Created! ID: " . $createdEvent->getId() . '<br><br>';
			} catch (Exception $e) {
				echo "When attempting to add a new event, an error occurred: " . $e->getMessage()  . '<br><br>';
			}
		}
	}

	// To Delete Events from Google Calendar that no longer exist in TDJ
	foreach($allEvents as $event) {
		$eventDesc = $event->description;
		$eventId = $event->getID();
		if(in_array($event->description, $tdj_deleted_events)) {
			try {
				$google_service->events->delete('troy@dogghouseinteractive.com', $eventId);
				echo 'Event ' . $eventDesc . ' was successfully deleted. <br><br>';
			} catch (Exception $e) {
				echo "When attempting to delete an event, an error occurred: " . $e->getMessage()  . '<br><br>';
			} 
		}
	}

	// To Delete ALL TDJ Events from Google Calendar 
	//foreach($allEvents as $event) {
	//	$eventDesc = $event->description;
	//	$eventId = $event->getID();
	//	if(str_starts_with($event->description, 'tdj-booking-')) {
	//		try {
	//			$google_service->events->delete('troy@dogghouseinteractive.com', $eventId);
	//			echo 'Event ' . $eventDesc . ' was successfully deleted. <br><br>';
	//		} catch (Exception $e) {
	//			echo "An error occurred: " . $e->getMessage()  . '<br><br>';
	//		} 
	//	}
	//}

	wp_reset_postdata();
	echo "Synchronization completed!";
	
} else if (isset($_GET['code'])) { // Check if redirected after authorization
	require_once get_home_path() . 'vendor/autoload.php';
	$client = new Google_Client();
	$client->setClientId('1080532167388-evot5avjms5ld5qcq1br1gdak7s6v380.apps.googleusercontent.com');
	$client->setClientSecret('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');
	$client->setRedirectUri('https://thedogjedi.com/sync-to-google-calendar/'); 
	$client->addScope(Google_Service_Calendar::CALENDAR);
	// ... Obtain tokens and store them securely
	$client->authenticate($_GET['code']);
	$accessToken = $client->getAccessToken();
	$client->setAccessToken($accessToken);
	$_SESSION['access_token'] = $accessToken;
	//	$_SESSION['refresh_token'] = $refreshToken;
	echo 'Setting session tokens...';
	if (isset($_SESSION['access_token']) /* && isset($_SESSION['refresh_token'] ) */ ) {
	//	$currentTime = time(); 
	//	if ($tokenExpiryTimestamp <= $currentTime) {
	//    // Token expired - Refresh
	//    $newTokens = $client->refreshToken($refreshToken);
	//    $newTokens['access_token'] = $_SESSION['access_token'];
	//    // You might also want to update your $tokenExpiryTimestamp if you're storing it
	//	}
		echo 'Access token' . /* and refresh token */ ' exist(s) <br><br>';

		$google_service = new Google_Service_Calendar($client);

		// Get event data from your PHP calendar
		$booking_dates = array();
		$args = array(
			'post_type' => 'booking',
			'posts_per_page' => -1,
		);
		$bookings = new WP_Query($args);
		$booking_ids = array();
		$events = array();
		if($bookings->have_posts()) {
			while($bookings->have_posts()) {
				$bookings->the_post();
				$booking_ids[] = get_the_ID();
				$service = get_field('service');
				$dogs = get_field('dogs_involved');
				$start_date = get_field('booking_start_date');
				$end_date = get_field('booking_end_date');
				if(!$end_date) {
					$end_date = $start_date;
				}
				$dropoff_time = date('H:i:s', strtotime(get_field('drop-off_time')));
				$pickup_time = date('H:i:s', strtotime(get_field('pick-up_time')));
				if(!$dropoff_time || $dropoff_time == '00:00:00') {
					$dropoff_time = '08:00:00';
				}
				if(!$pickup_time || $pickup_time == '00:00:00') {
					$pickup_time = '08:00:00';
				}
				$user_bookings = array();
				$user_bookings['service'] = $service;
				$dog_names = array();
				$dog_links = array();
				foreach($dogs as $dog_id) {
					$dog_names[] = get_the_title($dog_id);
					$dog_links[] = get_the_permalink($dog_id);
				}
				$user_bookings['dogs'] = implode(', ', $dog_names);
				$user_bookings['dog_links'] = ($dog_links);
				$user_bookings['start_date'] = $start_date;
				$user_bookings['end_date'] = $end_date;
				$user_bookings['dropoff_time'] = $dropoff_time;
				$user_bookings['pickup_time'] = $pickup_time;
				$user_bookings['color'] = $color;
				$booking_dates[] = $user_bookings;

				$eventTitle = $user_bookings['service'] . ' for ' . $user_bookings['dogs'];
				$eventDescription = 'tdj-booking-'.get_the_ID();
				$eventStart = $user_bookings['start_date'] . 'T' . $user_bookings['dropoff_time'] . '-05:00'; // Timezone example
				$eventEnd = $user_bookings['end_date'] . 'T' . $user_bookings['pickup_time'] . '-05:00';

				// Create Google Calendar Event object
				$events[] = new Google_Service_Calendar_Event([
						'summary' => $eventTitle,
						'description' => $eventDescription,
						'start' => ['dateTime' => $eventStart],
						'end' => ['dateTime' => $eventEnd],
				]);		
			}
		}

		// Query for existing Google Calendar Events (this will be paginated by 20)
		$search_results = $google_service->events->listEvents('troy@dogghouseinteractive.com');

		// Build an array that includes all pages of results 
		$allEvents = $search_results->getItems(); // Store initial results
		while($nextPageToken = $search_results->getNextPageToken()) {
			$search_results = $google_service->events->listEvents('troy@dogghouseinteractive.com', [
					'pageToken' => $nextPageToken
			]);
			$allEvents = array_merge($allEvents, $search_results->getItems()); // Append new results
		}

		// Store the original TDJ Post IDs (stored in the event's description) so that they can be checked against later
		$tdj_bookings = array();
		foreach($allEvents as $google_cal_event) {
			if(str_starts_with($google_cal_event->description, 'tdj-booking-')) {
				$tdj_bookings[] = $google_cal_event->description;
			}
		}

		// Create an array of events from the Google Calendar which no longer exist in TDJ
		$tdj_deleted_events = array();
		foreach($tdj_bookings as $tdj_booking) {
			$formatted_id = str_replace('tdj-booking-', '', $tdj_booking);
			if(!in_array($formatted_id, $booking_ids)) {
				$tdj_deleted_events[] = 'tdj-booking-' . $formatted_id;
			}
		}

		// Update existing TDJ events in Google Calendar
		foreach($allEvents as $gcal_event) {
			if(in_array($gcal_event->description, $tdj_bookings)) {
				// event is already in Google Calendar, check if title needs updating
				$booking_id = str_replace('tdj-booking-', '', $gcal_event->description);
				$dogs = get_field('dogs_involved', $booking_id);
				$dog_names = array();
				foreach($dogs as $dog) {
					$dog_names[] = get_the_title($dog);
				}
				$new_title = implode(', ', $dog_names);

				// Check if title needs updating
				if($gcal_event->summary != $new_title) {
					try {
						$gcal_event->setSummary($new_title);
						$updatedEvent = $google_service->events->update('troy@dogghouseinteractive.com', $gcal_event->getId(), $gcal_event);
						echo 'Event title updated to "' . $new_title . '" for booking ' . $gcal_event->description . '<br><br>';
					} catch (Exception $e) {
						echo "When attempting to update event title, an error occurred: " . $e->getMessage() . '<br><br>';
					}
				}
			}
		}

		// Add new events from TDJ to Google Calendar
		foreach($events as $new_event) {
			if(!in_array($new_event->description, $tdj_bookings)) {
				// Create a new Google Calendar Event
				try {
					$reminderOverrides = [
							[
									'method' => 'popup',   // Reminder delivery method (email, popup, etc.)
									'minutes' => 1440        // Minutes before the event starts
							],
							// You can add more overrides if needed with different methods and minutes
					];
					$reminders = new Google_Service_Calendar_EventReminders();
					$reminders->setUseDefault(false);
					$reminders->setOverrides($reminderOverrides);
					$new_event->setReminders($reminders);
					$createdEvent = $google_service->events->insert('troy@dogghouseinteractive.com', $new_event);
					echo "Event Created! ID: " . $createdEvent->getId() . '<br><br>';
				} catch (Exception $e) {
					echo "When attemping to add a new event, an error occurred: " . $e->getMessage()  . '<br><br>';
				}
			}
		}

		// To Delete Events from Google Calendar that no longer exist in TDJ
		foreach($allEvents as $event) {
			$eventDesc = $event->description;
			$eventId = $event->getID();
			if(in_array($event->description, $tdj_deleted_events)) {
				try {
					$google_service->events->delete('troy@dogghouseinteractive.com', $eventId);
					echo 'Event ' . $eventDesc . ' was successfully deleted. <br><br>';
				} catch (Exception $e) {
					echo "When attempting to delete an event, an error occurred: " . $e->getMessage()  . '<br><br>';
				} 
			}
		}

		// To Delete ALL TDJ Events from Google Calendar 
		//foreach($allEvents as $event) {
		//	$eventDesc = $event->description;
		//	$eventId = $event->getID();
		//	if(str_starts_with($event->description, 'tdj-booking-')) {
		//		try {
		//			$google_service->events->delete('troy@dogghouseinteractive.com', $eventId);
		//			echo 'Event ' . $eventDesc . ' was successfully deleted. <br><br>';
		//		} catch (Exception $e) {
		//			echo "An error occurred: " . $e->getMessage()  . '<br><br>';
		//		} 
		//	}
		//}

		wp_reset_postdata();
		echo "Synchronization completed!<br><br>";
	}
} else {
    // ... Initiate OAuth flow, as tokens are not present
	
	require_once get_home_path() . 'vendor/autoload.php';
	$client = new Google_Client();
	$client->setClientId('1080532167388-evot5avjms5ld5qcq1br1gdak7s6v380.apps.googleusercontent.com');
	$client->setClientSecret('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');
	$client->setRedirectUri('https://thedogjedi.com/sync-to-google-calendar/'); 
	$client->addScope(Google_Service_Calendar::CALENDAR);
	$auth_url = $client->createAuthUrl();
	wp_redirect($auth_url);
	echo 'Authenticating...<br><br>';
}

//function check_for_changes() {
//	foreach($tdj_bookings as $tdj_booking) {			
//		$booking_id = str_replace('tdj-booking-', '', $tdj_booking);
//		$dogs = get_field('dogs_involved', $booking_id);
//		$dog_names = array();
//		foreach($dogs as $dog) { 
//			$dog_names[] = get_the_title($dog);
//		}
//		$booking_title = get_field('service', $booking_id) . ' for ' . implode(', ', $dog_names);
//		$start_date = get_field('start_date', $booking_id);
//		$start_time = get_field('dropoff_time', $booking_id);
//		$end_date = get_field('end_date', $booking_id);
//		$end_time = get_field('pickup_time', $booking_id);
//		// Set which TDJ Booking to be compared to the current Event
//		if($tdj_booking == 'tdj-booking-' . $booking_id) {
//			// Check for a change in the title (which would be caused by an update in the dogs involved, or the type of service booked)
//			if($booking_title != $event->summary) {
//				$event->setSummary($booking_title);
//			} 
//			// Check for a change in start date/time
//			if($start_date . 'T' . $start_time . '-05:00' != $event->start->dateTime) {
//				$startDateTime = new Google_Service_Calendar_EventDateTime();
//				$newStartTimeString = $start_date . 'T' . $start_time . '-05:00';
//				$newStartTime = new DateTime($newStartTimeString);
//				$startDateTime->setDateTime($newStartTime);
//				$event->setStart($startDateTime);
//			}
//			// Check for a change in end date/time
//			if($end_date . 'T' . $end_time . '-05:00' != $event->end->dateTime) {
//				$endDateTime = new Google_Service_Calendar_EventDateTime();
//				$newEndTimeString = $end_date . 'T' . $end_time . '-05:00';
//				$newEndTime = new DateTime($newEndTimeString);
//				$endDateTime->setDateTime($newEndTime);
//				$event->setEnd($endDateTime);
//			}
//			if($booking_title != $event->summary || $start_date . 'T' . $start_time . '-05:00' != $event->start->dateTime || $end_date . 'T' . $end_time . '-05:00' != $event->end->dateTime) {
//				// Send the update request
//				try {
//					$updatedEvent = $google_service->events->update('troy@dogghouseinteractive.com', $eventId, $event);
//					echo 'Event ' . $eventDesc . ' was successfully updated. <br><br>';
//				} catch (Exception $e) {
//					echo "An error occurred: " . $e->getMessage()  . '<br><br>';
//				} 
//			}
//		}	
//	}
//}
