<?php
/*
view.php (meeting version)
View a meeting. Very simple file, as all the HTMl is stored in the objects as public functions
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/meeting.php';

// If there's a meeting ID, then declare a meeting object and output the view HTML
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$meeting = new meeting($db, $id);
	$meeting->view();
	$meeting->sponsor->view();
	$meeting->institution->view();
	$meeting->cosponsor->view();
	$meeting->cosponsor2->view();
	$meeting->outputHTML();
}

// Send to "View All" if no meeting found
else {
	echo '<script type="text/javascript">';
	echo 'alert("No meeting selected!  Taking you to the list of all meetings to pick from.");';
	echo 'window.location.href = "viewall.php";';
	echo '</script>';
	die('Forwarding...');
}
?>