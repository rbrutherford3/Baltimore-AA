<?php
/*
form.php (group version)
This file creates a form for inputting (adding/editing) meeting information.  If an ID
argument is passed, then we're editing.  If not, we're adding.  Most of the HTML is in
the member functions meeting, institution, and person
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/meeting.php';
include_once '../../lib/header.php';


// Form string variables (employing array method for form elements)
$formName = 'form';
$meetingBase = 'meeting';
$institutionBase = 'institution';
$sponsorBase = 'sponsor';
$cosponsorBase = 'cosponsor';
$cosponsor2Base = 'cosponsor2';

// Edit meeting if ID provided
if(isset($_GET['id'])) {
  
	// Grab ID
	$meetingID = $_GET['id'];
	
	// Initiate meeting
	$meeting = new meeting($db, $meetingID);
	
	// Fill the information from the db
	$meeting->view();
	
	// Set title
	$title = 'Edit meeting';
}
// Adding meeting if no ID provided
else {

	$meetingID = null;
	
	$meeting = new meeting($db);
	
	$title = 'Add new meeting';
}

// Load javascript files, set title, initiate form (note that the validate form function
// accepts all the form element string bases).  Note that we need to reload the main stylesheet
// after loading bootstrap because bootstrap overrides a lot of styles.
echo '
	<link href="/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="/lib/main.css"> 
	<script type="text/javascript" src="/lib/personbuttons.js"></script>
	<script type="text/javascript" src="/lib/institutionbuttons.js"></script>
	<script type="text/javascript" src="/lib/secondbuttons.js"></script>
	<script type="text/javascript" src="/lib/validate.js"></script>
	<script type="text/javascript" src="validate.js"></script>

	<title>Institution Committee - ' . $title . '</title>
</head>
<body>
<div class="container-fluid">
<form name="' . $formName . '" action="load.php" onsubmit="return validateForm(\'' .  $formName . 
	'\', \'' . $institutionBase . '\', \'' . $sponsorBase . '\', \'' . 
	$cosponsorBase . '\', \'' . $cosponsor2Base . '\');" method="post">';

// Get information for institution and load all institutions from db for pulldown menu
$meeting->institution->view();
$meeting->institution->viewAll();

// Get information for sponsor and load all persons from db for pulldown menu
$meeting->sponsor->view();
$meeting->sponsor->viewAll();

// Get information for cosponsor and load all persons from db for pulldown menu
$meeting->cosponsor->view();
$meeting->cosponsor->viewAll();

// Get information for cosponsor 2 (if it exists) and load all persons from db for pulldown menu
$meeting->cosponsor2->view();
$meeting->cosponsor2->viewAll();

// Draw divs using bootstrap columns and rows as containers
echo '<div class="row">';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
echo '<p>';
echo '<a class="button" href = "../" style="margin-top: 10px;">Home</a>';
echo '<a class="button" href = "viewall.php">Meetings</a>';
echo '</p>';
echo '</div>';
echo '<div class="col-lg-9 col-md-8" style="text-align: center;">';	
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
$meeting->inputHTML($meetingBase, $meetingBase, true);
echo '</div>';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
$meeting->institution->inputHTML('institution', 'institution', true);
echo '</div>';
echo '<div class="col-lg-6 col-md-4" style="text-align: center;">';
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
$meeting->sponsor->inputHTML($sponsorBase, $sponsorBase, true, 'Sponsor');
echo '</div>';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
$meeting->cosponsor->inputHTML($cosponsorBase, $cosponsorBase, true, 'Co-Sponsor');
echo '</div>';
// Only show second cosponsor div if there is a cosponsor, otherwise hide.  Draw add/hide buttons appropriately.
if(is_null($meeting->cosponsor2->getID())) {
	echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
	$meeting->cosponsor2->inputHTML($cosponsor2Base, $cosponsor2Base, false, 'Co-Sponsor #2');
	echo '</div>';
	echo '<div class="col-lg-3 col-md-4" style="text-align: center;">';
	echo '</div>';
	echo '</div>';
	echo '<div class="row">';
	echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
	echo '<p>';
	echo '<input type="submit" value="Submit">';
	echo '</p>';
	echo '</div>';
	echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
	echo '<p>';
	$meeting->cosponsor->toggleButton($cosponsorBase, $cosponsorBase, true, 'cosponsor');
	echo '</p>';
	echo '</div>';
	echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
	echo '<p>';
	$meeting->cosponsor2->toggleButton($cosponsor2Base, $cosponsor2Base, false, 'second cosponsor');
	echo '</p>';
	echo '</div>';	
	echo '<div class="col-lg-3" style="text-align: center;">';	
	echo '</div>';
	echo '</div>';
}
else {
	echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
	$meeting->cosponsor2->inputHTML($cosponsor2Base, $cosponsor2Base, true, 'Co-Sponsor #2');
	echo '</div>';
	echo '<div class="col-lg-3 col-md-4" style="text-align: center;">';
	echo '</div>';
	echo '</div>';
	echo '<div class="row">';
	echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
	echo '<p>';
	echo '<input type="submit" value="Submit">';
	echo '</p>';
	echo '</div>';
	echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
	echo '<p>';
	$meeting->cosponsor->toggleButton($cosponsorBase, $cosponsorBase, true, 'cosponsor');
	echo '</p>';
	echo '</div>';
	echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
	echo '<p>';
	$meeting->cosponsor2->toggleButton($cosponsor2Base, $cosponsor2Base, true, 'second cosponsor');
	echo '</p>';
	echo '</div>';	
	echo '<div class="col-lg-3" style="text-align: center;">';	
	echo '</div>';
	echo '</div>';
}


echo '
</div>
</form>
</body>
</html>';

?>