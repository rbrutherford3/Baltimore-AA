<?php

// Basic PDO database connection

// !!!->Not giving proper connection string to protect access to database
	
$db = new PDO('mysql:host=fake_host;
	dbname=institutioncommittee;
	charset=utf8', 
	'fake_username', 
	'fake_password');

$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );	// Kick out errors
	
?>