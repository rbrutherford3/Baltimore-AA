<?php
/* assignments/create.php
Creates sponsor's nights using given criteria.  Lots of SQL.
*/

include_once '../lib/dbconnect.php';
include_once '../lib/group.php';
include_once '../lib/header.php';

set_time_limit(120);

echo '
	<title>Institution Committee - Loading...</title>
	<head></head>
	<body>
	Loading...
	</body>';
	
date_default_timezone_set('America/New_York');

$dow = new dow();

// Grab month for creating assignments, set boundaries for beginning and end dates
$monthYear = $_POST['monthyear'];
//$year = $_POST['year'];
$createMonthYear = new DateTime($monthYear);
$createMonth = (int)$createMonthYear->format('n');
$createYear = (int)$createMonthYear->format('Y');
//echo '<script>alert("' . $createYear . '");</script>;';
$begin = new DateTime($monthYear . '-01');
$end = clone $begin;
$end->modify('+1 month');
$end->modify('-1 day');
$end->setTime(0,0,1);

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

// Forward to the editing page
echo '
<script>
	alert("Manually make any preliminary changes and save, then click the red button to automatatically assign the remainder");
	window.location = "edit.php?month=' . $createMonthYear->format('n') . '&year=' . $createMonthYear->format('Y') . '";
</script>';
?>