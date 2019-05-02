<?php

// Include files
include '../includes/dbconnect.php';
include '../includes/dows.php';

// GRAB ALL INPUTS FROM form.php

// Grab ID
if ($_POST['meetingID'] ==  '') {
	$newMeeting = true;
}
else {
	$newMeeting = false;
}

$meetingID = $_POST['meetingID'];

// Grab Old Display ID
$meetingOldDisplayID = $_POST['meetingOldDisplayID'];

// Grab New Display ID
$meetingNewDisplayID = $_POST['meetingNewDisplayID'];

// Grab day of week
$meetingDOW = $_POST['meetingDOW'];

// Grab time
$meetingTime = strtotime($_POST['meetingTime']);
$meetingTime = date('H:i', $meetingTime);

// Grab gender
$meetingGender = $_POST['meetingGender'];

// Grab meeting active check variable
$meetingActive = (isset($_POST['meetingActive'])) ? 1 : 0;

// Grab meeting notes (set to null if blank so a blank entry doesn't go in the database)
if($_POST['meetingNotesPublic']=='') {
	$meetingNotesPublic = null;
}
else {
	$meetingNotesPublic = $_POST['meetingNotesPublic'];
}

if($_POST['meetingNotesPrivate']=='') {
	$meetingNotesPrivate = null;
}
else {
	$meetingNotesPrivate = $_POST['meetingNotesPrivate'];
}

// Grab institution input option
$institutionOption = $_POST['institutionInputType'];

// Grab CORRECT institution ID (different ones for editing vs. choosing (none for adding)
if ($institutionOption==1) {
	$institutionID = $_POST['institutionIDSelect'];
}
else if ($institutionOption==3) {
	$institutionID = $_POST['institutionID'];
}


// Only grab institution info if adding or editing institution, not selecting
if (($institutionOption==2) || ($institutionOption==3)) {
	
	// Grab institution name
	$institutionName = $_POST['institutionName'];
	
	// Grab institution address
	$institutionAddress = $_POST['institutionAddress'];
	$institutionCity = $_POST['institutionCity'];
	$institutionZip = $_POST['institutionZip'];
	
	// Grab institution bg
	$institutionBG = (isset($_POST['institutionBG'])) ? 1 : 0;
	
	// Grab institution notes (set to null if blank so a blank entry doesn't go in the database)
	if($_POST['institutionNotes']=='') {
		$institutionNotes = null;	// Keep null if blank
	}
	else {
		$institutionNotes = $_POST['institutionNotes'];
	}
}

// If a new meeting or the meeting ID has changed
if (($newMeeting) || (!($newMeeting) && ($meetingOldDisplayID <> $meetingNewDisplayID))) {
	
	// Check to see if meeting by same ID already exists
	$stmt = $db->prepare("SELECT ID FROM meetings WHERE DisplayID=:meetingNewDisplayID;");
	$stmt->bindValue(':meetingNewDisplayID', $meetingNewDisplayID, PDO::PARAM_INT);
	$stmt->execute();
	if ($row = $stmt->fetch()) {
		$meetingID = $row['ID'];
		echo '<script type="text/javascript">';
		echo 'window.alert("Meeting ' . $meetingNewDisplayID . ' already exists!  Taking you to their entry so you can edit it.");';
		echo 'window.location.href = "form?id=' . $meetingID . '";';
		echo '</script>';
		die('Forwarding...');
	}
}

// Note: Rep ID grabbed, nothing needs to be done for option 1

// If adding a new institution...
if ($institutionOption==2) {

	// Check to see if institution already exists
	$stmt = $db->prepare("SELECT ID FROM institutions WHERE Name=:institutionName;");
	$stmt->bindValue(':institutionName', $institutionName, PDO::PARAM_STR);
	$stmt->execute();
	
	// Warn the user if the rep already exists in the database (might need to modify notes)
	if ($row = $stmt->fetch()) {
		$institutionID = $row['ID'];
		echo '<script type="text/javascript">alert("WARNING: Institution with same name already exists!  Click \'Edit meeting just added\' to change institution info."); </script>';
	}
	// Add institution if it doesn't exist
	else {
		// Add new institution
		$stmt = $db->prepare("INSERT INTO institutions (Name, Address, City, Zip, BG, Notes) VALUES 
			(:institutionName, :institutionAddress, :institutionCity, :institutionZip, :institutionBG, :institutionNotes);");
		$stmt->bindValue(':institutionName', $institutionName, PDO::PARAM_STR);
		$stmt->bindValue(':institutionAddress', $institutionAddress, PDO::PARAM_STR);
		$stmt->bindValue(':institutionCity', $institutionCity, PDO::PARAM_STR);
		$stmt->bindValue(':institutionZip', $institutionZip, PDO::PARAM_STR);
		$stmt->bindValue(':institutionBG', $institutionBG, PDO::PARAM_INT);
		$stmt->bindValue(':institutionNotes', $institutionNotes, PDO::PARAM_STR);
		$stmt->execute();
		
		$institutionID = $db->lastInsertId();
	}
}

// If editing an existing institution...
else if ($institutionOption==3) {
	// Update institutions's record
	$stmt = $db->prepare("UPDATE institutions SET 
		Name=:institutionName, Address=:institutionAddress, City=:institutionCity, Zip=:institutionZip, BG=:institutionBG, Notes=:institutionNotes
		WHERE ID=:institutionID;");
	$stmt->bindValue(':institutionName', $institutionName, PDO::PARAM_STR);
	$stmt->bindValue(':institutionAddress', $institutionAddress, PDO::PARAM_STR);
	$stmt->bindValue(':institutionCity', $institutionCity, PDO::PARAM_STR);
	$stmt->bindValue(':institutionZip', $institutionZip, PDO::PARAM_STR);
	$stmt->bindValue(':institutionBG', $institutionBG, PDO::PARAM_INT);
	$stmt->bindValue(':institutionNotes', $institutionNotes, PDO::PARAM_STR);
	$stmt->bindValue(':institutionID', $institutionID, PDO::PARAM_INT);
	$stmt->execute();
}

// Update existing record if editing, insert new one if adding
if ($newMeeting) {
	$stmt = $db->prepare("INSERT INTO meetings (DisplayID, Institution, DOW, Time, Gender, NotesPublic, NotesPrivate, Active) 
		VALUES (:meetingNewDisplayID, :institutionID, :meetingDOW, :meetingTime, :meetingGender, :meetingNotesPublic, :meetingNotesPrivate, :meetingActive);");
	$stmt->bindValue(':meetingNewDisplayID', $meetingNewDisplayID, PDO::PARAM_INT);
	$stmt->bindValue(':institutionID', $institutionID, PDO::PARAM_INT);
	$stmt->bindValue(':meetingDOW', $meetingDOW, PDO::PARAM_INT);
	$stmt->bindValue(':meetingTime', $meetingTime, PDO::PARAM_INT);
	$stmt->bindValue(':meetingGender', $meetingGender, PDO::PARAM_INT);
	$stmt->bindValue(':meetingNotesPublic', $meetingNotesPublic, PDO::PARAM_STR);
	$stmt->bindValue(':meetingNotesPrivate', $meetingNotesPrivate, PDO::PARAM_STR);
	$stmt->bindValue(':meetingActive', $meetingActive, PDO::PARAM_INT);
	
	$stmt->execute();
	
	$meetingID = $db->lastInsertId(); // Grab ID of record just inserted
}
else {
/* 	echo '<script type="text/javascript">';
	echo 'window.alert("Made it this far!");';
	echo '</script>'; */
	$stmt = $db->prepare("UPDATE meetings SET DisplayID=:meetingNewDisplayID, Institution=:institutionID, 
		DOW=:meetingDOW, Time=:meetingTime, Gender=:meetingGender, NotesPublic=:meetingNotesPublic, 
		NotesPrivate=:meetingNotesPrivate, Active=:meetingActive 
		WHERE ID=:meetingID;");
	$stmt->bindValue(':meetingNewDisplayID', $meetingNewDisplayID, PDO::PARAM_INT);
	$stmt->bindValue(':institutionID', $institutionID, PDO::PARAM_INT);
	$stmt->bindValue(':meetingDOW', $meetingDOW, PDO::PARAM_INT);
	$stmt->bindValue(':meetingTime', $meetingTime, PDO::PARAM_INT);
	$stmt->bindValue(':meetingGender', $meetingGender, PDO::PARAM_INT);
	$stmt->bindValue(':meetingNotesPublic', $meetingNotesPublic, PDO::PARAM_STR);
	$stmt->bindValue(':meetingNotesPrivate', $meetingNotesPrivate, PDO::PARAM_STR);
	$stmt->bindValue(':meetingActive', $meetingActive, PDO::PARAM_INT);
	$stmt->bindValue(':meetingID', $meetingID, PDO::PARAM_INT);
	
	$stmt->execute();
}

echo '<script type="text/javascript">';
echo 'window.location.href = "form?id=' . $meetingID . '";';
echo '</script>';

?>