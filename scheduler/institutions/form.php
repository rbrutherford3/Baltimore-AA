<?php
/*
form.php (institution version)
This file creates a form for inputting (adding/editing) group information.  If an ID
argument is passed, then we're editing.  If not, we're adding.  Most of the HTML is in
the member functions of isnstitution.php
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/institution.php';
include_once '../../lib/header.php';

echo '<link href="/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/lib/main.css">';

// Form string variables (employing array method for form elements)
$formName = 'form';
$institutionBase = 'institution';

// Edit person if ID provided
if(isset($_GET['id'])) {
  
	// Grab ID
	$institutionID = $_GET['id'];
	
	// Initiate person
	$institution = new institution($db, $institutionID);
	
	// Fill the information from the db
	$institution->view();
	
	// Set title
	$title = 'Edit ' . $institution->getName()->getFormatted();
}
// Adding person if no ID provided
else {

	$institutionID = null;
	
	$institution = new institution($db);
	
	$title = 'Add new institution';
}

// Load javascript files, set title, initiate form (note that the validate form function
// accepts all the form element string bases)
echo ' 
	<script type="text/javascript" src="validate.js"></script>
	<script type="text/javascript" src="../lib/validate.js"></script>
	<title>Institution Committee - ' . $title . '</title>
</head>
<body>
<form name="' . $formName . '" action="load.php" onsubmit="return validateForm(\'' .  $formName . 
	'\', \'' . $institutionBase . '\');" method="post">
<div class="page">
	<div class="container">';	// To keep things in line

// Draw divs using bootstrap columns and rows as containers
echo '<div class="container-fluid">';
echo '<div class="row">';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
echo '<p>';
echo '<a class="button" href = "../" style="margin-top: 10px;">Home</a>';
echo '<a class="button" href = "viewall.php">Institutions</a>';
echo '</p>';
echo '</div>';
echo '<div class="col-lg-9 col-md-8" style="text-align: center;">';	
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
$institution->inputHTMLSimple($institutionBase, $institutionBase);
echo '</div>';
echo '<div class="col-lg-9 col-md-8" style="text-align: center;">';	
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-lg-3 col-md-4" style="min-width: 350px; text-align: center;">';
echo '<p>';
echo '<input type="submit" value="Submit">';
echo '</p>';
echo '</div>';
echo '<div class="col-lg-9 col-md-8" style="text-align: center;">';	
echo '</div>';
echo '</div>';
echo '</div>
</body>
</html>';

?>