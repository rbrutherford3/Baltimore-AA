<?php
/*
load.php (group version)
Form processing, updating database, and forwarding to view page
*/


// Include files
include_once '../../lib/dbconnect.php';
include_once '../../lib/person.php';
include_once '../../lib/group.php';
include_once '../../lib/loadperson.php';
include_once '../../lib/recaptcha.php';

$db = database::connect();

// Define form element root names
$formName = 'form';
$groupBase = 'group';
$rep1Base = 'rep1';
$rep2Base = 'rep2';

recaptcha::verify(false);

// Get ID based on input method for person
$rep1ID = loadPerson($db, $rep1Base);

// Do the same for rep2 if it exists
if ($_POST[$rep2Base]['exists']) {
	$rep2ID = loadPerson($db, $rep2Base);
}
else {
	$rep2ID = null;
}

// Compile group object
if (isset($_POST[$groupBase]['id'])) {	// Update db if editing

	// Initiate group object (with ID)
	$group = new group($db, $_POST[$groupBase]['name'], isset($_POST[$groupBase]['dow'][0]), 
		isset($_POST[$groupBase]['dow'][1]), isset($_POST[$groupBase]['dow'][2]), isset($_POST[$groupBase]['dow'][3]), 
		isset($_POST[$groupBase]['dow'][4]), isset($_POST[$groupBase]['dow'][5]), isset($_POST[$groupBase]['dow'][6]), 
		$_POST[$groupBase]['gender'], isset($_POST[$groupBase]['bg']), $rep1ID, $rep2ID, $_POST[$groupBase]['notes'], 
		isset($_POST[$groupBase]['active']), isset($_POST[$groupBase]['probation']), $_POST[$groupBase]['id']);

	// Check to see that information isn't conflicting with another record
	if ($group->checkExistsExcluding()) {
		echo '
			<script>
			alert("' . $group->getName()->getFormatted() . ' already exists in a separate entry.  ' . 
			'Forwarding you to their page.");
			window.location = "form.php?id=' . $group->getID() . '";
			</script>';
	}

	// Update otherwise
	else {
		$group->update();	// (add handling for false results?)
	}
}
else { // Insert into db if adding

	// Initiate group object (without ID)
	$group = new group($db, $_POST[$groupBase]['name'], isset($_POST[$groupBase]['dow'][0]), 
		isset($_POST[$groupBase]['dow'][1]), isset($_POST[$groupBase]['dow'][2]), isset($_POST[$groupBase]['dow'][3]), 
		isset($_POST[$groupBase]['dow'][4]), isset($_POST[$groupBase]['dow'][5]), isset($_POST[$groupBase]['dow'][6]), 
		$_POST[$groupBase]['gender'], isset($_POST[$groupBase]['bg']), $rep1ID, $rep2ID, $_POST[$groupBase]['notes'], 
		isset($_POST[$groupBase]['active']), isset($_POST[$groupBase]['probation']));

	// Check to see that information isn't already in db
	if ($group->checkExists()) {
		echo '
			<script>alert("' . $group->getName()->getFormatted() . ' already exists in a separate entry.  ' . 
			'Forwarding you to their page.");
			window.location = "form.php?id=' . $group->getID() . '";
			</script>';
	}
	else {
		$group->insert();	// (add handling for false results?)
	}
}

// Forward to view page
echo '
	<script>
	window.location = "view.php?id=' . $group->getID() . '";
	</script>';


?>