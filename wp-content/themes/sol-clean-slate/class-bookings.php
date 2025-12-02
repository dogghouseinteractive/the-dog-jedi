<?php

class Bookings {
  public $client;
  public $google;
  private $calendarID = 'troy@dogghouseinteractive.com';

  function __construct() {
    require_once get_home_path() . 'vendor/autoload.php';
    putenv("GOOGLE_APPLICATION_CREDENTIALS=/nas/content/live/thedogjedi/the-dog-jedi-calendar-9eed48d036d3.json");
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope(Google_Service_Calendar::CALENDAR);
    $client->addScope('https://www.googleapis.com/auth/reminders');
    $this->client = $client;

    $google_service = new Google_Service_Calendar($client);
    $this->google = $google_service;
  }

  public function getGoogleEvents() {
    // https://developers.google.com/google-apps/calendar/v3/reference/events/list
    $options = array(
      'maxResults' => 2500, // 2500 is the absolute maximum per-page
      'orderBy' => 'startTime',
      'singleEvents' => TRUE,
    );
    $results = $this->google->events->listEvents( $this->calendarID, $options);
    $tdj_events = [];
    foreach( $results as $event ) {
      if ( $event->description && str_contains( $event->description, 'tdj-booking-' )) {
        $tdj_events[$event->description] = $event;
      }
    }
    return $tdj_events;
  }

  /*
  public function getPagedGoogleEvents() {
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
  }
  */

  public function getGoogleEvent( $eventID ) {
    return $this->google->events->get( $this->calendarID, $eventID );
  }

  // Untested
  public function deleteGoogleEvent( $eventID ) {
    try {
      $this->google->events->delete( $this->calendarID, $eventID );
      return true;
    } 
    catch (Exception $e) {
      echo "An error occurred: " . $e->getMessage()  . '<br><br>';
    } 
  }

  // Untested
  // EXCLUSIVELY deletes tdj-booking- events.
  public function deleteAllGoogleEvents() {
    $events = $this->getGoogleEvents();
    foreach( $events as $eventID => $event ) {
      $this->deleteGoogleEvent( $eventID );
    }
  }

  // If the specified $post_id is for a Booking post that is set published, adds it to the 
  // Google calendar.
  public function addGoogleEvent( $post_id ) {
    $events = $this->getGoogleEvents();
    if( empty( $events['tdj-booking-'.$post_id] ) ) {
      if ( get_post_type( $post_id ) !== 'booking' ) return false;
      if ( get_post_status( $post_id ) !== 'publish' ) return false;
      
      $service = get_field( 'service', $post_id );
      $color = '';
      if ( $service == 'Dog Boarding') { $color = '#DE9611'; } else { $color = '#524131'; }
      $dogs = get_field( 'dogs_involved', $post_id );
      $start_date = get_field('booking_start_date', $post_id );
      $end_date = get_field('booking_end_date', $post_id );
      if ( !$end_date ) { $end_date = $start_date; }
      $dropoff_time = date('H:i:s', strtotime(get_field('drop-off_time', $post_id )));
      $pickup_time = date('H:i:s', strtotime(get_field('pick-up_time', $post_id )));
      if(!$dropoff_time || $dropoff_time == '00:00:00') { $dropoff_time = '08:00:00'; }
      if(!$pickup_time || $pickup_time == '00:00:00') { $pickup_time = '08:00:00'; }
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
      $user_bookings['end_date'] = $end_date ?: $start_date;
      $user_bookings['dropoff_time'] = $dropoff_time;
      $user_bookings['pickup_time'] = $pickup_time;
      $user_bookings['color'] = $color;
      $booking_dates[] = $user_bookings;

      $eventTitle = $user_bookings['service'] . ' for ' . $user_bookings['dogs'];
      $eventDescription = 'tdj-booking-'.$post_id;
      $eventStart = $user_bookings['start_date'] . 'T' . $user_bookings['dropoff_time'] . '-04:00'; // Timezone example
      $eventEnd = $user_bookings['end_date'] . 'T' . $user_bookings['pickup_time'] . '-04:00';

      // Create Google Calendar Event object
      $event = new Google_Service_Calendar_Event([
        'summary' => $eventTitle,
        'description' => $eventDescription,
        'start' => ['dateTime' => $eventStart],
        'end' => ['dateTime' => $eventEnd],
      ]);		
      $reminderOverrides = [ [ 'method' => 'popup', 'minutes' => 1440 ], ];
      $reminders = new Google_Service_Calendar_EventReminders();
      $reminders->setUseDefault('false');
      $reminders->setOverrides($reminderOverrides); 
      $event->setReminders($reminders);

      try {
        $createdEvent = $this->google->events->insert('troy@dogghouseinteractive.com', $event);
        echo "Event Created! ID: " . $createdEvent->getId() . '<br><br>';
      } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage()  . '<br><br>';
      }
    } 
    else echo "Event $post_id already exists in Google calendar: {$events['tdj-booking-'.$post_id]->getId()}.";
  }

  // Untested
  // To Add New Events from TDJ to Google Calendar 
  public function addAllGoogleEvents() {
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
  }

  // Rough output of queried TDJ Google event IDs, start times, and descriptions.
  public function displayGoogleEvents() {
    ?>
      <h2>Google Calendar</h2>
      <ul>
        <?php foreach( $this->getGoogleEvents() as $item ): ?>
        <li><?php echo $item->getId(); ?>: <?php echo $item->getStart()->getDateTime(); ?> - <?php echo $item->getDescription(); ?></li>
        <?php endforeach; ?>
      </ul>
    <?php 
  }

  public function displayLocalBookings() {
    $google_events = $this->getGoogleEvents();
    ?>
      <h2>Local Bookings</h2>
      <ul>
        <?php foreach( $this->getAllBookings() as $booking ): ?>
          <li>
            <?php $booking_id = 'tdj-booking-' . $booking->ID; 
              echo $booking->ID; ?> - <?php echo $booking->post_status; ?> - <?php echo $booking->post_title; 
              $event = empty( $google_events[$booking_id] ) ? false : $google_events[$booking_id];
              if ( $event )
                echo ' (' . $event->getId() . ': ' . $event->getStart()->getDateTime() . ')';
              else echo ' (<strong>no event in calendar</strong>)';
            ?>
          </li>
        <?php endforeach; ?>          
      </ul>
    <?php
  }

  public function getAllBookings() {
    $args = array(
      'post_type' => 'booking',
      'posts_per_page' => -1,
      'post_status' => 'all',
    );
    $bookings = new WP_Query( $args );
    return $bookings->posts;
  }

  // Create an array of events from the Google Calendar which no longer exist in TDJ
  public function getDeletedEvents() {
    $tdj_deleted_events = array();
    foreach($tdj_bookings as $tdj_booking) {
      $formatted_id = str_replace('tdj-booking-', '', $tdj_booking);
      if(!in_array($formatted_id, $booking_ids)) {
        $tdj_deleted_events[] = 'tdj-booking-' . $formatted_id;
      }
    }
    return $tdj_deleted_events;
  }

  // Based on https://github.com/Jinjinov/google-reminders-php/
  public function displayReminders() {
    $httpClient = $this->client->authorize();
    $reminders = list_reminders($httpClient, 10);

    foreach($reminders as $reminder) {
      echo '<p>';
      echo $reminder;
      echo '</p>';
    }
      /*
    }
    else {
      $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
      header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
    }
       */
  }

}

class GoogleReminder {
  function __construct($id,$title,$dt,$creation_timestamp_msec = null,$done = false) {
    if ($id == null) {
      throw new Exception('Reminder id must not be None');
    }
    $this->id = $id;
    $this->title = $title;
    $this->dt = $dt;
    $this->creation_timestamp_msec = $creation_timestamp_msec;
    $this->done = $done;
  }

  public function __toString()
  {
    if($this->done)
    {
      return "{$this->dt->format("Y.m.d")} {$this->title} [Done]";
    }
    else
    {
      return "{$this->dt->format("Y.m.d")} {$this->title}";
    }
  }
}

// https://github.com/googleapis/google-api-php-client
// https://developers.google.com/identity/protocols/OAuth2WebServer
function create_reminder_request_body($reminder) {
  $body = (object)[
    '2' => (object)[
      '1' => 7
    ],
    '3' => (object)[
      '2' => $reminder->id
    ],
    '4' => (object)[
      '1' => (object)[
        '2' => $reminder->id
      ],
      '3' => $reminder->title,
      '5' => (object)[
        '1' => $reminder->dt->year,
        '2' => $reminder->dt->month,
        '3' => $reminder->dt->day,
        '4' => (object)[
          '1' => $reminder->dt->hour,
          '2' => $reminder->dt->minute,
          '3' => $reminder->dt->second,
        ]
      ],
      '8' => 0
    ]
  ];

  return json_encode($body);
}

function get_reminder_request_body($reminder_id) {
  $body = (object)['2' => [(object)['2' => $reminder_id]]];
  return json_encode($body);
}

function delete_reminder_request_body($reminder_id) {
  $body = (object)['2' => [(object)['2' => $reminder_id]]];
  return json_encode($body);
}

// The body corresponds to a request that retrieves a maximum of num_reminders reminders, 
// whose creation timestamp is less than max_timestamp_msec.
// max_timestamp_msec is a unix timestamp in milliseconds. 
// if its value is 0, treat it as current time.
function list_reminder_request_body($num_reminders, $max_timestamp_msec = 0) {
  $body = (object)[
    '5' => 1,  // boolean field: 0 or 1. 0 doesn't work ¯\_(ツ)_/¯
    '6' => $num_reminders,  // number of reminders to retrieve
  ];

  if ($max_timestamp_msec) {
    $max_timestamp_msec += (int)(15 * 3600 * 1000);
    $body['16'] = $max_timestamp_msec;
    // Empirically, when requesting with a certain timestamp, reminders with the given timestamp 
    // or even a bit smaller timestamp are not returned. 
    // Therefore we increase the timestamp by 15 hours, which seems to solve this...  ~~voodoo~~
    // (I wish Google had a normal API for reminders)
  }

  return json_encode($body);
}

function build_reminder($reminder_dict) {
  $r = $reminder_dict;

  try {
    $id = $r['1']['2'];
    $title = $r['3'];

    $year = $r['5']['1'];
    $month = $r['5']['2'];
    $day = $r['5']['3'];

    $date_time = new DateTime();
    $date_time->setDate($year, $month, $day);

    if(array_key_exists('4', $r['5']))
    {
      $hour = $r['5']['4']['1'];
      $minute = $r['5']['4']['2'];
      $second = $r['5']['4']['3'];

      $date_time->setTime($hour, $minute, $second);
    }

    $creation_timestamp_msec = (int)($r['18']);
    $done = array_key_exists('8', $r) && $r['8'] == 1;

    return new Reminder(
      $id,
      $title,
      $date_time,
      $creation_timestamp_msec,
      $done
    );
  }
  catch (Exception $KeyError) {
    echo('build_reminder failed: unrecognized reminder dictionary format');

    return null;
  }
}
    
// Send a 'create reminder' request.  Returns boolean.
function create_reminder($httpClient, $reminder) {
  $response = $httpClient->request(
    'POST',
    'https://reminders-pa.clients6.google.com/v1internalOP/reminders/create',
    [
      'headers' => [ 'content-type' => 'application/json+protobuf' ],
      'body' => create_reminder_request_body($reminder),
    ]
  );

  if ($response->getStatusCode() == 200) {
    $content = $response->getBody();
    return true;
  }
  else {
    return false;
  }
}

// Retrieve information about the reminder with the given id. None if an error occurred
function get_reminder($httpClient, $reminder_id) {
  $response = $httpClient->request(
    'POST',
    'https://reminders-pa.clients6.google.com/v1internalOP/reminders/get',
    [
      'headers' => [ 'content-type' => 'application/json+protobuf' ],
      'body' => get_reminder_request_body($reminder_id)
    ]
  );

  if ($response->getStatusCode() == 200) {

    $content = $response->getBody();
    $content_dict = json_decode($content, true);

    if (!isset($content_dict) || empty($content_dict)) {
      echo("Couldn\'t find reminder with id=${reminder_id}");
      return null;
    }

    $reminder_dict = $content_dict['1'][0];

    return build_reminder($reminder_dict);
  }
  else {
    return null;
  }
}

// Delete the reminder with the given id.  Returns boolean.
function delete_reminder($httpClient, $reminder_id) {
  $response = $httpClient->request(
    'POST',
    'https://reminders-pa.clients6.google.com/v1internalOP/reminders/delete',
    [
      'headers' => [ 'content-type' => 'application/json+protobuf' ],
      'body' => delete_reminder_request_body($reminder_id)
    ]
  );

  if ($response->getStatusCode() == 200) {
    $content = $response->getBody();
    return true;
  }
  else {
    return false;
  }
}

// List last num_reminders created reminders, or None if an error occurred
function list_reminders($httpClient, $num_reminders) {
  $response = $httpClient->request(
    'POST',
    'https://reminders-pa.clients6.google.com/v1internalOP/reminders/list',
    [
      'headers' => [ 'content-type' => 'application/json+protobuf' ],
      'body' => list_reminder_request_body($num_reminders)
    ]
  );

  if ($response->getStatusCode() == 200) {

    echo "Status code 200";
    $content = $response->getBody();
    print_r( $content );
    $content_dict = json_decode($content, true);
    print_r( $content_dict );

    if (!array_key_exists('1', $content_dict)) {
      return [];
    }

    $reminders_dict_list = $content_dict['1'];
    $reminders = [];

    foreach($reminders_dict_list as $reminder_dict) {
      array_push($reminders, build_reminder($reminder_dict));
    }

    return $reminders;
  }
  else {
    print_r( $response );
    echo 'Response failure, status code: ' . $response->getStatusCode();
    return null;
  }
}

// Shortcode to kick out testing results without having to modify a template page
add_shortcode( 'tdj_test', 'tdj_testing_shortcode' );
function tdj_testing_shortcode() {
  $test_event = '6hh8im8o5e6o5heohs2qjflteo';
  $test_event = '6nooduagvlq43qqistu2g5t5c0';
  $bookings = new Bookings;
  $bookings->displayLocalBookings();
  echo '<pre>';
  print_r( $bookings->getGoogleEvent( $test_event ) );
  $reminders = $bookings->getGoogleEvent( $test_event )->reminders;
  print_r( $reminders );
  print_r( $reminders->useDefault ? 'true' : 'false' );
  echo '</pre>';
  // $bookings->displayReminders();
}

