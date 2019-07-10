<?php

// PHP file to allow a group to view a meeting assignment for any given month.  
// Navigation is based on number of months since most recent assignment entry

include_once '../lib/dbconnect.php';
include_once '../lib/header.php';
include_once '../lib/group.php';

// Grab parameters from URL (group ID & month relative to latest assignment entry)
$id = (int)$_GET["id"];

if (empty($_GET["month"])) {
    $month = 0;
}
else {
	$month = $_GET["month"];
}

// Get meeting Display ID
$sql = "SELECT `DisplayID` FROM meetings WHERE `ID`=:id LIMIT 1;";
$stmt = $db->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$meetingID = $row["DisplayID"];

// Get dates (find highest date from assignments table, take month & year, go back X months)
$sqlMonth = "SELECT MAX(`Date`) AS `MaxDate` FROM assignments;";
$stmtMonth = $db->prepare($sqlMonth);
$stmtMonth->execute();
$rowMonth = $stmtMonth->fetch(PDO::FETCH_ASSOC);
//echo $rowMonth['Max_Date'] . '<br>';
$maxDate = new DateTime($rowMonth['MaxDate']);
$maxMonth = (int)$maxDate->format('n');
$maxYear = (int)$maxDate->format('Y');
$maxDateFirst = new DateTime($maxYear . '-' . $maxMonth . '-01');
$dateMin = clone $maxDateFirst;
$dateMin->modify('-' . $month . ' month');
$monthQ = (int)$dateMin->format('n');
$yearQ = (int)$dateMin->format('Y');

// For navigation buttons
$prevMonth = $month+1;
$nextMonth = $month-1;

// Find meeting assignment for given group and month
$sqlAssignments = "SELECT a.`Date`, 
g.`ID` AS `ID` 
FROM assignments a
LEFT JOIN groups g ON g.`ID`=a.`Group` 
WHERE a.`Meeting`=:id 
AND MONTH(a.`Date`)=:month 
AND YEAR(a.`Date`)=:year";
$stmtAssignments = $db->prepare($sqlAssignments);
$stmtAssignments->bindValue(":id", $id, PDO::PARAM_INT);
$stmtAssignments->bindValue(":month", $monthQ, PDO::PARAM_INT);
$stmtAssignments->bindValue(":year", $yearQ, PDO::PARAM_INT);
$numRows=0;
if ($stmtAssignments->execute()) {
	while ($row = $stmtAssignments->fetch(PDO::FETCH_ASSOC)) {
		$rows[] = $row;
		$numRows++;
	}
}
else {
	exit("Query failed");
}

// Output HTML, header, nav buttons, etc.
echo '
	<title>Institution Committee - Meeting #' . $meetingID . ', ' . $dateMin->format('M Y') . '</title>
</head>
<body>
<h1>Meeting #' . $meetingID . ' - ' . $dateMin->format('F, Y') . ' commitments</h1>
<a class="button" href="meeting.php?id=' . $id . '&month=' . $prevMonth . '">Previous Month</a>
<a class="button" href="index.php">Meetings</a>';

// Don't allow "forward" navigation when on highest month
if ($month > 0) {
	echo '
<a class="button" href="meeting.php?id=' . $id . '&month=' . $nextMonth . '">Next Month</a>';
}

// Output meeting information for each assignment that month (hopefully only one)
if ($numRows > 0) {
	foreach($rows as $row) {
		$dateAssignment = new DateTime($row["Date"]);
		echo '<hr>';
		echo '<h1>' . $dateAssignment->format('F j, Y') . '</h1>';
		if ($row["ID"] == 0 ) {
			echo '<h2>Sponsor&#39;s Night</h2>';
		}
		else {
			$group = new group($db, $row["ID"]);
			$group->view();
			$group->rep->view();
			$group->rep2->view();
			$group->outputHTML(true);
		}
	}
	
	// Button navigation buttons
	echo '
<hr>
<br>
<a class="button" href="meeting.php?id=' . $id . '&month=' . $prevMonth . '">Previous Month</a>
<a class="button" href="index.php">Meetings</a>';

	// Don't allow "forward" navigation when on highest month
	if ($month > 0) {
		echo '
<a class="button" href="meeting.php?id=' . $id . '&month=' . $nextMonth . '">Next Month</a>';
	}
}
else {
	echo '<h2>NO ASSSIGNMENTS!</h2>';
}

echo '
</body>
</html>';

?>