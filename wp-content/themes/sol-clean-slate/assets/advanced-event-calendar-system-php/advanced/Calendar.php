<?php
// Include the configuration file
include 'config.php';
// Include the calendar class
include 'Calendar.class.php';
// Get the current date (if specified); default is null
$current_date = isset($_GET['current_date']) ? $_GET['current_date'] : null;
// Get the unique id (if specified); default is 0
$uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
// Alternative to the above, but using sessions instead
// $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
// Get the size (if specified); default is normal
$size = isset($_GET['size']) ? $_GET['size'] : 'normal';
// Create a new calendar instance
$calendar = new Calendar($current_date, $uid, $size);
// Connect to the calendar database using the constants declared in the config.php file
$calendar->connect_to_database(db_host, db_user, db_pass, db_name);
// Check if the add/update event form was submitted 
if (isset($_POST['title'], $_POST['description'], $_POST['startdate'], $_POST['enddate'], $_POST['color'])) {
    // Title cannot be empty
    if (empty($_POST['title'])) {
        exit('Please enter the event title!');
    }
    // Validate the color
    $color = ctype_xdigit(ltrim($_POST['color'], '#')) && strlen(ltrim($_POST['color'], '#')) == 6 ? $_POST['color'] : '#5373ae';
    // If the event ID exists, update the corresponding event otherwise add a new event 
    if (empty($_POST['eventid'])) {
        // Add new event
        $calendar->add_event($_POST['title'], $_POST['description'], $_POST['startdate'], $_POST['enddate'], $color);
    } else {
        // Update existing event
        $calendar->update_event($_POST['eventid'], $_POST['title'], $_POST['description'], $_POST['startdate'], $_POST['enddate'], $color);
    }
    exit('success');
}
// Delete event
if (isset($_GET['delete_event'])) {
    $calendar->delete_event($_GET['delete_event']);
    exit;
}
// Retrieve events list in HTML format
if (isset($_GET['events_list'])) {
    $events_list = $calendar->list_events_by_date_html($_GET['events_list']);
    if ($events_list) {
        echo $events_list;
    } else {
        echo '<div class="events">There are no events.</div>';
    }
    exit;
}
// Display the calendar
echo $calendar;
?>