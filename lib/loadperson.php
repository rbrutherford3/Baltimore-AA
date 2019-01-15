<?php
/* This function is used to determine how to handle the different input options for a person (add, edit or select).
I originally wanted to include this in the person class, but ran into some difficulties, (like how it would have access
to the form data), so I decided to use a separate function and just have it be included in the various form files.  It
has worked, so I don't mess with it right now */

include_once 'person.php';

function loadPerson($db, $nameBase) {
	$option = $_POST[$nameBase]['method'];	// Grab input method
	if ($option == 1) {	// "Add" method (inserting a record)
		// Make a new person object with the information from the form
		$person = new person($db, $_POST[$nameBase]['name'], $_POST[$nameBase]['initial'], 
			$_POST[$nameBase]['phone']['1'], $_POST[$nameBase]['phone']['2'], $_POST[$nameBase]['phone']['3'], 
			$_POST[$nameBase]['notes'], isset($_POST[$nameBase]['active']));
		// Check that a person with that same information doesn't already exist
		if ($person->checkExists()) {
		echo '<script>alert("' . $person->getName()->getFormatted() . $person->getInitial()->getFormatted() . 
			' already exists in database, will use existing entry.  You may wish to edit this person with new ' .
			'notes or manage your roster.");</script>';
		}
		// If a person doesn't already exist, insert the record
		else {
			$person->insert();	// (Should add code for false returns)
		}
		return $person->getID();	// Return the ID for insertion into group or institution object
	}
	else if ($option == 2) {	// "Edit" method (updating a record)
		// Make a new person object with the information from the form
		$person = new person($db, $_POST[$nameBase]['name'], $_POST[$nameBase]['initial'], 
			$_POST[$nameBase]['phone']['1'], $_POST[$nameBase]['phone']['2'], $_POST[$nameBase]['phone']['3'], 
			$_POST[$nameBase]['notes'], isset($_POST[$nameBase]['active']), $_POST[$nameBase]['id']);
		// Check that the new information being changed doesn't conflict with an existing record
		if ($person->checkExistsExcluding()) {
			echo '<script>alert("' . $person->getName()->getFormatted() . $person->getInitial()->getFormatted() . 
				' already exists in a separate entry, will use existing entry.  You may wish to edit this person with ' .
				'new notes or manage your roster.");</script>';
		}
		// If no conflict, update the record
		else {
			$person->update();
		}
		return $person->getID();	// Return the ID for insertion into group or institution object
	}
	else if ($option == 3) {	// If selecting a person from the pulldown menu	
		return $_POST[$nameBase]['pulldown'];	// Simply return the ID!
	}
	else {
		die('Invalid selection option in handlePerson function');
	}
}



?>