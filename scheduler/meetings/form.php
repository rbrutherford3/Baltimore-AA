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
include_once '../../lib/recaptcha.php';

$db = database::connect();

// Form string variables (employing array method for form elements)
$formName = 'meetingform';
$meetingBase = 'meeting';
$institutionBase = 'institution';
$sponsorBase = 'spons';
$cosponsorBase = 'cospons';
$cosponsor2Base = 'cospons2';

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
	<link href="' . $libloc . 'bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="' . $libloc . 'main.css"> 
	<script type="text/javascript" src="' . $libloc . 'personbuttons.js"></script>
	<script type="text/javascript" src="' . $libloc . 'institutionbuttons.js"></script>
	<script type="text/javascript" src="' . $libloc . 'secondbuttons.js"></script>
	<script type="text/javascript" src="' . $libloc . 'validate.js"></script>
	<script type="text/javascript" src="validate.js"></script>';
echo recaptcha::javascript();
echo '

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

// Check for the existence of co-sponsor and second co-sponsor
$cosponsorExists = !is_null($meeting->cosponsor->getID());
$cosponsor2Exists = !is_null($meeting->cosponsor2->getID());

// Determine div and button visibility states of each based on their existence
if(!$cosponsorExists) {
	$toggleState1 = 1;
	$toggleState2 = 0;
}
elseif($cosponsorExists && !$cosponsor2Exists) {
	$toggleState1 = 2;
	$toggleState2 = 1;
}
elseif($cosponsorExists && $cosponsor2Exists) {
	$toggleState1 = 3;
	$toggleState2 = 2;
}

// Draw divs using bootstrap columns and rows as containers
echo '
	<div class="row">
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">
			<p>
				<a class="button" href = "../" style="margin-top: 10px;">Home</a>
				<a class="button" href = "viewall.php">Meetings</a>
			</p>
		</div>
		<div class="col-lg-9 col-md-8" style="text-align: center;">
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
			$meeting->inputHTML($meetingBase, $meetingBase, true);
echo '
		</div>
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
			$meeting->institution->inputHTML('institution', 'institution', true);
echo '
		</div>
		<div class="col-lg-6 col-md-4" style="text-align: center;">
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
			$meeting->sponsor->inputHTML($sponsorBase, $sponsorBase, true, 'Sponsor');
echo '
		</div>
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
			$meeting->cosponsor->inputHTML($cosponsorBase, $cosponsorBase, $cosponsorExists, 'Co-Sponsor');
echo '
		</div>
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
			$meeting->cosponsor2->inputHTML($cosponsor2Base, $cosponsor2Base, $cosponsor2Exists, 'Co-Sponsor #2');
echo '
		</div>
		<div class="col-lg-3 col-md-4" style="text-align: center;">
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">
			<p>';
echo recaptcha::submitbutton('meetingsubmit', 'Save', 'submit', false, false);
echo '
			</p>
		</div>
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">
			<p>';
				$meeting->cosponsor->toggleButton($cosponsorBase, $cosponsorBase, $toggleState1, 'cosponsor');
echo '
			</p>
		</div>
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">
			<p>';
				$meeting->cosponsor2->toggleButton($cosponsor2Base, $cosponsor2Base, $toggleState2, 'second cosponsor');
echo '
			</p>
		</div>
		<div class="col-lg-3" style="text-align: center;">
		</div>
	</div>
</div>
<script>
secondbuttons("' . $cosponsorBase . '", "' . $cosponsor2Base. '");
</script>
</form>
</body>
</html>';

?>
