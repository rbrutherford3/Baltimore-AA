<?php

// Form for creating new assignments, starting with sponsor's nights.  All the inputs are explained in the HTML text.

include_once '../../lib/header.php';
include_once '../../lib/dbconnect.php';
include_once '../../lib/header.php';
include_once '../../lib/recaptcha.php';

$db = database::connect();

// Find the latest date in the assignment table
$queryDate = "SELECT MAX(a.`Date`) AS `maxdate` FROM `assignments` a;";
$stmtDate = $db->prepare($queryDate);
$queryDateSuccess = false;
if ($stmtDate->execute()) {
	if ($rowDate = $stmtDate->fetch(PDO::FETCH_ASSOC)) {
		$lastDate = $rowDate['maxdate'];
		$queryDateSuccess = true;
	}
}

// Stop the whole process if there IS no latest date in assignment table...
if ($queryDateSuccess) {

	// Setup month selection (only the most recent month of assignments or the next one are allowed)
	$lastDate = explode('-', $lastDate);
	$lastYear = $lastDate[0];
	$lastMonth = $lastDate[1];

	$oldMonth = date_create_from_format('Y-m', $lastYear . '-' . $lastMonth);
	$newMonth = clone $oldMonth;
	$newMonth->modify('+1 month');

	// Header info
	echo '
		<title>Institution Committee - Meeting Assignment Creation Tool</title>';
	echo recaptcha::javascript();
	
	// Quick check to make sure user wants to overwrite existing data, if so selected
	echo '
		<script>
			function warn() {
				if (confirm("THIS OPERATION WILL OVERWRITE PREVIOUSLY CREATED ASSIGNMENTS FOR ' . strtoupper($oldMonth->format('F Y')) . '!!!  Continue?")) {
					document.getElementById("oldmonth").checked = true;
				}
				else {
					document.getElementById("newmonth").checked = true;
				}
			}
		</script>
		</head>
		<body>';

	// Start form
	echo '
		<form name="pageform" id="pageform" action="create.php" method="post">';

	// Header
	echo '
		<h1>Meeting Assignment Creation Tool</h1>
		<p>Sponsor\'s night and manual assignment stage (1 of 2)</p><br>';
	echo '
		<h2>Month to assign:</h2>
		<p>
			<input id="newmonth" type="radio" name="monthyear" value="' . $newMonth->format('Y-m') . '" checked>
			<label for="newmonth">Next Month (' . $newMonth->format('F Y') . ')</label><br>
			<input id="oldmonth" type="radio" name="monthyear" value="' . $oldMonth->format('Y-m') . '" onclick="warn();">
			<label for="oldmonth">Prev Month (' . $oldMonth->format('F Y') . ')</label> <font color="red">OVERWRITES EXISTING DATA!</font>
		</p>
		<br>';

	// Select cutoff date, default 4th of month (explained below in output text)
	echo '
		<h2>Cutoff date:</h2>
		(meetings before this date, but not on this date, will be assigned as sponsor\'s nights)
		<p>
			<select name="cutoff">';
	for ($i=1; $i<=31; $i++) {
		echo '
				<option value="' . $i . '"' . ($i==4 ? ' selected' : '') . '>' . $i . '</option>';
	}
	echo '
			</select>
		</p>
		<br>';

	// Select institution threshold, default 3 or more institutions (explained below in output)
	echo '
		<h2>Institution threshold:</h2>
		(Institutions with these number of meetings or more automatically get a sponsor\'s night)
		<p>
			<select name="institutions">';
	for ($i=1; $i<=10; $i++) {
		echo '
				<option value="' . $i . '"' . ($i==3 ? ' selected' : '') . '>' . $i . '</option>';
	}
	echo '
			</select>
		</p>
		<br>';

	// Navigation buttons
	echo '
		<p>
		<a class="button" href="../">Home</a>';
	echo recaptcha::submitbutton('createformsubmit', 'Submit', 'submit', false, false);
	echo '
		</p>
	</form>
	</body>
	</html>';
}
else {
	echo '<script>alert("No previous date found!  Check database for information");</script>';
	echo '<script>window.location.href="../index.html";</script>';
}
?>