<?php
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
// add event
$calendar->add_event(1, 'Holiday', 'Going away for a while.', '2021-10-11 13:00:00', '2021-10-15 18:00:00', '#53ae6d');
$calendar->add_event(2, 'Interview', 'Important meeting with work.', '2021-10-06 09:00:00', '2021-10-06 11:00:00', '#ae5353');
// delete event
// $calendar->delete_event(1);
// update event
// $calendar->update_event(2, 'Interview Updated', 'Important meeting with work.', '2021-10-07 09:00:00', '2021-10-07 11:00:00', '#ae5353');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Event Calendar</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link href="calendar.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	    <nav class="navtop">
	    	<div>
	    		<h1>Event Calendar</h1>
	    	</div>
	    </nav>
		<div class="content">
			<div class="calendar-container">
                <?=$calendar?>
            </div>
		</div>
		<script src="Calendar.js?v=1.1.0"></script>
		<script>
		new Calendar();
		</script>
	</body>
</html>