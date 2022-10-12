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
    $a = strlen(__DIR__) - 3;
    $b = strlen(getcwd()) + strlen(basename($_SERVER['REQUEST_URI'])) + 1;
    $len_root = strlen($_SERVER['REQUEST_URI']) - ($b - $a);
    $root = substr($_SERVER['REQUEST_URI'], 0, $len_root);
    return str_replace($ds, $dsHTML, $root) . 'lib' . $dsHTML;
}
?>
