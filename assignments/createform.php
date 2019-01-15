<?php

// Form for creating new assignments.  All the inputs are explained in the output text.  Note that this form is compatible
// only with Google Chrome because of the date inputs, will change.


// Setup dates, using next month
include_once '../lib/header.php';
$date = new DateTime('next month');
$month = $date->format('n');
$year = $date->format('Y');

// Header info
echo '
	<title>Institution Committee - Assignment Creation Tool</title>
	</head>
	<body>';

// Start form	
echo '
	<form name="form" action="create.php" method="post">';

// Header
echo '
	<h1>Assignment Creation Tool</h1>
	<h2 style="color: red;">WARNING: THIS TOOL OVERWRITES EXISTING ASSIGNMENTS FOR THE MONTH!  USE WITH CAUTION!</h2>';
	
// Select month & year for automatically creating assignments, default next month
echo '
	<h2>Select month for creating assignments:</h2>
	<p>
		<select name="month">';
for ($i=1; $i<=12; $i++) {
	$di = DateTime::createFromFormat('!m', $i);
	echo '
			<option value="' . $i . '"' . ($i==$month ? ' selected' : '') . '>' . $di->format('F') . '</option>';
}
echo '
		</select>
		<select name="year">';
for($i=$year-5; $i<=$year+5; $i++) {
	echo '
			<option value="' . $i . '"' . ($i==$year ? ' selected' : '') . '>' . $i . '</option>';
}
echo '
		</select>
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

// Select history in months, default 12 months (explained below in output)
echo '
	<h2>History:</h2>
	(Don\'t assign groups to this meeting if they\'ve been to this institution in the past X months)
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

// Selection for manual entry, default unchecked (explained below in output)
echo '
	<h2>Manual entry:</h2>
	(Check this if you don\'t want to automatically assign groups, but rather make manual entries)
	<p>
	<input name="manual" type="checkbox">
	</p>
	<br>';
	
// Navigation buttons
echo '
	<p>
	<a class="button" href="../">Home</a>
	<input type="submit" value="Submit">
	</p>
</body>
</html>';
?>