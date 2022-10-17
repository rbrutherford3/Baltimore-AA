<?php

// Form for creating new assignments.  All the inputs are explained in the HTML text.

// Setup dates, using next month
include_once '../../lib/header.php';
include_once '../../lib/dbconnect.php';
include_once '../../lib/recaptcha.php';

$db = database::connect();

$month = $_POST['month'];
$year = $_POST['year'];

// Find the latest date in the assignment table
$queryDate = "SELECT MAX(a.`Date`) AS `maxdate` FROM `assignments` a;";
$stmtDate = $db->prepare($queryDate);
$queryDateSuccess = false;
if ($stmtDate->execute()) {
	if ($rowDate = $stmtDate->fetch(PDO::FETCH_ASSOC)) {
		$lastDate = $rowDate['maxdate'];
		//echo '<script>alert("' . $lastDate . '");</script>';
		$queryDateSuccess = true;
	}
}

// Only proceed if a most recent assignment date was found
if ($queryDateSuccess) {

	$lastMonth = new DateTime($lastDate);
	// Make sure month being given is the most recent month worth of assignments, otherwise it could cause problems
	if (((int)$lastMonth->format('n')==(int)$month) && ((int)$lastMonth->format('Y')==(int)$year)) {
		// Header info
		echo '
			<title>Institution Committee - Assignment Creation Tool</title>';
		echo recaptcha::javascript();
		echo '
			</head>
			<body>';

		// Start form
		echo '
			<form name="form" action="fill.php" method="post">
			<input type="hidden" name="month" value="' . $month . '">
			<input type="hidden" name="year" value="' . $year . '">';

		// Header
		echo '
			<h1>Assignment Creation Tool</h1>
			<p>
			Automatic group assignment stage (2 of 2) - <b>' . $lastMonth->format('F Y') . '</b></p><br>';

		// Select history in months, default 12 months (explained below in output)
		echo '
			<h2>History:</h2>
			(Don\'t assign groups to this meeting if they\'ve been to this institution within the past X months)
			<p>
				<select name="history">';
		for ($i=1; $i<=24; $i++) {
			echo '
					<option value="' . $i . '"' . ($i==12 ? ' selected' : '') . '>' . $i . '</option>';
		}
		echo '
				</select>
			</p>
			<br>';

		// Navigation buttons
		echo '
			<p>
			<a class="button" href="../">Home</a>';
		echo recaptcha::submitbutton('formsubmit', 'Submit', 'submit', false, false);
		echo '
			</p>
			</form>
		</body>
		</html>';
	}
	else  {
		echo '<script>alert("Missing assignments are not for latest month\'s assignments!  Proceeding could cause problems.  
				Please check records against data.");</script>';
		echo '<script>window.location.href="../index.html";</script>';
	}
}
else {
	echo '<script>alert("No previous date found!  Check database for information");</script>';
	echo '<script>window.location.href="../index.html";</script>';
}
?>