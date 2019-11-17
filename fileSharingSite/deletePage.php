<?php
    session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DESTRUCTION</title>
</head>
<body>
<!-- Confirm or reject file deletion, send back to userPage.php -->
<!-- Next correction - remove printf() statements, use raw html -->

    <?php
    // assign file variables
    $destroyFile = $_POST["destroy"];
    // filter input
    if( !preg_match('/^[\w_\.\-]+$/', $destroyFile) ){
        header("Location: ./userPage.php");
    }    
    $destroyFilePath = $_SESSION["userPath"] . "/" . $destroyFile;

    // Confirm deletion of file

    // if POST is set, then decision form has not been submitted
    // so, prompt user with decision form
    if (isset($_POST["destroy"])) {
        // warning message
        printf("<h1>Are you sure you wish to destroy %s?<br><br>There will be no going back.</h1>",
            htmlentities($destroyFile)
        );
        // decision form
        printf("
            <form method=\"GET\">
                <input type=\"hidden\" name=\"file\" value=\"%s\">
                <input type=\"radio\" name=\"decision\" value=\"yes\" /> Do it.<br>
                <input type=\"radio\" name=\"decision\" value=\"no\" /> Mercy, for now.<br>
                <input type=\"submit\" name=\"deleteInput\" />
            </form>",
                htmlentities($destroyFilePath)
        );
    }

    // Check decision and perform appropriate operations

    if ($_GET["decision"] == "yes") {
        // delete file
        unlink($_GET["file"]);
        // message
        printf("<h1>It is done.</h1>");
        printf("<h2>Click below to return</h2>");
        // redirect to userPage.php
        // bonus implement - timed auto-redirect?
        printf("
            <form action=\"userPage.php\">
                <input type=\"submit\" />
            </form>"
        );
    // redirects to userPage.php, no file deletion
    } elseif ($_GET["decision"] == "no") {
        header("Location: ./userPage.php");
    }
    ?>
</body>
</html>