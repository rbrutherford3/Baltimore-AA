<?php
/* assignments/create.php
This is the meat of the program and automatically creates random assignmetns with 
the given criteria for the given month.  Lots of SQL.
*/

// $timeStart = microtime();

include_once '../lib/dbconnect.php';
include_once '../lib/group.php';
include_once '../lib/header.php';

set_time_limit(120);

echo '
	<title>Institution Committee - Loading...</title>
	</head>
	<body>
	Loading...';
	
date_default_timezone_set('America/New_York');

$dow = new dow();

// Grab month for creating assignments, set boundaries for beginning and end dates
$month = $_POST['month'];
$year = $_POST['year'];
$createMonthYear = new DateTime($year . '-' . $month);
$createMonth = (int)$createMonthYear->format('n');
$createYear = (int)$createMonthYear->format('Y');
$begin = new DateTime($year . '-' . $month . '-01');
$end = clone $begin;
$end->modify('+1 month');
$end->modify('-1 day');
$end->setTime(0,0,1);

/* $sql = "SELECT * FROM assignments WHERE (Month(`Date`)=:month AND Year(`Date`)=:year);";
$stmt = $db->prepare($sql);
$stmt->bindValue(":month", $createMonth, PDO::PARAM_INT);
$stmt->bindValue(":year", $createYear, PDO::PARAM_INT);
if ($stmt->execute()) {
	if ($stmt->rowCount() > 0) {
		echo '<script>
			if(confirm("Assignments exist for this month!  Click \'Ok\' to overwrite existing entries"))
			{
			}
			else 
			{
				window.location.href = "viewall.php?month=' . $month . '&year=' . $year . '&meeting=&group=";
			}
			</script>';
	}
} */

// Grab institutions number (this many meetings with the same institution or more
// automatically get sponsors' nights)
$institutions = $_POST['institutions'];

// Grab history (number of months to look back at the whether a group has been to
// the same institution or not)
$history = $_POST['history'];

// Grab cutoff date (meetings before this date automatically get sponsors' nights)
$cutoff = $_POST['cutoff'];

// Clear the assignments for this period
$sql = "DELETE FROM assignments WHERE (Month(`Date`)=:month AND Year(`Date`)=:year);";
$stmt = $db->prepare($sql);
$stmt->bindValue(":month", $createMonth, PDO::PARAM_INT);
$stmt->bindValue(":year", $createYear, PDO::PARAM_INT);
$stmt->execute();

// Set variables for iterating through month
$interval = new DateInterval('P1D');
$period = new DatePeriod($begin, $interval, $end);
$count = 0;

// Go through month and create all [blank] assignments and sponsors' nights
foreach ($period as $dt) {
	$count++;
	$dSQL = date_format($dt, "Y-m-d");
	// $dPrint = date_format($dt, "n/j/Y");
	// echo '<b>' . $dPrint . '</b><br>';
	
	// Get all active meetings on the given day of the week
	$dowN = $dow->getNumber(date_format($dt, 'w'));
	$sqlMeetings = "SELECT m.ID FROM meetings m LEFT JOIN institutions i ON m.Institution=i.ID WHERE (m.DOW & :DOW) AND m.Active;";
	$stmtMeetings = $db->prepare($sqlMeetings);
	$stmtMeetings->bindValue(":DOW", $dowN, PDO::PARAM_INT);
	
	// Loop through the meetings
	if ($stmtMeetings->execute()) {
		while ($rowMeetings = $stmtMeetings->fetch(PDO::FETCH_ASSOC)) {
			// echo '&nbsp&nbsp&nbsp&nbsp<b>' . $row['DisplayID'] . '</b><br>';
			
			// If before the cutoff date, then assign as sponsor's night
			if (date_format($dt, 'd') < $cutoff) {
				$group = 0;
				// echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
				// echo '<i>NOT ASSIGNING BASED ON BEGINNING OF THE MONTH</i><br>';
			}
			
			// If within the first week, assign as sponsor's night if meeting's institution has more
			// than the "threshold" for assigning a sponsor's night
			else if (date_format($dt, 'd') <=7 ) {
				$sqlInstitutions = "SELECT `ID` 
									FROM meetings 
									WHERE Active
									AND `Institution`=
									(SELECT `Institution` 
									FROM meetings 
									WHERE ID=:ID);";
				$stmtInstitutions = $db->prepare($sqlInstitutions);
				$stmtInstitutions->bindValue(":ID", $rowMeetings['ID'], PDO::PARAM_INT);
				$stmtInstitutions->execute();
				if ($stmtInstitutions->rowCount() >= $institutions) {
					$group = 0;
					// echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
					// echo '<i>NOT ASSIGNING BASED ON NUMBER OF INSTITUTIONS</i><br>';
				}
				else {
					$group = null;
				}
			}
			
			// If none of the above, then a group will be assigned
			else {
				$group = null;
			}
			
			$sqlAssignment = "INSERT INTO assignments (`Date`, Meeting, `Group`) VALUES (:Date, :Meeting, :Group);";
			$stmtAssignment = $db->prepare($sqlAssignment);
			$stmtAssignment->bindValue(":Date", $dSQL, PDO::PARAM_STR);
			$stmtAssignment->bindValue(":Meeting", $rowMeetings['ID'], PDO::PARAM_INT);
			$stmtAssignment->bindValue(":Group", $group, PDO::PARAM_INT);
			$stmtAssignment->execute();
		}
	}
}

// If manual entry was opted, then simply forward to the editing page
if (isset($_POST['manual'])) {
	echo '
	<script>
	window.location = "edit.php?month=' . $month . '&year=' . $year . '&group=&meeting=";
	</script>';
}

// Otherwise, we're randomly assigning!
else {
	/* Grab all the meetings just created and order them randomly sort them.
	Include a count of the number of meetings that an assignment's meeting's
	institution actively has (whew...) and order it by that (most first).
	--> The reason for this is because we're trying to assign groups that haven't
	been to that institution in the last X months, and so we need to consider
	institutions with the most meetings first, otherwise (discovered through
	trial and error), we run out of groups!
	*/
	$sqlAssignments = "SELECT a.`ID`, m.`ID` AS `MeetingID`, i.`BG`, m.`Gender`, m.`DOW`
					FROM assignments a
					LEFT JOIN meetings m
					ON a.`Meeting`=m.`ID`
					LEFT JOIN institutions i
					ON m.`Institution`=i.`ID`
					LEFT JOIN
					meetings mm
					ON i.ID=mm.Institution
					WHERE (MONTH(`Date`)=:month AND YEAR(`Date`)=:year)
					AND `Group` IS NULL 
					AND mm.Active 
					GROUP BY a.ID, m.ID, i.ID
					ORDER BY COUNT(mm.ID) DESC, RAND();";
	$stmtAssignments = $db->prepare($sqlAssignments);
	$stmtAssignments->bindValue(":month", $createMonth, PDO::PARAM_INT);
	$stmtAssignments->bindValue(":year", $createYear, PDO::PARAM_INT);
	
	// Execute query and grab results
	if ($stmtAssignments->execute()) {
		while ($rowAssignments = $stmtAssignments->fetch(PDO::FETCH_ASSOC)) {
			$assignment = $rowAssignments['ID'];
			$meeting = $rowAssignments['MeetingID'];
			$bg = $rowAssignments['BG'];
			$gender = $rowAssignments['Gender'];
			$dow = new dow($rowAssignments['DOW']);
			$dowN = $dow->getValue();
			
			// Set up Groups SQL
			if ($bg) {
				$bgSQL = "AND BG ";
			}
			else {
				$bgSQL = "";
			}
			if ($gender == 1) {
				$genderSQL = "AND Gender<>2 ";
			}
			else if ($gender == 2) {
				$genderSQL = "AND Gender<>1 ";
			}
			else {
				$genderSQL = "";
			}
			$found = false;
			
			// Set up previous month (to prioritize based on who was last on standby)
			$pastMonthYear = clone $createMonthYear;
			$pastMonthYear->modify('-1 months');
			$pastMonth = (int)$pastMonthYear->format('n');
			$pastYear = (int)$pastMonthYear->format('Y');
			
			// Loop back through time until a qualifying group is found
			// (back in time --> search through last month's standbys, then the previous months, etc.)
			$count = 0;
			do {
				$count++;
				
				// The crux of this query is select a group at random that is compatible with the meeting
				// (in terms of background check, gender, and day of week) that was not already assigned for
				// this month, or the 'standby' month in question (goes farther back each loop)
				$sqlGroups = "SELECT `ID` FROM groups 
						WHERE `ID` NOT IN
							(SELECT DISTINCT `Group` FROM assignments WHERE `Meeting` IN
								(SELECT `ID` FROM meetings WHERE `Institution` IN
									(SELECT `Institution` 
									FROM meetings 
									WHERE `ID`=:meetingID)
								)
							AND `Date` > DATE_SUB(:end0, INTERVAL :history MONTH)
							AND `Group` IS NOT NULL)
						AND `ID` NOT IN 
							(SELECT DISTINCT `Group`
								FROM assignments 
								WHERE 
								((MONTH(`Date`)=:createMonth AND YEAR(`Date`)=:createYear)
								OR 
								(MONTH(`Date`)=:pastMonth AND YEAR(`Date`)=:pastYear))
								AND `Group` IS NOT NULL
							)
						AND Active 
						AND NOT Probation 
						AND (DOW=127 OR DOW=62 OR NOT (DOW & :dow)) 
						" . $bgSQL . "
						" . $genderSQL . "
						ORDER BY RAND()
						LIMIT 1;";

				$stmtGroups = $db->prepare($sqlGroups);
				$stmtGroups->bindValue(":end0", date_format($end, 'Y-m-d'), PDO::PARAM_STR);
				$stmtGroups->bindValue(":history", $history, PDO::PARAM_INT);
				$stmtGroups->bindValue(":meetingID", $meeting, PDO::PARAM_INT);
				$stmtGroups->bindValue(":createMonth", $createMonth, PDO::PARAM_INT);
				$stmtGroups->bindValue(":createYear", $createYear, PDO::PARAM_INT);
				$stmtGroups->bindValue(":pastMonth", $pastMonth, PDO::PARAM_INT);
				$stmtGroups->bindValue(":pastYear", $pastYear, PDO::PARAM_INT);
				$stmtGroups->bindValue(":dow", $dowN, PDO::PARAM_INT);
				$stmtGroups->execute();

				// If a group was found, then record it and break out of the loop
				if ($rowGroups = $stmtGroups->fetch(PDO::FETCH_ASSOC)) {
					$group = $rowGroups['ID'];
					$found = true;
				}
				
				// If a group wasn't found, search a month further back
				else {
					
					// Don't go back further than 12 months (means something is wrong)
					if ($count > 12) {
						//echo '<b><i>FAILED TO FIND MATCHING MEETING!!!</b></i><br>';
						$found = true;
						$group = null;
					}
					$pastMonthYear->modify('-1 months');
					$pastMonth = (int)$pastMonthYear->format('n');
					$pastYear = (int)$pastMonthYear->format('Y');
				}
			} while (!$found);	// loop until matching group is found or count of 12 is met or exceeded

			
			// Update the assignment with the matching group!
			$sqlAssignment = "UPDATE assignments SET `Group`=:group WHERE `ID`=:assignment;";
			$stmtAssignment = $db->prepare($sqlAssignment);
			$stmtAssignment->bindValue(":group", $group, PDO::PARAM_INT);
			$stmtAssignment->bindValue(":assignment", $assignment, PDO::PARAM_INT);
			$stmtAssignment->execute();
		}
	}
	
	// Forward to the page to view
	echo '
		<script>
		window.location = "viewall.php?month=' . $month . '&year=' . $year . '";
		</script>';
}

	
?>