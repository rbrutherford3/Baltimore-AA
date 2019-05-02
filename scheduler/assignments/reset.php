<?php
include_once '../../lib/dbconnect.php';
include_once '../../lib/header.php';
echo '
	<title>Institution Committee - Loading...</title>
	</head>
	<body>
	Loading...';
	
$csv = array_map('str_getcsv', file('history.csv'));
//echo $csv[0][1];

$sql = "DELETE FROM assignments WHERE 1;";
$stmt = $db->prepare($sql);
$stmt->execute();
//echo 'CLEARED ASSIGNMENT TABLE<BR>';
$sql = "ALTER TABLE assignments AUTO_INCREMENT=1;";
$stmt = $db->prepare($sql);
$stmt->execute();
//echo 'RESET AUTO_INCREMENT ON ASSIGNMENT TABLE<BR>';

for ($i=1; $i<count($csv); $i++) {
	for ($j=1; $j<count($csv[0]); $j++) {
		$groupID = (int)$csv[$i][0];
		$date = date_create_from_format('Y/m',$csv[0][$j]);
		//echo $date->format('n/y') . '<br>';
		$assignment = $csv[$i][$j];
		
		// If there is an 'x' next to the number, then grab the number and put the 'x' in tht notes
		if (substr($assignment, -1, 1) == 'x') {
			$assignment = substr($assignment, 0, -1);
			$notes = 'x';
		}
		else {
			$notes = null;
		}
		if (is_numeric($assignment)) {
			$assignment = (int)$assignment;
			
			$sql = "SELECT ID from meetings WHERE DisplayID=:DisplayID;";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(":DisplayID", $assignment);
			$stmt->execute();
			if ($row = $stmt->fetch()) {
				$assigned = true;
				$assignment2 = (int)$row['ID'];
			}
			else {
				$assigned=false;
				//echo 'ERROR GETTING MEETING ID FOR ' . $assignment . '<br>';
			}
		}
		else {
			$assigned = false;
		}
		if ($assigned) {
			$sql = "INSERT INTO assignments (`Date`, `Meeting`, `Group`, `Notes`) VALUES (:Date, :Meeting, :Group, :Notes);";
			$stmt = $db->prepare($sql);
			$stmt->bindValue(":Date", $date->format('Y-m') . '-00', PDO::PARAM_STR);
			$stmt->bindValue(":Meeting", $assignment2, PDO::PARAM_INT);
			$stmt->bindValue(":Group", $groupID, PDO::PARAM_INT);
			$stmt->bindValue(":Notes", $notes, PDO::PARAM_STR);
			$stmt->execute();

			
			//echo 'Group ID ' . $groupID . ' was assigned to meeting ' . $assignment . ' (' . $assignment2 . ')on date ' . $date->format('n/y') . '<br>';
		}
		
		//echo $dates[$i-1]->format('n/y') . '<br>';
	}
}

echo '
	<script>
	alert("Successfully reset assignments table");
	window.location.href = "viewall.php";
	</script>';

echo '</body>';
echo '</html>';	

?>