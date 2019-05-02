<?php
/*
load.php (person version)
Form processing, updating database, and forwarding to view page
*/


// Include files
include_once '../../lib/dbconnect.php';
include_once '../../lib/person.php';
// Define form element root names
$formName = 'form';
$personBase = 'person';

// Compile person object
if (isset($_POST[$personBase]['id'])) {	// Update db if editing

	// Initiate person object (with ID)
	$person = new person($db, $_POST[$personBase]['name'], $_POST[$personBase]['initial'], 
		$_POST[$personBase]['phone']['1'], $_POST[$personBase]['phone']['2'], $_POST[$personBase]['phone']['3'], 
		$_POST[$personBase]['notes'], isset($_POST[$personBase]['active']), $_POST[$personBase]['id']);
	
	// Check to see that information isn't conflicting with another record
	if ($person->checkExistsExcluding()) {
		echo '
			<script>
			alert("' . $person->getName()->getFormatted() . $person->getInitial()->getFormatted() . ' already exists in a separate entry.  ' . 
			'Forwarding you to their page.");
			window.location = "form.php?id=' . $person->getID() . '";
			</script>';
	}
	
	// Update otherwise
	else {
		$person->update();	// (add handling for false results?)
	}
}
else { // Insert into db if adding

	// Initiate person object (without ID)
	$person = new person($db, $_POST[$personBase]['name'], $_POST[$personBase]['initial'], 
		$_POST[$personBase]['phone']['1'], $_POST[$personBase]['phone']['2'], $_POST[$personBase]['phone']['3'], 
		$_POST[$personBase]['notes'], isset($_POST[$personBase]['active']));
		
	// Check to see that information isn't already in db
	if ($person->checkExists()) {
		echo '
			<script>
			alert("' . $person->getName()->getFormatted() . $person->getInitial()->getFormatted() . ' already exists in a separate entry.  ' . 
			'Forwarding you to their page.");
			window.location = "form.php?id=' . $person->getID() . '";
			</script>';
	}
	else {
		$person->insert();	// (add handling for false results?)
	}
}

// Forward to view page
echo '
	<script>
	window.location = "view.php?id=' . $person->getID() . '";
	</script>';


?>