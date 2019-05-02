<?php
/*
view.php (person version)
View a person. Very simple file, as all the HTML is stored in the objects as public functions
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/person.php';

// If there's a person ID, then declare a person object and output the view HTML
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$person = new person($db, $id);
	$person->view();
	$person->outputHTML();
}

// Send to "View All" if no person found
else {
	echo '<script type="text/javascript">';
	echo 'alert("No person selected!  Taking you to the list of all people to pick from.");';
	echo 'window.location.href = "viewall.php";';
	echo '</script>';
	die('Forwarding...');
}
?>