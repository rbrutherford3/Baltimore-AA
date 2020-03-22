<?php

// Navigation page for Baltimore AA meetings to view their assignments.  Simple

include_once '../lib/dbconnect.php';
include_once '../lib/datatypes.php';

// Grab all active groups
$sql = "SELECT m.`ID`, m.`DisplayID`, i.`Name`, m.`DOW`, m.`Time`, m.`Gender`
FROM meetings m
LEFT JOIN institutions i ON i.`ID`=m.`Institution`
WHERE m.`ACTIVE`=1
ORDER BY m.`DisplayID` ASC;";
$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$row["DOW"] = new dow($row["DOW"]);
	$row["Gender"] = new gender($row["Gender"]);
	$row["Time"] = new mtime($row["Time"]);
	$rows[] = $row;
}

// Header HTML, etc
echo '
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../lib/main.css">
	<link rel="icon" type="image/png" href="favicon.ico">
	<title>Baltimore AA Institution Committee Schedule</title>
</head>
<body>
<h1>
Please select your institution meeting
</h1>
<a class="button" href="../index.html">
Home
</a>
<h2>
Institution Meetings
</h2>';

// List all groups and links to view their most recent assignments
foreach($rows as $row) {
	echo '
<p>
	<a href="meeting.php?id=' . $row["ID"] . '">' . $row["DisplayID"] . ' (' . 
		$row["DOW"]->getFormatted() . 	', ' . $row["Time"]->getFormatted() . ' at ' . 
		$row["Name"] . ', ' . $row["Gender"]->getFormatted() . ')
	</a>
</p>';	
		
}

echo '
</body>
</html>';

?>
