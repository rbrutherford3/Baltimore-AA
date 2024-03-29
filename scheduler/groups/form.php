<?php
/*
form.php (group version)
This file creates a form for inputting (adding/editing) group information.  If an ID
argument is passed, then we're editing.  If not, we're adding.  Most of the HTML is in
the member functions person and group
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/group.php';
include_once '../../lib/header.php';
include_once '../../lib/recaptcha.php';

$db = database::connect();

// Form string variables (employing array method for form elements)
$formName = 'groupform';
$groupBase = 'group';
$rep1Base = 'rep1';
$rep2Base = 'rep2';

// Edit group if ID provided
if(isset($_GET['id'])) {
  
	// Grab ID
	$groupID = $_GET['id'];

	// Initiate group
	$group = new group($db, $groupID);

	// Fill the information from the db
	$group->view();

	// Set title
	$title = 'Edit "' . $group->getName()->getFormatted() . '"';
}
// Adding group if no ID provided
else {

	$groupID = null;

	$group = new group($db);

	$title = 'Add new group';
}

// Load javascript files, set title, initiate form (note that the validate form function
// accepts all the form element string bases)
echo '
	<link href="' . $libloc . 'bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="' . $libloc . 'main.css">
	<script type="text/javascript" src="' . $libloc . 'personbuttons.js"></script>
	<script type="text/javascript" src="' . $libloc . 'secondbuttons.js"></script>
	<script type="text/javascript" src="validate.js"></script>
	<script type="text/javascript" src="' . $libloc . 'validate.js"></script>';
	echo recaptcha::javascript();
	echo '

	<title>Institution Committee - ' . $title . '</title>
</head>
<body>
<div class="container-fluid">
<form name="' . $formName . '" action="load.php" onsubmit="return validateForm(\'' .  $formName . 
	'\', \'' . $groupBase . '\', \'' . $rep1Base . '\', \'' . $rep2Base . '\');" method="post">';
/* <div class="page">
	<div class="container">';	// To keep things in line */

// Get information for rep and load all persons from db for pulldown menu
$group->rep->view();
$group->rep->viewAll();

// Get information for rep 2 and load all persons from db for pulldown menu
$group->rep2->view();
$group->rep2->viewAll();

// Check for the existence of the second rep
$rep2Exists = !is_null($group->rep2->getID());

// Determine div and button visibility states of each based on their existence
if ($rep2Exists) {
	$toggleState = 2;
}
else {
	$toggleState = 1;
}

// Draw divs using bootstrap columns and rows as containers
echo '
	<div class="row">
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">
			<p>
				<a class="button" href = "../" style="margin-top: 10px;">Home</a>
				<a class="button" href = "viewall.php">Groups</a>
			</p>
		</div>
		<div class="col-lg-9 col-md-8" style="text-align: center;">
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
			$group->inputHTML($groupBase,$groupBase,true);
echo '
		</div>
		<div class="col-lg-9 col-md-8" style="text-align: center;">
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
			$group->rep->inputHTML($rep1Base, $rep1Base, true, 'Representative');
echo '
		</div>
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
			// Only show second rep div if there is a second rep, otherwise hide.  Draw add/hide buttons appropriately
			$group->rep2->inputHTML($rep2Base, $rep2Base, $rep2Exists, 'Representative #2');
echo '
		</div>
		<div class="col-lg-6 col-md-4" style="text-align: center;">
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">
			<p>';
echo recaptcha::submitbutton('groupsubmit', 'Save', 'submit', false, false);
echo '
			</p>
		</div>
		<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">
			<p>';
				$group->rep2->toggleButton($rep2Base, $rep2Base, $toggleState, 'second representative');
echo '
			</p>
		</div>
		<div class="col-lg-6 col-md-4" style="text-align: center;">
		</div>
	</div>
</div>
<script>
secondbuttons("' . $rep2Base . '");
</script>
</body>
</html>';

?>
