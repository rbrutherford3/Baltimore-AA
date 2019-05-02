<?php
/*
View all people.  Very simple, as all the HTML is stored in the objects as public functions
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/person.php';

$person = new person($db);

$person->viewAll();

$person->outputsHTML();

?>