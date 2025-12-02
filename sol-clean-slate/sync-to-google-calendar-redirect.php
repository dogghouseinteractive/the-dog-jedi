<?php 

/**
 * Template Name: Sync to Google Calendar Redirect
 */

require_once get_home_path() . 'vendor/autoload.php';
$client = new Google_Client();
$client->setClientId('1080532167388-evot5avjms5ld5qcq1br1gdak7s6v380.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://thedogjedi.com/sync-to-google-calendar-redirect/'); 
$client->addScope(Google_Service_Calendar::CALENDAR);

$client->authenticate($_GET['code']);
$accessToken = $client->getAccessToken();

$client->setAccessToken($accessToken);
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
		$color = '';
		if($service == 'Dog Boarding') {
			$color = '#DE9611';
		} else {
			$color = '#524131';
		}
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
$search_results = $google_service->events->listEvents('troy@dogghouseinteractive.com', [
	'pageToken' => $nextPageToken
]);

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

// To Add New Events from TDJ to Google Calendar 
foreach($events as $event) {
	if(in_array($event->description, $tdj_bookings)) {
		// do nothing, event is already in Google Calendar
	} else {
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
			$event->setReminders($reminders);
			$createdEvent = $google_service->events->insert('troy@dogghouseinteractive.com', $event);
			echo "Event Created! ID: " . $createdEvent->getId() . '<br><br>';
		} catch (Exception $e) {
			echo "An error occurred: " . $e->getMessage()  . '<br><br>';
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
			echo "An error occurred: " . $e->getMessage()  . '<br><br>';
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