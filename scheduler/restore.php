<?php

include_once '../lib/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_FILES["sql"]["error"] == UPLOAD_ERR_OK) {
        $filename = getcwd() . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . basename($_FILES["sql"]["name"]);
        move_uploaded_file($_FILES["sql"]["tmp_name"], $filename);
        $sql = file_get_contents($filename);
        $db = database::connect();
        $errors = false;
        try {
            $db->exec($sql);
        }
        catch(Exception $e) {
            $errors = true;
            var_dump($e->getMessage());
        }
        if ($errors)
            $output = "There was an error executing the query";
        else
            $output = "Data successfully restored!";
    }
    else
        $output = "There was an error uploading the .sql file";
    echo '
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="../lib/main.css">
        <link rel="icon" type="image/png" href="favicon.ico">
        <title>Institution Committee Meeting Assignment Tool</title>
    </head>
    <body>
        <p>' . $output . '</p>
        <a href="index.html"><button>Home</button></a>
    </body>
    </html>
';
}
else {
    echo '
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" type="text/css" href="../lib/main.css">
            <link rel="icon" type="image/png" href="favicon.ico">
            <title>Institution Committee Meeting Assignment Tool</title>
        </head>
        <body>
            <h1>Restore Baltimore AA Institution Committee Tool Data</h1>
            <form method="post" enctype="multipart/form-data">
            <p>
                <label for="file">Select .sql backup file:</label>
            </p>
            <p>
                <input type="file" name="sql" id="file" accept=".sql">
            </p>
            <p>
                <input type="submit" name="submit" value="Submit">
            </p>
        </form>
        </body>
        </html>
    ';
}
?>