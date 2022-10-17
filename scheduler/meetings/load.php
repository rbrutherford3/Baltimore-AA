<?php
/*
load.php (meeting version)
Form processing, updating database, and forwarding to view page
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/person.php';
include_once '../../lib/meeting.php';
include_once '../../lib/loadperson.php';
include_once '../../lib/loadinstitution.php';
include_once '../../lib/recaptcha.php';

$db = database::connect();

recaptcha::verify(false);

// Define form element root names
$meetingBase = 'meeting';
$institutionBase = 'institution';
$sponsorBase = 'spons';
$cosponsorBase = 'cospons';
$cosponsor2Base = 'cospons2';

// Get ID based on input method for institution
$institutionID = loadInstitution($db, $institutionBase);

// Get ID based on input method for sponsor and cosponsor
$sponsorID = loadPerson($db, $sponsorBase);

// Do the same for cosponsor if it exists
if ($_POST[$cosponsorBase]['exists']) {
	$cosponsorID = loadPerson($db, $cosponsorBase);
}
else {
	$cosponsorID = null;
}

// Do the same for second cosponsor if it exists
if ($_POST[$cosponsor2Base]['exists']) {
	$cosponsor2ID = loadPerson($db, $cosponsor2Base);
}
else {
	$cosponsor2ID = null;
}

// Compile meeting object
if (isset($_POST[$meetingBase]['id'])) {	// Update db if editing

	// Initiate meeting object (with ID)
	$meeting = new meeting($db, $_POST[$meetingBase]['displayID'], $institutionID, $_POST[$meetingBase]['dow'], 
		$_POST[$meetingBase]['hour'], $_POST[$meetingBase]['minute'], $_POST[$meetingBase]['ampm'], 
		$_POST[$meetingBase]['gender'], $sponsorID, $cosponsorID, $cosponsor2ID, 
		$_POST[$meetingBase]['notesPublic'], $_POST[$meetingBase]['notes'], isset($_POST[$meetingBase]['active']), 
		$_POST[$meetingBase]['id']);

	// Check to see that information isn't conflicting with another record
	if ($meeting->checkExistsExcluding()) {
		echo '
			<script>
			alert("Meeting ' . $meeting->getDisplayID()->getFormatted() . ' already exists in a separate entry.  ' . 
			'Forwarding you to their page.");
			window.location = "form.php?id=' . $meeting->getID() . '";
			</script>';
	}

	// Update otherwise
	else {
		$meeting->update();	// (add handling for false results?)
	}
}
else { // Insert into db if adding

	// Initiate meeting object (without ID)
	$meeting = new meeting($db, $_POST[$meetingBase]['displayID'], $institutionID, $_POST[$meetingBase]['dow'], 
		$_POST[$meetingBase]['hour'], $_POST[$meetingBase]['minute'], $_POST[$meetingBase]['ampm'], 
		$_POST[$meetingBase]['gender'], $sponsorID, $cosponsorID, $cosponsor2ID, 
		$_POST[$meetingBase]['notesPublic'], $_POST[$meetingBase]['notes'], isset($_POST[$meetingBase]['active']));

	// Check to see that information isn't already in db
	if ($meeting->checkExists()) {
		echo '
			<script>alert("Meeting ' . $meeting->getDisplayID()->getFormatted() . ' already exists in a separate entry.  ' . 
			'Forwarding you to their page.");
			window.location = "form.php?id=' . $meeting->getID() . '";
			</script>';
	}
	else {
		$meeting->insert();	// (add handling for false results?)
	}
}

// Forward to view page
echo '
	<script>
	window.location = "view.php?id=' . $meeting->getID() . '";
	</script>';


?>