<?php
/*
View all institutions.  Very simple, as all the HTML is stored in the objects as public functions
*/

include_once '../lib/dbconnect.php';
include_once '../lib/institution.php';

$institution = new institution($db);

$institution->viewAll();

$institution->outputsHTML();

?>