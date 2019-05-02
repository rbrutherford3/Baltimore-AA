<?php

// Form for viewing assignments, pretty straight-forward

include '../../lib/header.php';
include '../../lib/dbconnect.php';
echo '
	<title>Institution Committee - Assignment Search Tool</title>
	</head>
	<body>';

echo '
	<form action="viewall.php" method="get">
	<h1>Assignment Search Tool</h1>';

// Get maximum and minimum years of assignments
$sqlMonth = "SELECT YEAR(MIN(`Date`)) AS `MinYear`, YEAR(MAX(`Date`)) AS `MaxYear` FROM assignments;";
$stmtMonth = $db->prepare($sqlMonth);
if ($stmtMonth->execute()) {
	if ($rowMonth = $stmtMonth->fetch(PDO::FETCH_ASSOC)) {
		$MinYear = $rowMonth['MinYear'];
		$MaxYear = $rowMonth['MaxYear'];
	}
}

// Select month & year for viewing assignments, default all dates
echo '
	<h2>Select month for viewing assignments:</h2>
	<p>
		<select name="month">
		<option value="" selected>***ALL MONTHS***</option>';
for ($i=1; $i<=12; $i++) {
	$di = DateTime::createFromFormat('n', $i);
	echo '
			<option value="' . $i . '">' . $di->format('F') . '</option>';
}
echo '
		</select>
		<select name="year">
		<option value="" selected>**ALL YEARS***</option>';
for($i=$MinYear; $i<=$MaxYear; $i++) {
	echo '
			<option value="' . $i . '">' . $i . '</option>';
}
echo '
		</select>
	</p>
	<br>';

// Get all meetings, active or not
$sqlMeeting = "SELECT m.`ID`, `DisplayID`, `Name` FROM meetings m LEFT JOIN institutions i ON m.Institution=i.ID WHERE 1 ORDER BY `DisplayID`;";
$stmtMeeting = $db->prepare($sqlMeeting);
if ($stmtMeeting->execute()) {
	while ($rowMeeting = $stmtMeeting->fetch(PDO::FETCH_ASSOC)) {
		$rowsMeeting[] = $rowMeeting;
	}
}

// Form HTML input for meetings
echo '
	<h2>Select meeting:</h2>
	<p>
	<select name="meeting">
		<option value="">***ALL MEETINGS***</option>';
foreach($rowsMeeting as $row) {
	echo '
		<option value="' . $row['ID'] . '">' . $row['DisplayID'] . ' (' . $row['Name'] . ')' . '</option>';
}
echo '
	</select>
	</p>
	<br>';

// Get all groups, active or not
$sqlGroup = "SELECT `ID`, `Name` FROM groups WHERE 1 ORDER BY `Name`;";
$stmtGroup = $db->prepare($sqlGroup);
if ($stmtGroup->execute()) {
	while ($rowGroup = $stmtGroup->fetch(PDO::FETCH_ASSOC)) {
		$rowsGroup[] = $rowGroup;
	}
}
	
echo '
	<h2>Select group:</h2>
	<p>
	<select name="group">
		<option value="">***ALL GROUPS***</option>';
foreach($rowsGroup as $row) {
	echo '
		<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
}
echo '
	</select>
	</p>
	<br>';
	
// Navigation buttons
echo '
	<p>
	<a class="button" href="../">Home</a>
	<input type="submit" value="Submit">
	</p>
	</form>
</body>
</html>';	

?>