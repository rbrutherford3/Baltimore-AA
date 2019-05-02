<?php

include_once '../../lib/datatypes.php';
include_once '../../lib/dbconnect.php';
include_once '../../lib/header.php';
echo '
	<title>Institution Committee - Loading...</title>
	</head>
	<body>
	Loading...';

	
// Grab parameters from previous page
if (!isset($_POST['month']) || empty($_POST['month'])) {
	$monthQ = null;
}
else {
	$monthQ = $_POST['month'];
}
if (!isset($_POST['year']) || empty($_POST['year'])) {
	$yearQ = null;
}
else {
	$yearQ = $_POST['year'];
}
if (!isset($_POST['groupQ']) || empty($_POST['groupQ'])) {
	$groupQ = null;
}
else {
	$groupQ = $_POST['groupQ'];
}
if (!isset($_POST['meetingQ']) || empty($_POST['meetingQ'])) {
	$meetingQ = null;
}
else {
	$meetingQ = $_POST['meetingQ'];
}

$assignment = $_POST['assignment'];
$changed = $_POST['changed'];
$group = $_POST['group'];
$notes = $_POST['notes'];

// Loop through assignments and update if changed
for($i=0; $i<count($assignment); $i++) {
	if($changed[$i]) {
		$newnotes = new notes($notes[$i]);
		$sql = "UPDATE assignments SET `Group`=:group, `Notes`:=:notes WHERE `ID`=:assignment;";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(":group", ($group[$i]=='' ? null : $group[$i]), PDO::PARAM_INT);
		$stmt->bindValue(":notes", $newnotes->getValue(), PDO::PARAM_STR);
		$stmt->bindValue(":assignment", $assignment[$i], PDO::PARAM_INT);
		if (!$stmt->execute()) {
			echo '<script>alert("Changing assignment failed!");</script>';
		}
	}
}

// Send back to view page
echo '
	<script>
		window.location.href = "viewall.php?month=' . $monthQ . '&year=' . $yearQ . '&group=' . $groupQ . '&meeting=' . $meetingQ . '";
	</script>';

echo '</body>';
echo '</html>';	
?>