<?php
/* assignments/fill.php
This is the meat of the program and automatically creates random assignmetns with 
the given criteria for the given month.  Lots of SQL.
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/group.php';
include_once '../../lib/header.php';
include_once '../../lib/recaptcha.php';

$db = database::connect();

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	recaptcha::verify(false);
}

set_time_limit(120);

echo '
	<title>Institution Committee - Loading...</title>
	<head></head>
	<body>
	Loading...
	</body>';

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

// Grab history (number of months to look back at the whether a group has been to
// the same institution or not)
$history = $_POST['history'];

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
$stmtAssignments->bindValue(":month", $month, PDO::PARAM_INT);
$stmtAssignments->bindValue(":year", $year, PDO::PARAM_INT);

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
							MONTH(`Date`)=:createMonth
							AND
							YEAR(`Date`)=:createYear
							AND `Group`IS NOT NULL
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

?>