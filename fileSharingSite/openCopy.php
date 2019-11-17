<?php
session_start();
?>
<!--
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Open Page</title>
</head>
<body>
    <h1>
        Open Page
    </h1>

    <?php
    // path to file
    $filePath = $_SESSION["userPath"] . "/" . $_POST["see"];
    // gets file MIME type, sets header to type
    // from 330 wiki
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($filePath);
    var_dump($finfo);
    var_dump($mime);
    header("Content-Type: ".$mime);
    // FIX ME - prints this entire file
    readfile($filePath);
    ?>
</body>
</html>
-->