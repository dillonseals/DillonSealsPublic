<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h3>Failed to upload file<h3>
    <br><br>
    <h3>Click below to return to User Home Page:<h3>
    <form method="POST" action="./userPage.php">
        <input type="submit" value="Home" />
    </form>

</body>
</html>