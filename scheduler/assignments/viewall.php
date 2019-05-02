<?php

// Portal for viewing assignments, grabs parameters and loads assignment object

include_once '../../lib/assignment.php';
include_once '../../lib/dbconnect.php';
	
// Grab parameters from URL	
if (!isset($_GET['month']) || empty($_GET['month'])) {
	$month = null;
}
else {
	$month = $_GET['month'];
}
if (!isset($_GET['year']) || empty($_GET['year'])) {
	$year = null;
}
else {
	$year = $_GET['year'];
}
if (!isset($_GET['group']) || empty($_GET['group'])) {
	$group = null;
}
else {
	$group = $_GET['group'];
}
if (!isset($_GET['meeting']) || empty($_GET['meeting'])) {
	$meeting = null;
}
else {
	$meeting = $_GET['meeting'];
}
if (!isset($_GET['sort']) || empty($_GET['sort'])) {
	$sort = 0;
}
else {
	$sort = $_GET['sort'];
}

// Create a new assignment object and trigger the viewall function
$assignment = new assignment($db);
$assignment->viewAll($month, $year, $group, $meeting, $sort, false);

?>