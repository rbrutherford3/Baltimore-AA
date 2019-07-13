<?php
/*
view.php (group version)
View a group. Very simple file, as all the HTMl is stored in the objects as public functions
*/

include_once '../../lib/dbconnect.php';
include_once '../../lib/group.php';

// If there's a group ID, then declare a group object and output the view HTML
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$group = new group($db, $id);
	$group->view();
	$group->rep->view();
	$group->rep2->view();
	$group->outputHTML(false);
}

// Send to "View All" if no group found
else {
	echo '<script type="text/javascript">';
	echo 'alert("No group selected!  Taking you to the list of all groups to pick from.");';
	echo 'window.location.href = "viewall.php";';
	echo '</script>';
	die('Forwarding...');
}
?>