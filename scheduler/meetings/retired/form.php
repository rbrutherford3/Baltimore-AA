<?php

// Include files
include '../includes/dbconnect.php';
include '../includes/dows.php';

// Edit group if ID provided
if(isset($_GET['id'])) {
	$newMeeting = false;

	$meetingID = $_GET['id'];

	// Grab group info to be edited
	$stmt = $db->prepare("SELECT DisplayID, Institution AS InstitutionID, i.Name AS InstitutionName, Address, City, Zip, BG, DOW, Time, Gender, m.Active AS MeetingActive,  
		NotesPublic, NotesPrivate, i.Notes AS InstitutionNotes, 
		Sponsor AS SponsorID, s.Name AS SponsorName, s.Initial AS SponsorInitial, s.Phone AS SponsorPhone, s.Notes AS SponsorNotes, 
		CoSponsor AS CoSponsorID, c.Name AS CoSponsorName, c.Initial AS CoSponsorInitial, c.Phone AS CoSponsorPhone, c.Notes AS CoSponsorNotes 
		FROM meetings m 
		LEFT JOIN institutions i 
		ON m.Institution=i.ID 
		LEFT JOIN people s 
		ON m.Sponsor=s.ID 
		LEFT JOIN people c 
		ON m.CoSponsor=c.ID 
		WHERE m.ID=:meetingID;");
	$stmt->bindValue(':meetingID', $meetingID, PDO::PARAM_INT);
	$stmt->execute();

	// Grab data if group exists
	if ($row = $stmt->fetch()) {

		// Grab display ID
		$meetingDisplayID= $row['DisplayID'];

		// Grab institution ID
		$institutionID = $row['InstitutionID'];

		// Check for each day of week and enable checkmark if group meets that day
		for($i=0; $i<7; $i++) {
			if($dowsN[$i] & $row['DOW']) {	// bitmask: "does group meet this day of week?"
				$dow[$i] = ' selected="selected"';
			}
			else {
				$dow[$i] = '';
			}
		}

		// Format meeting time
		$meetingTime = strtotime($row['Time']);
		$meetingTime = date('H:i', $meetingTime);

		// Check for each gender possibility and select the appropriate one for the pulldown menu
		for($i=0; $i<3; $i++) {
			if ($i==$row['Gender']) {
				$gender[$i] = ' selected="selected"';
			}
			else {
				$gender[$i] = '';
			}
		}

		// Check for the group active field and check the box if it is true
		if ($row['MeetingActive']) {
			$meetingActive = ' checked';
		}
		else {
			$meetingActive = '';
		}

		// Grab meeting notes (public and private)
		$meetingNotesPublic = htmlspecialchars($row['NotesPublic']);
		$meetingNotesPrivate = htmlspecialchars($row['NotesPrivate']);

		// Grab institution notes (private)
		$institutionNotes = htmlspecialchars($row['InstitutionNotes']);

/*  		// Grab rep ID
		$repID = $row['RepID']; */

		// If there's no institution, then fill empty info and set button defaults
		if(is_null($institutionID)) {
			$institutionName = '';
			$institutionAddress = '';
			$institutionCity = '';
			$institutionZip = '';
			$bg = '';
			$addNewInstitutionChecked = ' checked';
		}

		// If there's an institution, then fill info and set button defaults
		else {
			$institutionName = htmlspecialchars($row['InstitutionName']);
			$institutionAddress = htmlspecialchars($row['Address']);
			$institutionCity = htmlspecialchars($row['City']);
			$institutionZip = htmlspecialchars($row['Zip']);
			if ($row['BG']) {
				$bg = ' checked';
			}
			else {
				$bg = '';
			}
			$addNewInstitutionChecked = '';
			$editExistingInstitutionChecked = ' checked';
		}

	// Send to "add a meeting" page if no group is found
	} else {
		echo '<script type="text/javascript">';
		echo 'alert("Meeting with ID ' . $meetingID . ' does not exist.  Forwarding you to form for adding a new meeting.");';
		echo 'window.location.href = "form";';
		echo '</script>';
		die('Forwarding...');
	}

	// Set title
	$title = 'Institution Committee - Edit "' . $institutionName . '" Meeting';
	$header = 'Edit Meeting';
}

// Adding group if no ID provided
else {
	$newMeeting = true;

	// Set ID to null (this will signify a new entry on load php page);
	$meetingID = null;

	// Set Display ID to null
	$meetingDisplayID = null;

	// Set ID to null (this will signify a new entry on load php page);
	$institutionID = null;

	// Start with blank institution name
	$institutionName = '';

	// Start with blank institution address
	$institutionAddress = '';
	$institutionCity = '';
	$institutionZip = '';

	// Default to "doesn't require background check
	$bg = '';

	// Start with blank days of week
	for($i=0; $i<7; $i++) {
		$dow[$i] = '';
	}

	// Start with blank meeting time
	$meetingTime = '';

	// Default to 'All genders'
	$gender[0] = ' selected="selected"';
	$gender[1] = '';
	$gender[2] = '';

	// Default to meeting active
	$meetingActive = ' checked';

	// Start with blank notes
	$meetingNotesPublic = '';
	$meetingNotesPrivate = '';
	$institutionNotes = '';

	// Set input defaults
	$addNewInstitutionChecked = ' checked';

	// Set title
	$title = 'Institution Committee - Add New Meeting';
	$header = 'Add New Meeting';
}

// Grab institutions for pull-down list
$stmt = $db->prepare("SELECT ID, Name, Address, City FROM Institutions WHERE Active=1 ORDER BY Name ASC;");
$stmt->execute();

$foundInstitutions = false;
while ($row = $stmt->fetch()) {
	$foundInstitutions = true;
	$institutionIDs[] = $row['ID'];
	$institutionNames[] = $row['Name'];
	$institutionAddresses[] = $row['Address'];
	$institutionCities[] = $row['City'];
	// If rep ID exists, then choose it
	if(is_null($institutionID)) {
		$institutionChecked[] = '';
	}
	else if ($institutionID == $row['ID']) {
		$institutionChecked[] = ' selected';
	}
	else {
		$institutionChecked[] = '';
	}
}

// Header HTML
include('../includes/header.php');
echo ' 
	<script type="text/javascript" src="buttons.js"></script>
	<script type="text/javascript" src="tabless.js"></script>
	<script type="text/javascript" src="validate.js"></script>
	<title>' . $title . '</title>
</head>
<body>
	<h1>' . $header . '</h1>
	<h2>Meeting</h2>
';

// Begin form HTML
echo'
	<form action="load" name="meetingForm" onsubmit="return validateForm()" method="post">';

// Meeting ID
echo '
		<input type="hidden" id="MeetingID" name="meetingID" value="' . $meetingID . '" />';

// Old Display ID
echo '
		<input type="hidden" id="eetingOldDisplayID" name="meetingOldDisplayID" value="' . $meetingDisplayID . '" />';

// Meeting ID html
echo '
		<b>ID:</b>
		<br>
		<input type="number" name="meetingNewDisplayID"  min="100" max="799" value="' . $meetingDisplayID . '" maxlength="3" />
		<br>';


// Days of week form HTML
echo '
		<br>
		<b>Day of Week:</b>
		<br>
		<select name="meetingDOW">
			<option value=0>*Select Day*</option>';
for($i=0; $i<7; $i++) {
	echo '
			<option value=' . $dowsN[$i] . '' . $dow[$i] . '>' . $dows[$i] . '</option>';
}
echo '
		</select>
		<br>';

// Time HTML
echo '
		<br>
		<b>Meeting Time:</b>
		<br>
		<input type="time" name="meetingTime" value="' . $meetingTime . '">
		<br>
';


// Genders form HTML
echo '
		<br>
		<b>Gender:</b><br>
		<select name="meetingGender">
			<option value="0"' . $gender[0] . '>All genders</option>
			<option value="1"' . $gender[1] . '>Men only</option>
			<option value="2"' . $gender[2] . '>Women only</option>
		</select><br><br>';

// Meeting active check form HTML
echo '
		<b><input type="checkbox" id="meetingActive" name="meetingActive"' . $meetingActive . ' />
		<label for="meetingActive">Meeting active (currently accepting meeting assignments)</label></b>
		<br>';

// Meeting notes
echo '
		<br>
		<b>Printed Notes:</b>
		<br>
		<textarea name="meetingNotesPublic" rows="4" cols="53">' . $meetingNotesPublic . '</textarea>
		<br>
		<br>
		<b>Private Notes:</b>
		<br>
		<textarea name="meetingNotesPrivate" rows="4" cols="53">' . $meetingNotesPrivate . '</textarea>
		<br>
		<br>
		<hr align="left" width="800px">';

// Institution ID html
echo '
		<input type="hidden" id="InstitutionID" name="institutionID" value="' . $institutionID . '">';

// Institution add/edit/select options
	echo '
			<h2>Institution</h2>
			<table>';

// If institutions are in the database, then show the pulldown menu
if ($foundInstitutions) {
echo '
				<tr>
					<td>';
	// Selection for pull-down menu
	echo '
						<input type="radio" id="SelectExistingInstitution" value=1 name="institutionInputType">
					</td>
					<td>
						<i><b><label for="SelectExistingInstitution">Select from list:</label></b></i>
					</td>
					<td colspan="3">';


	// Institution pull-down menu
	echo '
						<select id="InstitutionSelect" name="institutionIDSelect" disabled>
							<option value=0>***Select Institution***</option>';

	// Populate list of institutions
	for ($i=0; $i<count($institutionIDs); $i++) {
		echo'
							<option value=' . $institutionIDs[$i] . ' ' . $institutionChecked[$i] . '>' . 
								$institutionNames[$i] . ' - ' . $institutionAddresses[$i] . ', ' . $institutionCities[$i] . 
							'</option>';
	}
	echo '
						</select>
					</td>
				</tr>';
}

// Add/Select options
echo '
			<tr>
				<td colspan="5" height="10px" />
			</tr>
			<tr>
				<td>
					<input type="radio" id="addNewInstitution" value=2 name="institutionInputType"' . $addNewInstitutionChecked . '>
				</td>
				<td nowrap>
					<i><b><label for="addNewInstitution">Add New Institution</label></b></i>
				</td>
				<td width="10px" />
				<td>
				';

// Only show edit button if we are editing an existing institution and the institutionID was found
if(!($newMeeting)  && (!is_null($institutionID))) {
echo '
					<input type="radio" id="EditExistingInstitution" value=3 name="institutionInputType"' . $editExistingInstitutionChecked . '>
				</td>
				<td>
					<i><b><label for="EditExistingInstitution">Edit Existing Institution</label></b></i>';
}
echo '
				</td>
			</tr>
		</table>';



// Institution name
echo '
		<br>
		<b>Institution Name: </b>
		<br>
		<input type="text" id="InstitutionName" name="institutionName" value="' . $institutionName . '" size=53></input><br><br>';	// Name form HTML

// Institution address
echo '
		<b>Institution Address:</b><br>
		<input type="text" id="InstitutionAddress" name="institutionAddress" value="' . $institutionAddress . '" size=53></input><br><br>
		<b>City:</b>
		<br>
		<input type="text" id="InstitutionCity" name="institutionCity" value="' . $institutionCity . '" size=20></input>
		<br><br>
		<b>Zip:</b>
		<br>
		<input type="text" id="InstitutionZip" name="institutionZip" value="' . $institutionZip . '" size=5 maxlength=5></input>
		<br>';

// Background check
echo '
		<br>
		<b><input type="checkbox" id="InstitutionBG" name="institutionBG"' . $bg . ' />
		<label for="InstitutionBG">Requires background checks</label></b>
		<br>';

// Institution notes
echo '
		<br>
		<b>Notes (not printed):</b>
		<br>
		<textarea id="InstitutionNotes" name="institutionNotes" rows="4" cols="53">' . $institutionNotes . '</textarea>
		<br>';

/* // Group ID html
echo '
		<input type="hidden" name="groupID" value="' . $groupID . '">';

// Rep ID html
echo '
		<input type="hidden" id="RepID" name="repID" value="' . $repID . '">'; */

// Rep HTML
/* echo '
		<br>
		<b>Representative:</b><br><br>
		<table frame="box" style="padding: 10px;">
			<tr>
				<td>
					<input type="radio" id="addNewRep" value=1 name="repInputType"' . $addNewRepChecked . '>
				</td>
				<td>
					<i><b><label for="addNewRep">Add New Rep</label></b></i>
				</td>
				<td>';

// Only show edit button if we are editing an existing group and the repID was found
if(($edit)  && (!is_null($repID))) {
echo '
					<input type="radio" id="editExistingRep" value=2 name="repInputType"' . $editExistingRepChecked . '>
					<i><b><label for="editExistingRep">Edit Existing Rep</label></b></i>';
}
echo '
				</td>
			</tr>
			<tr>
				<td>
					&nbsp
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					First Name:
				</td>
				<td>
					&nbsp<input type="text" id="RepName" name="repName" value="' . $repName . '" size=25 maxlength="25"></input>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					Last Initial:
				</td>
				<td>
					&nbsp<input id="RepInitial" type="text" name="repInitial" value="' . $repInitial . '" size=1 maxlength="1"></input>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					Phone Number:
				</td>
				<td>
					(<input id="RepPhone1" type="text" name="repPhone1" value ="' . $repPhone1 . '" maxlength="3" size="1"></input>) 
					<input id="RepPhone2" type="text" name="repPhone2" value ="' . $repPhone2 . '" maxlength="3" size="1"></input> -
					<input id="RepPhone3" type="text" name="repPhone3" value ="' . $repPhone3 . '" maxlength="4" size="2"></input>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td valign="top">
					Notes (not printed):
				</td>
				<td>
					&nbsp<textarea id="RepNotes" name="repNotes" rows="6" cols="25">' . htmlspecialchars($repNotes) . '</textarea><br>
				</td>
			</tr>
			<tr>
				<td>
					&nbsp
				</td>
			</tr>';

// If people are in the database, then show the pulldown menu
if ($foundPeople) {
	echo '
			<tr>
				<td>
					<input type="radio" id="selectExistingRep" value=3 name="repInputType">
				</td>
				<td>
					<i><b><label for="selectExistingRep">Select from list:</label></b></i>
				</td>
				<td>
					<select id="RepSelect" name="repIDSelect" disabled>
						<option value=0>***Select Person***</option>';

	// Populate list of people
	for ($i=0; $i<count($repIDs); $i++) {
		echo'
							<option value=' . $repIDs[$i] . ' ' . $repChecked[$i] . '>' . 
								$repNames[$i] . ' ' . $repInitials[$i] . ' - (' . 
								$repPhones1[$i] . ') ' . $repPhones2[$i] . '-' . $repPhones3[$i] . 
							'</option>';
	}
	echo '
					</select>
				</td>
			</tr>';
}
echo '
		</table>'; */


// Buttons and end of form and page
echo '
		<br>
		<a href="javascript:history.back()"><button type="button">Back</button></a>
		<input type="submit">
	</form>
</body>
</html>';
?>