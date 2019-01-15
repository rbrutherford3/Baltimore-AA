<?php
/*
View all groups.  Very simple, as all the HTML is stored in the objects as public functions
*/

include_once '../lib/dbconnect.php';
include_once '../lib/group.php';

$group = new group($db);

$group->viewAll();

$group->outputsHTML();

?>