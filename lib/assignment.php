<?php

/* assignment.php
For a variety of practical reasons, I did not use data.php to extend this class, but rather just used
one function (viewall) to populate assignments and view or edit them.  The process was just a little
too involved to use the simple data class. */

include_once 'data.php';
include_once 'datatypes.php';
include_once 'meeting.php';
include_once 'group.php';
include_once 'recaptcha.php';
date_default_timezone_set('America/New_York');

// Class assignment, has only one function: viewAll
class assignment {

	protected $db;

	function __construct($db) {
		$this->db = $db;
	}

	/* The viewAll function is the only function in this class.  Its arguments are:
	$monthQ: query month, null if all months and years
	$yearQ: query year, null if all months and years
	$groupQ: query group, null if all groups
	$meetingQ: query meeting, null if all meetings
	$sort: sort mode, zero if null
	$edit: true if editing assignments, false if simply viewing them */
	public function viewAll($monthQ, $yearQ, $groupQ, $meetingQ, $sort, $edit) {

		$viewPeople = false;
		$viewLinks = true;
		$viewAddress = false;

		// SET UP ASSIGNMENTS SQL
		// create different date SQL statements based on whether month and/or year were specified
		if (is_null($monthQ) && is_null($yearQ)) {
			$month = null;
			$year = null;
			$dateSQL = "WHERE `Date` IS NOT NULL ";
		}
		elseif (is_null($monthQ) && !is_null($yearQ)) {
			$month = null;
			$year = $yearQ;
			$dateSQL = "WHERE YEAR(`Date`)=:year ";
		}
		elseif (!is_null($monthQ) && is_null($yearQ)) {
			$month = $monthQ;
			$year = null;
			$dateSQL = "WHERE MONTH(`Date`)=:month ";
		}
		else {
			$month = $monthQ;
			$year = $yearQ;
			$dateSQL = "WHERE
				MONTH(`Date`)=:month
				AND
				YEAR(`Date`)=:year ";
		}
		// Set up group SQL if group is specified
		if (is_null($groupQ)) {
			$groupSQL = "";
		}
		else {
			$groupSQL = "AND a.`Group`=:group ";
		}
		// Set up meeting SQL if meeting is specified
		if (is_null($meetingQ)) {
			$meetingSQL = "";
		}
		else {
			$meetingSQL = "AND a.`Meeting`=:meeting ";
		}
		// Set up the various sort options, self explanatory.  Note the default.  (DisplayID = Meeting ID)
		switch($sort) {
			case 1: 
				$sortSQL = "ORDER BY `DisplayID` DESC, `Date` DESC;";
				break;
			case 2:
				$sortSQL = "ORDER BY `Date` ASC, `DisplayID` ASC;";
				break;
			case 3:
				$sortSQL = "ORDER BY `Date` DESC, `DisplayID` DESC;";
				break;
			case 4:
				$sortSQL = "ORDER BY `Institution` ASC, `Date` ASC;";
				break;
			case 5:
				$sortSQL = "ORDER BY `Institution` DESC, `Date` DESC;";
				break;
			default:
				$sortSQL = "ORDER BY `DisplayID` ASC, `Date` ASC;";
				break;
		}

		$scopeSQL = $dateSQL . 
			$groupSQL . 
			$meetingSQL . 
			$sortSQL;

		// Compile SQL statement, note the optional bindings
		$sqlAssignments = "SELECT 
			a.`ID`, 
			m.`DOW`, 
			a.`Date`, 
			a.`Meeting` AS `MeetingID`, 
			`DisplayID`, 
			i.`Name` AS `Institution`, 
			a.`Group` AS `GroupID`, 
			g.`Name` AS `Group`, 
			a.`Notes`, 
			m.`Gender`, 
			i.`BG`" . 
			($viewAddress ? ",
			i.`Address`,
			i.`City`,
			i.`Zip`" : "") . 
			($viewPeople ? ",
			r.`Name` AS `RepName`,
			r.`Initial` AS `RepInitial`,
			r.`Phone` AS `RepPhone`,
			r2.`Name` AS `Rep2Name`,
			r2.`Initial` AS `Rep2Initial`,
			r2.`Phone` AS `Rep2Phone`,
			s.`Name` AS `SponsorName`,
			s.`Initial` AS `SponsorInitial`,
			s.`Phone` AS `SponsorPhone`,
			c.`Name` AS `CoSponsorName`,
			c.`Initial` AS `CoSponsorInitial`,
			c.`Phone` AS `CoSponsorPhone`,
			c2.`Name` AS `CoSponsor2Name`,
			c2.`Initial` AS `CoSponsor2Initial`,
			c2.`Phone` AS `CoSponsor2Phone`" : "") .
			"FROM assignments a 
			LEFT JOIN meetings m 
			ON a.`Meeting`=m.`ID` 
			LEFT JOIN institutions i 
			ON m.`Institution`=i.ID 
			LEFT JOIN groups g 
			ON a.`Group`=g.`ID`" . 
			($viewPeople ? "
			LEFT JOIN people r 
			ON g.`Rep`=r.`ID`
			LEFT JOIN people r2
			ON g.`Rep2`=r2.`ID`
			LEFT JOIN people s 
			ON m.`Sponsor`=s.`ID`
			LEFT JOIN people c
			ON m.`CoSponsor`=c.`ID`
			LEFT JOIN people c2
			ON m.`CoSponsor2`=c2.`ID`" : "") .
			$scopeSQL;

		$monthView = true;
		$stmtAssignments = $this->db->prepare($sqlAssignments);
		if (!is_null($monthQ)) {
			$stmtAssignments->bindValue(":month", $month, PDO::PARAM_INT);
		}
		else {
			$monthView = false;
		}
		if (!is_null($yearQ)) {
			$stmtAssignments->bindValue(":year", $year, PDO::PARAM_INT);
		}
		else {
			$monthView = false;
		}
		if (!is_null($groupQ)) {
			$stmtAssignments->bindValue(":group", $groupQ, PDO::PARAM_INT);
			$monthView = false;
		}
		if (!is_null($meetingQ)) {
			$stmtAssignments->bindValue(":meeting", $meetingQ, PDO::PARAM_INT);
			$monthView = false;
		}
		// Execute the SQL statement and store the results
		if ($stmtAssignments->execute()) {
			$results=false;
			while ($rowAssignments = $stmtAssignments->fetch(PDO::FETCH_ASSOC)) {
				$rowsAssignments[] = $rowAssignments;
				$results=true;
			}
		}

		// Only do the following if the query was successful...
		if ($results) {

			// See if there are any incomplete meeting assignments (no groups)
			$complete = true;
			//$counter = 0;
			foreach($rowsAssignments as $row) {
				if (is_null($row['GroupID'])) {
					//echo '<script>alert("' . $counter . '");</script>';
					$complete = false;
				}
				//$counter++;
			}

			//echo '<script>alert("' . ($incomplete ? 'Missing Entries' : 'No missing entries'). '");</script>';

			// If editing assignments, query compatible groups for each meeting for the drop-down menu
			if ($edit) {
				$rowsMeetings = array();
				$sqlMeetings = "SELECT m.ID, DOW, BG, Gender FROM meetings m LEFT JOIN institutions i ON m.`Institution`=i.`ID`;";
				$stmtMeetings = $this->db->prepare($sqlMeetings);
				if ($stmtMeetings->execute()) {
					while ($rowMeetings = $stmtMeetings->fetch(PDO::FETCH_ASSOC)) {
						// Note that we're not just storing in sequence, but rather using an associative key to be able to
						// lookup the various meetings later on.
						$rowsMeetings[$rowMeetings['ID']] = $rowMeetings;
					}
				}

				// Query the compatible groups for each meeting
				// (Note the use of pass-by-referencing to be able to edit the contents of the array)
				foreach($rowsMeetings as &$rowMeetings) {
					// Set up groups SQL
					if ($rowMeetings['BG']) {
						$bgSQL = "AND BG ";
					}
					else {
						$bgSQL = "";
					}
					if ($rowMeetings['Gender']==1) {
						$genderSQL = "AND Gender<>2 ";
					}
					else if ($rowMeetings['Gender']==2) {
						$genderSQL = "AND Gender<>1 ";
					}
					else {
						$genderSQL = "";
					}
					// Note that we're also querying the date the group was last at the institution of a
					// given meeting in the first portion of the query.  This date cannot be the query month.
					// This is all for the user to be able to pick an appropriate group.
					$sqlGroups = "SELECT `ID` AS `gID`, `Name`, 
							(SELECT MAX(`Date`) 
							FROM assignments 
							WHERE `Meeting` IN 
							(SELECT `ID` FROM meetings WHERE `Institution` IN 
								(SELECT `Institution` 
									FROM meetings 
									WHERE `ID`=:meeting
								)
							)
							AND `Group`=`gID`
							AND NOT (MONTH(`Date`)=:month AND YEAR(`Date`)=:year)
							) AS `Date`
							FROM groups g
							WHERE Active 
							AND (DOW=127 OR DOW=62 OR NOT (DOW & :dow)) 
							" . $bgSQL . "
							" . $genderSQL . "
							ORDER BY `Name`
							;";
					$stmtGroups = $this->db->prepare($sqlGroups);
					$stmtGroups->bindValue(":meeting", $rowMeetings['ID'], PDO::PARAM_INT);
					$stmtGroups->bindValue(":month", $month, PDO::PARAM_INT);
					$stmtGroups->bindValue(":year", $year, PDO::PARAM_INT);
					$stmtGroups->bindValue(":dow", $rowMeetings['DOW'], PDO::PARAM_INT);
					// Store all the groups in the meetings array
					if ($stmtGroups->execute()) {
						while ($rowGroups = $stmtGroups->fetch(PDO::FETCH_ASSOC)) {
							// This line is why we need pass-by-reference
							$rowMeetings['Groups'][] = $rowGroups;
						}
					}
				}
			}
		}

		// START HTML
		include('header.php');

		// If editing vs. viewing, then there is different navigation buttons, titles, and javascript
		if ($edit) {
			// Header HTML (include tables.css)
			echo '
				<script type="text/javascript" src="' . $libloc . 'reload.js"></script>
				<script type="text/javascript" src="matchCheck.js"></script>';
			echo recaptcha::javascript();
			echo '
				<link rel="stylesheet" type="text/css" href="' . $libloc . 'tables.css">
				<title>Institution Committee - View Assignments</title>
			</head>
			<body>';

			// Start Body HTML, define table and table headers
			echo '
				<form name="form" id="form" action="load.php" method="post">';
			echo recaptcha::tokeninput();
			echo '
				<h1>Institution Assignments</h1>
				<p>
				<a class="button" href="../">Home</a>
				<a class="button" href="viewall.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=' . $sort . ' ">Back</a>';
			echo recaptcha::submitbutton('submit1', 'Save', 'submit', false, false);
			echo '
				</p>
				<input name="month" type="hidden" value="' . $monthQ . '">
				<input name="year" type="hidden" value="' . $yearQ . '">
				<input name="groupQ" type="hidden" value="' . $groupQ . '">
				<input name="meetingQ" type="hidden" value="' . $meetingQ . '">';
		}
		else {
			// Header HTML
			echo '
				<script type="text/javascript" src="' . $libloc . 'reload.js"></script>';
			echo recaptcha::javascript();
			echo '
				
				<link rel="stylesheet" type="text/css" href="' . $libloc . 'tables.css">
				<title>Institution Committee - Edit Assignments</title>
			</head>
			<body>';

			// Add option to automatically finish missing entries if in a one-month view of all groups and meetings
			if ($monthView && !$complete) {
				echo '
				<form name="form" action="fillform.php" method="post">';
			echo recaptcha::tokeninput();
			echo '
				<input type="hidden" name="month" value="' .  $month . '">
				<input type="hidden" name="year" value="' . $year . '">';
			}
			// Start Body HTML, define table and table headers
			echo '
				<h1>Institution Assignments</h1>
				<p>
				<a class="button" href="../">Home</a>
				<a class="button" href="viewform.php">Search</a>
				<a class="button" href="createform.php">Create</a>';
			if ($results) {
				echo '
					<a class="button" href="edit.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=' . $sort . '">Edit</a>
					<a class="button" href="export.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=' . $sort . '">Export</a>';
							// Add option to automatically finish missing entries if in a one-month view of all groups and meetings
				if ($monthView && !$complete) {
					echo recaptcha::submitbutton('submit1', 'Fill Missing Entries', 'submit', false, false);
					//echo '
					//<input type="submit" value="Fill missing entries" style="background-color:red;">';
				}
			}
			echo '
				</p>';
		}

		// Only do anything if there were results from the assignment query...
		if ($results) {
			// Start table
			echo '
				<table class="scroll">';
			// If editing, exclude sorting buttons in table header
			if ($edit) {
				echo '
					<thead>
						<tr class="header">
							<th>Date</th>
							<th>Meeting ID</th>
							<th>Day of Week</th>
							<th>Institution</th>
							<th>Group (last month assigned to institiution)</th>
							<th>Notes</th>
						</tr>
					</thead>
					<tbody>';
			}
			// If viewing, show sorting buttons in table header
			else {
				echo '
					<thead>
						<tr class="header">
							<th>Date
							<a style="text-decoration:none;" href="viewall.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=2">&#9650</a>
							<a style="text-decoration:none;" href="viewall.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=3">&#9660</a>
							</th>
							<th>Meeting ID
							<a style="text-decoration:none;" href="viewall.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=0">&#9650</a>
							<a style="text-decoration:none;" href="viewall.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=1">&#9660</a>
							</th>
							<th>Day of Week</th>
							<th>Institution
							<a style="text-decoration:none;" href="viewall.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=4">&#9650</a>
							<a style="text-decoration:none;" href="viewall.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=5">&#9660</a>
							</th>';
				if ($viewAddress) {
					echo '
							<th>Address</th>
					';
				}
				if ($viewPeople) {
					echo '
							<th>Sponsor</th>
							<th>Sponsor Phone</th>
							<th>Cosponsor</th>
							<th>Cosponsor Phone</th>
							<th>Cosponsor 2</th>
							<th>Cosponsor 2 Phone</th>';
				}
				echo '
							<th>Group</th>';
				if ($viewPeople) {
					echo '
							<th>Group Rep</th>
							<th>Rep Phone</th>
							<th>Group Rep 2</th>
							<th>Rep 2 Phone</th>';
				}
				echo '
							<th>Notes</th>
						</tr>
					</thead>';
			}

			// Count is for refering to HTML elements in form for editing (especially for matchCheck.js)
			if ($edit) {
				$count=0;
				$countMax = count($rowsAssignments);
			}

			// Run through assignments and display each table row
			foreach($rowsAssignments as $row) {

				// If group ID is null, then there was no selection, display special HTML later
				if (is_null($row['GroupID'])) {
					$noSelection = true;
					$sponsorsNight = false;
				}
				// If group ID is 0, then it is a sponsor's night, display special HTML later
				else if ($row['GroupID'] == 0) {
					$noSelection = false;
					$sponsorsNight = true;
				}
				// Otherwise, display normal HTML later
				else {
					$noSelection = false;
					$sponsorsNight = false;
				}

				// Grab data from assignment row
				$mdate = new mdate($row['Date']);
				$dow = new dow($row['DOW']);
				$assignment = $row['ID'];
				$group = $row['GroupID'];
				$groupName = $row['Group'];
				$meeting = $row['MeetingID'];

				// If editing, display pulldown menues
				if ($edit) {

					// Display date, meetingID, day of week, and institiution
					echo '
						<tr class="rowA">
							<td nowrap>' . $mdate->getFormatted() . '</td>
							<td nowrap>' . $row['DisplayID'] . '</td>
							<td nowrap>' . $dow->getFormatted() . '</td>
							<td nowrap>' . $row['Institution'] . '</td>';

					// NOTE: THE FOLLOWING STYLINGS ARE DONE BECAUSE YOU CANNOT FORMAT PULLDOWN MENU TEXT
					// If no selection made, display red
					if ($noSelection) {
						echo '
							<td style="background-color: red;">';
					}
					// If sponsor's night, display black
					else if ($sponsorsNight) {
						echo '
							<td style="background-color: black;">';
					}

					// Otherwise, no styling
					else {
						echo '
							<td>';
					}

					// Display hidden fields used for loading values in separate PHP file
					// ('assignment' field tracks which assignment it is, and 'changed' tracks which ones were changed)
					echo '
								<input name="assignment[' . $count . ']" type="hidden" value="' . $assignment . '">
								<input name="changed[' . $count . ']" type="hidden" value="0">';

					// DISPLAY PULLDOWN MENU (changes call a js function to check whether the change resulted in a conflict or not, and also 
					// marks the 'changed' hidden HTML)
					echo '
								<select style="width: 300px;" name="group[' . $count . ']" 
								onchange="document.forms[\'form\'][\'changed[' . $count . ']\'].value=1; matchCheck(' . $count . ', ' . $countMax . ');">';

					// No selection
					echo '
									<option value="" selected>***No group selected!***</option>';

					// Sponsor's night selection
					echo '
									<option value="0" ' . ($sponsorsNight ? ' selected' : '') . '>SPONSOR\'S NIGHT</option>';

					// Provide a selection for each of the meeting's compatible groups
					foreach($rowsMeetings[$row['MeetingID']]['Groups'] as $groups) {
						// Select the assignment's current group, display the group name along with the date that it was last at that meeting's institution
						echo '
									<option value="' . $groups['gID'] . '"' . (($group==$groups['gID']) ? ' selected' : '') . '>' . 
									$groups['Name'] . (is_null($groups['Date']) ? '' : ' (' . date('n/Y', strtotime($groups['Date'])) . ')') . '</option>';
					}

					// End the select statement, display notes field (note the js that sets the 'changed' field)
					echo '
								</select>
							</td>
							<td>
								<input name="notes[' . $count . ']" type="text" class="notes" value="' . $row['Notes'] . '" onchange="document.forms[\'form\'][\'changed[' . $count . ']\'].value=1;">
							</td>
						</tr>';
					$count++;
				}

				// If viewing, display without pulldown menus (note the links to the meetings and groups)
				else {
					$repPhone = new phone($row['RepPhone']);
					$rep2Phone = new phone($row['Rep2Phone']);
					$sponsorPhone = new phone($row['SponsorPhone']);
					$coSponsorPhone = new phone($row['CoSponsorPhone']);
					$coSponsor2Phone = new phone($row['CoSponsor2Phone']);
					echo '
						<tr class="rowA">
							<td nowrap>' . $mdate->getFormatted() . '</td>
							<td nowrap>' . ($viewLinks ? '<a href="../meetings/view.php?id=' . $meeting . '">' : '') . $row['DisplayID'] . 
								($viewLinks ? '</a>' : '') . '</td>
							<td nowrap>' . $dow->getFormatted() . '</td>
							<td nowrap>' . $row['Institution'] . '</td>';
					if ($viewAddress) {
						echo '
							<td nowrap>' . $row['Address'] . ', ' . $row['City'] . ', MD ' . $row['Zip'] . '</td>';
					}
					if ($viewPeople) {
						echo '
							<td nowrap>' . $row['SponsorName'] . ' ' . $row['SponsorInitial'] . '</td>
							<td nowrap>' . $sponsorPhone->getFormatted() . '</td>
							<td nowrap>' . $row['CoSponsorName'] . ' ' . $row['CoSponsorInitial'] . '</td>
							<td nowrap>' . $coSponsorPhone->getFormatted() . '</td>
							<td nowrap>' . $row['CoSponsor2Name'] . ' ' . $row['CoSponsor2Initial'] . '</td>
							<td nowrap>' . $coSponsor2Phone->getFormatted() . '</td>';
					}
					echo '
							<td nowrap>' . (($noSelection) ? '<font color="red"><b>No Selection Made!</b></font>' : 
								(($sponsorsNight) ? '<i>SPONSOR\'S NIGHT</i>' : ($viewLinks ? '<a href="../groups/view.php?id=' . 
								$group . '">' : ''). $groupName . ($viewLinks ? '</a>' : ''))) . '</td>';
					if ($viewPeople) {
						echo '
							<td nowrap>' . $row['RepName'] . ' ' . $row['RepInitial'] . '</td>
							<td nowrap>' . $repPhone->getFormatted() . '</td>
							<td nowrap>' . $row['Rep2Name'] . ' ' . $row['Rep2Initial'] . '</td>
							<td nowrap>' . $rep2Phone->getFormatted() . '</td>';
					}
					echo '
							<td nowrap>' . $row['Notes'] . '</td>
						</tr>';
				}
			}
			echo '
					</tbody>
					</table>';

			// Display different buttons at the end if editing vs. viewing
			if ($edit) {
				echo '
					<p>
					<a class="button" href="../">Home</a>
					<a class="button" href="viewall.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=' . $sort . ' ">Back</a>';
				echo recaptcha::submitbutton('submit2', 'Save', 'submit', false, false);
				echo '
					</p>
					</form>';
			}
			else {
				echo '
					<p>
					<a class="button" href="../">Home</a>
					<a class="button" href="viewform.php">Search</a>
					<a class="button" href="createform.php">Create</a>
					<a class="button" href="edit.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=' . $sort . '">Edit</a>
					<a class="button" href="export.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '&sort=' . $sort . '">Export</a>';
				if ($monthView && !$complete) {
					echo recaptcha::submitbutton('submit2', 'Fill missing entries', 'submit', false, false);
					//echo '
					//<input type="submit" value="Fill missing entries" style="background-color:red;">';
				}
				echo '
					</p>';
				if ($monthView && !$complete) {
					echo '
					</form>';
				}
			}

			// If we're viewing a whole month, then we should show the standby and probation groups
			if (is_null($groupQ) && is_null($meetingQ) && !is_null($monthQ)) {

				// Simply select all the active, non-probation groups that didn't get assignments that month
				$sqlStandby = "SELECT `ID`, `Name` 
						FROM groups 
						WHERE `ID`
						NOT IN 
							(SELECT DISTINCT `Group` 
							FROM assignments 
							WHERE `Group` IS NOT NULL 
							AND `Group` != 0 
							AND
							MONTH(`Date`)=:month
							AND
							YEAR(`Date`)=:year) 
						AND `Active`
						AND NOT `Probation` 
						ORDER BY `Name` ASC;";
				$stmtStandby = $this->db->prepare($sqlStandby);
				$stmtStandby->bindValue(":month", $month, PDO::PARAM_STR);
				$stmtStandby->bindValue(":year", $year, PDO::PARAM_STR);

				// Display all the groups found (might change so that the loading comes before the display, but this works for now)
				if ($stmtStandby->execute()) {
					if ($stmtStandby->rowCount() > 0) {
						echo '<h2>Standby Groups:</h2>';
						while ($rowStandby = $stmtStandby->fetch(PDO::FETCH_ASSOC)) {
							echo '<li><a href="../groups/view.php?id=' . $rowStandby['ID'] . '">' . $rowStandby['Name'] . '</a></li>';
						}
					}
				}

				// Do the same thing, but for the probation groups
				$sqlProbation = "SELECT `ID`, `Name` FROM groups WHERE `Active` AND `Probation` ORDER BY `Name` ASC";
				$stmtProbation = $this->db->prepare($sqlProbation);
				if ($stmtProbation->execute()) {
					if ($stmtProbation->rowCount() > 0) {
						echo '<h2>Groups On Probation:</h2>';
						while ($row = $stmtProbation->fetch(PDO::FETCH_ASSOC)) {
							echo '<li><a href="../groups/view.php?id=' . $row['ID'] . '">' . $row['Name'] . '</a></li>';
						}
					}
				}
			}
		}

		// If there were no assignments, then say so
		else {
			echo '<h2>Your search returned zero results!</h2>';
		}
		echo '
			</body>
			</html>';
	}

	// Function to export the data being viewed into a .tsv file for reports, etc.  Data is re-queried with specified 
	// parameters to include all data from the database (including rep and sponsor info) associated with an assignment
	public function export($monthQ, $yearQ, $groupQ, $meetingQ, $sort) {

		$alldates = false;

		// SET UP "SCOPE SQL (copied from assignments.php, yeah I know...)
		// create different date SQL statements based on whether month and/or year were specified
		if (is_null($monthQ) && is_null($yearQ)) {
			$alldates = true;
			$month = null;
			$year = null;
			$dateSQL = "WHERE `Date` IS NOT NULL ";
		}
		elseif (is_null($monthQ) && !is_null($yearQ)) {
			$month = null;
			$year = $yearQ;
			$dateSQL = "WHERE YEAR(`Date`)=:year ";
		}
		elseif (!is_null($monthQ) && is_null($yearQ)) {
			$month = $monthQ;
			$year = null;
			$dateSQL = "WHERE MONTH(`Date`)=:month ";
		}
		else {
			$month = $monthQ;
			$year = $yearQ;
			$dateSQL = "WHERE
				MONTH(`Date`)=:month
				AND
				YEAR(`Date`)=:year ";
		}
		// Set up group SQL if group is specified
		if (is_null($groupQ)) {
			$groupSQL = "";
		}
		else {
			$groupSQL = "AND a.`Group`=:group ";
		}
		// Set up meeting SQL if meeting is specified
		if (is_null($meetingQ)) {
			$meetingSQL = "";
		}
		else {
			$meetingSQL = "AND a.`Meeting`=:meeting ";
		}
		// Set up the various sort options, self explanatory.  Note the default.  (DisplayID = Meeting ID)
		switch($sort) {
			case 1: 
				$sortSQL = "ORDER BY `Meeting ID` DESC, `Date` DESC;";
				break;
			case 2:
				$sortSQL = "ORDER BY `Date` ASC, `Meeting ID` ASC;";
				break;
			case 3:
				$sortSQL = "ORDER BY `Date` DESC, `Meeting ID` DESC;";
				break;
			case 4:
				$sortSQL = "ORDER BY `Institution Name` ASC, `Meeting ID` ASC;";
				break;
			case 5:
				$sortSQL = "ORDER BY `Institution Name` DESC, `Date` DESC;";
				break;
			default:
				$sortSQL = "ORDER BY `Meeting ID` ASC, `Date` ASC;";
				break;
		}

		// Compile scop sql
		$scopeSQL = $dateSQL . 
				$groupSQL . 
				$meetingSQL . 
				$sortSQL;

		// SQL statement (includes all fields except ID fields associated with a given entry)
		$sql = "SELECT
			`a`.`ID` AS `Assignment Key`,
			`a`.`Date` AS `Date`,
			`m`.`ID` AS `Meeting Key`,
			`m`.`DisplayID` AS `Meeting ID`,
			`m`.`DOW` AS `Meeting DOW`,
			`m`.`Time` AS `Meeting Time`,
			`m`.`Gender` AS `Meeting Gender`,
			`m`.`NotesPublic` AS `Public Meeting Notes`,
			`m`.`NotesPrivate` AS `Private Meeting Notes`,
			`m`.`Active` AS  `Meeting Active`,
			`i`.`ID` AS `Institution Key`,
			`i`.`Name` AS `Institution Name`,
			`i`.`Address` AS `Institution Address`, 
			`i`.`City` AS `Institution City`,
			`i`.`Zip` AS `Institution Zip Code`,
			`i`.`BG` AS `Institution BG`,
			`i`.`NotesPublic` AS `Public Institution Notes`,
			`i`.`NotesPrivate` AS `Private Institution Notes`,
			`i`.`Active` AS `Institution Active`,
			`g`.`ID` AS `Group Key`,
			`g`.`Name` AS `Group Name`,
			`g`.`DOW` AS `Group DOW`,
			`g`.`Gender` AS `Group Gender`,
			`g`.`BG` AS `Group BG`,
			`g`.`Standby` AS `Group Standby`,
			`g`.`Notes` AS `Group Notes`,
			`g`.`Active` AS `Group Active`,
			`g`.`Probation` AS `Group Probation`,
			`s`.`ID` AS `Meeting Sponsor Key`,
			`s`.`Name` AS `Meeting Sponsor Name`,
			`s`.`Initial` AS `Meeting Sponsor Initial`,
			`s`.`Phone` AS `Meeting Sponsor Phone`,
			`s`.`Notes` AS `Meeting Sponsor Notes`,
			`s`.`Active` AS `Meeting Sponsor Active`,
			`cs`.`ID` AS `Meeting Co-Sponsor Key`,
			`cs`.`Name` AS `Meeting Co-Sponsor Name`,
			`cs`.`Initial` AS `Meeting Co-Sponsor Initial`,
			`cs`.`Phone` AS `Meeting Co-Sponsor Phone`,
			`cs`.`Notes` AS `Meeting Co-Sponsor Notes`,
			`cs`.`Active` AS `Meeting Co-Sponsor Active`,
			`cs2`.`ID` AS `Meeting Co-Sponsor #2 Key`,
			`cs2`.`Name` AS `Meeting Co-Sponsor #2 Name`,
			`cs2`.`Initial` AS `Meeting Co-Sponsor #2 Initial`,
			`cs2`.`Phone` AS `Meeting Co-Sponsor #2 Phone`,
			`cs2`.`Notes` AS `Meeting Co-Sponsor #2 Notes`,
			`cs2`.`Active` AS `Meeting Co-Sponsor #2 Active`,
			`r`.`ID` AS `Group Rep Key`,
			`r`.`Name` AS `Group Rep Name`,
			`r`.`Initial` AS `Group Rep Initial`,
			`r`.`Phone` AS `Group Rep Phone`,
			`r`.`Notes` AS `Group Rep Notes`,
			`r`.`Active` AS `Group Rep Active`,
			`r2`.`ID` AS `Group Rep #2 Key`,
			`r2`.`Name` AS `Group Rep #2 Name`,
			`r2`.`Initial` AS `Group Rep #2 Initial`,
			`r2`.`Phone` AS `Group Rep #2 Phone`,
			`r2`.`Notes` AS `Group Rep #2 Notes`,
			`r2`.`Active` AS `Group #2 Rep Active`
			FROM `assignments` `a` 
			LEFT JOIN `meetings` `m` ON `a`.`Meeting`=`m`.`ID` 
			LEFT JOIN `institutions` `i` ON `m`.`Institution`=`i`.`ID`
			LEFT JOIN `groups` `g` ON `a`.`Group`=`g`.`ID` 
			LEFT JOIN `people` `s` ON `m`.`Sponsor`=`s`.`ID` 
			LEFT JOIN `people` `cs` ON `m`.`CoSponsor`=`cs`.`ID` 
			LEFT JOIN `people` `cs2` ON `m`.`CoSponsor2`=`cs2`.`ID` 
			LEFT JOIN `people` `r` ON `g`.`Rep` = `r`.`ID` 
			LEFT JOIN `people` `r2` ON `g`.`Rep2` = `r2`.`ID`" .
			$scopeSQL;


		$stmt = $this->db->prepare($sql);

		if (!is_null($monthQ)) {
			$stmt->bindValue(":month", $month, PDO::PARAM_INT);
		}
		if (!is_null($yearQ)) {
			$stmt->bindValue(":year", $year, PDO::PARAM_INT);
		}
		if (!is_null($groupQ)) {
			$stmt->bindValue(":group", $groupQ, PDO::PARAM_INT);
		}
		if (!is_null($meetingQ)) {
			$stmt->bindValue(":meeting", $meetingQ, PDO::PARAM_INT);
		}

		// Compile export file as string
		$tsv_export = '';

		if ($stmt->execute()) {
			$numColumns = $stmt->columnCount();
			if ($stmt->rowCount() > 0) {
				// Grab field names
				for ($i = 0; $i < $numColumns; $i++) {
					$tsv_export.= $stmt->getColumnMeta($i)['name']."\t";
				}
				$tsv_export.= "\n";

				// Grab data
				while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
					for ($i = 0; $i < $numColumns; $i++) {
						$tsv_export.= $row[$i]."\t";
					}
					$tsv_export.= "\n";
				}
			}
		}



		// Set up filename

		if ($alldates) {
			if(is_null($groupQ) && is_null($meetingQ)) {
				$filenameDate = ' - All assignments';
			}
			else {
				$filenameDate = ' - All dates';
			}
		}
		elseif (is_null($monthQ)) {
			$filenameDate = ' - ' . $yearQ;
		}
		elseif (is_null($yearQ)) {
			$dateObj   = DateTime::createFromFormat('!m', $monthQ);
			$monthName = $dateObj->format('F');
			$filenameDate = ' - ' . $monthName . ', all years';
		}
		else {
			$filenameDate = ' - ' . $yearQ . '-' . (strlen($monthQ) == 1 ? ('0' . $monthQ) : $monthQ);
		}
		$filenameGroup = '';
		if (!is_null($groupQ)) {
			$sqlGroup = "SELECT `Name` FROM `groups` WHERE `ID`=:group;";
			$stmtGroup = $this->db->prepare($sqlGroup); 
			$stmtGroup->bindValue(":group", $groupQ, PDO::PARAM_INT);
			if ($stmtGroup->execute()) {
				if ($stmtGroup->rowCount() > 0) {
					$rowGroup = $stmtGroup->fetch(PDO::FETCH_ASSOC);
					$filenameGroup .= ' - ' . $rowGroup['Name'];
				}
			}
		}
		$filenameMeeting = '';
		if (!is_null($meetingQ)) {
			$sqlMeeting = "SELECT `DisplayID` FROM `meetings` WHERE `ID`=:meeting;";
			$stmtMeeting = $this->db->prepare($sqlMeeting);
			$stmtMeeting->bindValue(":meeting", $meetingQ, PDO::PARAM_INT);
			if ($stmtMeeting->execute()) {
				if ($stmtMeeting->rowCount() > 0) {
					$rowMeeting = $stmtMeeting->fetch(PDO::FETCH_ASSOC);
					$filenameMeeting .= ' - Meeting ' . $rowMeeting['DisplayID'];
				}
			}
		}

		$filename = $filenameGroup . $filenameMeeting . $filenameDate;

		date_default_timezone_set('US/Eastern');
		$timeStamp = date('YmdHis');

		// Export the data and prompt a tsv file for download
		header('Content-type: text/plain');
		header('Content-Disposition: attachment; filename="Balt AA Inst Comm Mtg Mgr - Export ' . $timeStamp . $filename . '.tsv"');
		echo($tsv_export);
	}
}

?>
