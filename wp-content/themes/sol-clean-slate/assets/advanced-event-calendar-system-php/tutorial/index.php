<?php
include 'Calendar.php';
$calendar = new Calendar('2021-05-04');
$calendar->add_event('Shopping', '2021-05-03', 8, 'green');
$calendar->add_event('Concert', '2021-05-12', 2, 'blue');
$calendar->add_event('Hospital', '2021-05-23', 3, 'red');
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
		<div class="content home">
			<?=$calendar?>
		</div>
	</body>
</html>
