<?php
/*
form.php (person version)
This file creates a form for inputting (adding/editing) group information.  If an ID
argument is passed, then we're editing.  If not, we're adding.  Most of the HTML is in
the member functions of person.php
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/person.php';
include_once '../../lib/header.php';
include_once '../../lib/recaptcha.php';

echo '<link href="' . $libloc . 'bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="' . $libloc . 'main.css">';

// Form string variables (employing array method for form elements)
$formName = 'personform';
$personBase = 'person';

// Edit person if ID provided
if(isset($_GET['id'])) {
  
	// Grab ID
	$personID = $_GET['id'];

	// Initiate person
	$person = new person($db, $personID);

	// Fill the information from the db
	$person->view();

	// Set title
	$title = 'Edit ' . $person->getName()->getFormatted() . $person->getInitial()->getFormatted();
}
// Adding person if no ID provided
else {

	$personID = null;

	$person = new person($db);

	$title = 'Add new person';
}

// Load javascript files, set title, initiate form (note that the validate form function
// accepts all the form element string bases)
echo ' 
	<script type="text/javascript" src="validate.js"></script>
	<script type="text/javascript" src="' . $libloc . 'validate.js"></script>';
echo recaptcha::javascript();
echo '
	<title>Institution Committee - ' . $title . '</title>
</head>
<body>
<form name="' . $formName . '" action="load.php" onsubmit="return validateForm(\'' .  $formName . 
	'\', \'' . $personBase . '\');" method="post">
<div class="page">
	<div class="container">';	// To keep things in line

// Draw divs using bootstrap columns and rows as containers
echo '<div class="container-fluid">';
echo '<div class="row">';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
echo '<p>';
echo '<a class="button" href = "../" style="margin-top: 10px;">Home</a>';
echo '<a class="button" href = "viewall.php">People</a>';
echo '</p>';
echo '</div>';
echo '<div class="col-lg-9 col-md-8" style="text-align: center;">';
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
$person->inputHTMLSimple($personBase, $personBase, 'AA member');
echo '</div>';
echo '<div class="col-lg-9 col-md-8" style="text-align: center;">';
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
echo '<p>';
echo recaptcha::submitbutton('personsubmit', 'Save', 'submit', false, false);
echo '</p>';
echo '</div>';
echo '<div class="col-lg-9 col-md-8" style="text-align: center;">';
echo '</div>';
echo '</div>';
echo '</div>
</body>
</html>';

?>
