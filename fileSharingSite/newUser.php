<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>New User Creation</title>
</head>
<body>
<!-- New user creation -->

    <?php
    // check users.txt for conflict

    $newUsername = $_POST["newUserInput"];
    // make username a regex for preg_match
    $userRegex = "/" . $newUsername . "/";
    // open users.txt in read
    $userFileR = fopen("/home/dillonrayseals/userFiles/users.txt", "r");
    $checker = "";
    // loop through user.txt, check if username already in file
    while(!feof($userFileR)){
        if (preg_match($userRegex, fgets($userFileR))) {
            // error message
            printf("<h1>Oops, %s has already been taken. Sorry!</h1>",
                htmlentities($newUsername)
            );
            ?>
            <!-- redirect to homeScreen.php -->
            <br><br>
            <h2>Click below to return to login screen.</h2>
            <form method="POST" action="./homeScreen.php">
                <input type="submit" value="Back" />
            </form>
            <?php
            $checker = FALSE;
            break;
        } else {
            $checker = TRUE;
        }
    }
    // Create new user

    if ($checker) {
        // open users.txt in append
        $userFileA = fopen("/home/dillonrayseals/userFiles/users.txt", "a");
        // write username to users.txt
        fwrite($userFileA, $newUsername . "\n");
        // create new directory for new user
        mkdir("/home/dillonrayseals/userFiles/" . $newUsername, 0777);
        // set permissions (above doesn't entirely work, unsure why)
        chmod("/home/dillonrayseals/userFiles/" . $newUsername, 0777);
        ?>
        <!-- message and return button -->
        <br>
        <h1>New user successfully created!</h1>
                <h2>Click below to return to login</h2><br>
                <form action="homeScreen.php" method="POST">
                    <input type="submit" name="Return" value="Return"/>
                </form>
        <?php
    }
    ?>
</body>
</html>