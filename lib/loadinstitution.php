<?php
/* This function is used to determine how to handle the different input options for an institution (add, edit or select).
I originally wanted to include this in the institution class, but ran into some difficulties, (like how it would have access
to the form data), so I decided to use a separate function and just have it be included in the various form files.  It
has worked, so I don't mess with it right now */

include_once 'institution.php';

function loadInstitution($db, $nameBase) {
	$option = $_POST[$nameBase]['method'];	// Grab input method
	if ($option == 1) {	// "Add" method (inserting a record)
		// Make a new institution object with the information from the form
		$institution = new institution($db, $_POST[$nameBase]['name'], $_POST[$nameBase]['address'], 
			$_POST[$nameBase]['city'], $_POST[$nameBase]['zip'], isset($_POST[$nameBase]['bg']), 
			$_POST[$nameBase]['notesPublic'], $_POST[$nameBase]['notes'], isset($_POST[$nameBase]['active']));
		// Check that a institution with that same information doesn't already exist
		if ($institution->checkExists()) {
		echo '<script>alert("' . $institution->getName()->getFormatted() . 
			' already exists in database, will use existing entry.  You may wish to edit this institution with new ' .
			'notes or new address or manage your list.");</script>';
		}
		// If a institution doesn't already exist, insert the record
		else {
			$institution->insert();	// (Should add code for false returns)
		}
		return $institution->getID();	// Return the ID for insertion into group or institution object
	}
	else if ($option == 2) {	// "Edit" method (updating a record)
		// Make a new institution object (with ID) with the information from the form
		$institution = new institution($db, $_POST[$nameBase]['name'], $_POST[$nameBase]['address'], 
			$_POST[$nameBase]['city'], $_POST[$nameBase]['zip'], isset($_POST[$nameBase]['bg']), 
			$_POST[$nameBase]['notesPublic'], $_POST[$nameBase]['notes'], isset($_POST[$nameBase]['active']), $_POST[$nameBase]['id']);
		// Check that the new information being changed doesn't conflict with an existing record
		if ($institution->checkExistsExcluding()) {
			echo '<script>alert("' . $institution->getName()->getFormatted() .  
				' already exists in a separate entry, will use existing entry.  You may wish to edit this institution with ' .
				'new notes or new address or manage your list.");</script>';
		}
		// If no conflict, update the record
		else {
			$institution->update();
		}
		return $institution->getID();	// Return the ID for insertion into meeting object
	}
	else if ($option == 3) {	// If selecting an institution from the pulldown menu
		return $_POST[$nameBase]['pulldown'];	// Simply return the ID!
	}
	else {
		die('Invalid selection option in handleInstitution function');
	}
}



?>