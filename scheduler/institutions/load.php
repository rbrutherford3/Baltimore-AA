<?php
/*
load.php (institution version)
Form processing, updating database, and forwarding to view page
*/


// Include files
include_once '../../lib/dbconnect.php';
include_once '../../lib/institution.php';
include_once '../../lib/recaptcha.php';
// Define form element root names
$formName = 'form';
$institutionBase = 'institution';

recaptcha::verify(false);

// Compile institution object
if (isset($_POST[$institutionBase]['id'])) {	// Update db if editing

	// Initiate isntitution object (with ID)
	$institution = new institution($db, $_POST[$institutionBase]['name'], $_POST[$institutionBase]['address'], 
		$_POST[$institutionBase]['city'], $_POST[$institutionBase]['zip'], isset($_POST[$institutionBase]['bg']), 
		$_POST[$institutionBase]['notesPublic'], $_POST[$institutionBase]['notes'], isset($_POST[$institutionBase]['active']), $_POST[$institutionBase]['id']);

	// Check to see that information isn't conflicting with another record
	if ($institution->checkExistsExcluding()) {
		echo '
			<script>
			alert("' . $institution->getName()->getFormatted() . ' already exists in a separate entry.  ' . 
			'Forwarding you to their page.");
			window.location = "form.php?id=' . $institution->getID() . '";
			</script>';
	}

	// Update otherwise
	else {
		$institution->update();	// (add handling for false results?)
	}
}
else { // Insert into db if adding

	// Initiate institution object (without ID)
	$institution = new institution($db, $_POST[$institutionBase]['name'], $_POST[$institutionBase]['address'], 
		$_POST[$institutionBase]['city'], $_POST[$institutionBase]['zip'], isset($_POST[$institutionBase]['bg']), 
		$_POST[$institutionBase]['notesPublic'], $_POST[$institutionBase]['notes'], isset($_POST[$institutionBase]['active']));

	// Check to see that information isn't already in db
	if ($institution->checkExists()) {
		echo '
			<script>
			alert("' . $institution->getName()->getFormatted() . ' already exists in a separate entry.  ' . 
			'Forwarding you to their page.");
			window.location = "form.php?id=' . $institution->getID() . '";
			</script>';
	}
	else {
		$institution->insert();	// (add handling for false results?)
	}
}

// Forward to view page
echo '
	<script>
	window.location = "view.php?id=' . $institution->getID() . '";
	</script>';


?>