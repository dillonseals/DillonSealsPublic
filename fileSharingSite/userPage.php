<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Page</title>
</head>
<body>

    <?php
    printf("<h1>Welcome, %s</h1>",
        htmlentities($_SESSION["username"]));
    ?>
    <h2>
        Below are your files:
    </h2>

    <?php
    // declare path to user's directory
    $_SESSION["userPath"] = "/home/dillonrayseals/userFiles/".$_SESSION["username"];
    // https://stackoverflow.com/questions/1086105/get-the-files-inside-a-directory
    // print each file name in user's file directory
    foreach (new DirectoryIterator($_SESSION["userPath"]) as $file) {
        if($file->isDot()) continue;
        print $file->getFilename() . '<br>';
    }
    ?>

    <h2>
        <br><br>
        What would you like to do?
    </h2>

    <!-- Request Action -->
    <!-- Open file - Delete file - Upload File - Logout -->

    <h3>
        Enter the name of the file (with extension) that you would like to open below.
    </h3>
    <!--
        Requests file name to open
        Opens file in new tab
     -->
    <form method="POST" action="./openPage.php" target="_blank">
        <input type="text" name="see" id="see" />
        <input type="submit" name="openSubmit" value="Open"/>
    </form>
    <br>
    <h3>
        Enter the name of the file (with extension) that you would like to delete below.
    </h3>
    <!--
        Requests file name to delete
        Sends user to deletePage.php to confirm or go back
        Note - does not check if file exists
    -->
    <form method="POST" action="deletePage.php">
        <input type="text" name="destroy" id="destroy" />
        <input type="submit" name="destroySubmit" value="Delete"/>
    </form>
    <br>
    <h3>
        To upload a File click below.
    </h3>
    <form enctype="multipart/form-data" method=post>
	<p>
		<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
		<label for="uploadfile_input">Choose a file to upload:</label> <input name="uploadedfile" type="file" id="uploadfile_input" />
	</p>
	<p>
		<input type="submit" name="fileUpload" value="Upload File" />
	</p>
    </form>

    <?php
    if(isset($_POST['fileUpload'])) {
        echo "The if statement works";
        // Get the filename and make sure it is valid
        $filename = basename($_FILES['uploadedfile']['name']);
        if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
            echo "Invalid filename";
            exit;
        }
        
        if( copy($_FILES['uploadedfile']['tmp_name'], $_SESSION["userPath"]."/".$filename) ){
            header("Location: uploadSuccess.php");
            exit;
        }else{
            header("Location: uploadFail.php");
            exit;
        }
    }
    ?>
<!-- TODO:
            Request file to upload (see PHP course wiki)
            upload file to user's directory
                file to browser, then browser to server (I think)
            refresh page? should be able to upload a new file
-->
    <br>
    <h3>
        To create a new File, enter new FileName below.
    <br>
    <br>
    <h3>
        To logout, click below.
    </h3>
    <form method="POST" action="./homeScreen.php">
        <input type="submit" value="Logout" />
    </form>
</body>
</html>