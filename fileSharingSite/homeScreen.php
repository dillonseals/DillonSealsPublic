<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
else {
    session_destroy();
    session_start();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
</head>
<body>
    <h1>
        Greetings.
    </h1>
    <h2>
        Enter your username to login.
    </h2>

    <!-- request username -->
    <form method="POST" action="./validation.php">
        <input type="text" name="username" /><br>
        <input type="submit" name="Logout" />
    </form>
    <?php
    if (isset($_POST["Submit"]) && $_POST["Submit"] == "submit") {
        header("Location: ./validation.php");
    }
    ?>

<!--
        request input
        go to newUser.php
-->

    <br><br><br>
    <h2>
        To create a new user, enter your desired username below:
    </h2>
    <form method="POST" action="./newUser.php">
        <input type="text" name="newUserInput" />
        <input type="submit" name="newUserSubmit" />
    </form>
</body>
</html>