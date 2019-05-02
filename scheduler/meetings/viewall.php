<?php
/*
View all meetings.  Very simple, as all the HTML is stored in the objects as public functions
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/meeting.php';

$meeting = new meeting($db);

$meeting->viewAll();

$meeting->outputsHTML();

?>