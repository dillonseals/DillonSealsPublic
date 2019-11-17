<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Validating...</title>
</head>
<body>
    <?php
    // get input from homeScreen
    $user = $_POST["username"];
    // make username a regex for preg_match
    $userRegex = "/" . $user . "/";
    // open file with usernames (users.txt)
    $userFile = fopen("/home/dillonrayseals/userFiles/users.txt", "r");
    // loop through user.txt, check if username in file
    while(!feof($userFile)){
        if (preg_match($userRegex, fgets($userFile))) {
            $_SESSION["username"] = $user;
            header("Location: ./userPage.php");
        }
    }
    // session variable not assigned means while loop did not trigger
    // redirects to home page
    if ($_SESSION["username"] == NULL) {
        header("Location: ./homeScreen.php");
    }
    ?>
</body>
</html>