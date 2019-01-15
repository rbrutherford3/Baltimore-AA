<?php
/*
view.php (institution version)
View an institution. Very simple file, as all the HTML is stored in the objects as public functions
*/

include_once '../lib/dbconnect.php';
include_once '../lib/institution.php';

// If there's an institution ID, then declare an institution object and output the view HTML
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$institution = new institution($db, $id);
	$institution->view();
	$institution->outputHTML();
}

// Send to "View All" if no institution found
else {
	echo '<script type="text/javascript">';
	echo 'alert("No institution selected!  Taking you to the list of all institutions to pick from.");';
	echo 'window.location.href = "viewall.php";';
	echo '</script>';
	die('Forwarding...');
}
?>