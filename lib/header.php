<?php	// HTML header code
date_default_timezone_set('America/New_York');
$libloc = abspathHTML();
echo '<!--Created by Robert Rutherford, 2017-->
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="' . $libloc . 'main.css">
	<link rel="icon" type="image/png" href="/favicon.ico">';

function abspathHTML () {
        $ds = DIRECTORY_SEPARATOR;
        $dsHTML = '/';
        $curdir = __DIR__ . $ds;
        $rootdir = $_SERVER['DOCUMENT_ROOT'];
        if (substr($curdir, 0, strlen($rootdir)) == $rootdir) {
                $abspath = substr($curdir, strlen($rootdir));
        }
        else {
                $abspath = $dsHTML; // just to have all bases covered
        }
        return str_replace($ds, $dsHTML, $abspath);
}
?>
