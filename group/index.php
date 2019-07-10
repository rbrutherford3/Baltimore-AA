<?php

// Navigation page for Baltimore AA groups to view their assignments.  Simple

include_once '../lib/dbconnect.php';

// Grab all active groups
$sql = "SELECT ID, Name FROM groups WHERE ACTIVE=1 ORDER BY Name ASC;";
$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$rows[] = $row;
}

// Header HTML, etc
echo '
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/lib/main.css">
	<link rel="icon" type="image/png" href="favicon.ico">
	<title>Baltimore AA Institution Committee Schedule</title>
</head>
<body>
<h1>Please select your home group</h1>';

// List all groups and links to view their most recent assignments
foreach($rows as $row) {
	echo '<p><a href="group.php?id=' . $row["ID"] . '">' . $row["Name"] . '</a></p>';
}

echo '
</body>
</html>';

?>