<?php
include 'db_connection.php';
$conn = OpenCon();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
session_start();
if (!hash_equals($_SESSION['token'], $_POST['token'])){
    echo "Session " . $_SESSION['token'];
    echo "POST " . $_POST['token'];
    die("Request forgery detected");
}
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Scribbler Edit Page</title>
        <link rel="stylesheet" type="text/css" href="newsPage.css" />
        <!-- https://stackoverflow.com/questions/18301745/how-to-set-up-a-favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="news_logo.ico"/>
    </head>
    <body>
        <div class="taskbar" id="taskbar">
            <img src="news_logo.png" class="logo" alt="Logo">
            <p class="scribbler">Scribbler News</p>
            <div class="taskbarTabs fixed_center center" id="taskbar">
                <div class="inner">
                    <form  action="newsHomePage.php" method=post>
                        <input class="taskbarForms home" type="image" src="news_home_icon.png" alt='Picture of Home Button' name="Home">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    </form>
                </div>
                <div class="inner leftPadding">
                <form  action="topStories.php" method=post>
                    <input type="submit" class="taskbarForms pageNames topStories hover" name="Top Stores" value="Top Stories">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
                </div>
                <div class="inner">
                <form  action="newSubmission.php" method=post>
                    <input type="submit" class="taskbarForms pageNames newSubmission hover" name="New Submission" value="New Submission">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
                </div>
                <div class="inner">
                <form  action="discussion.php" method=post>
                    <input type="submit" class="taskbarForms pageNames comments hover" name="Discussion" value="Discussion">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
                </div>
                <div class="inner">
                <form  action="FAQ.php" method=post>
                    <input type="submit" class="taskbarForms pageNames FAQ hover" name="FAQ" value="FAQ">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
            </div>
        </div>
            
            <?php
            if(isset($_SESSION['current_user'])) {
                ?>
                <p class="username"><?php echo $_SESSION['current_user'];?></p>
                <form  action="newsHomePage.php" method=post>
                    <input type="submit" class="taskbarForms formbutton login hover" name="logout" value="Logout">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
                <?php
            }
            else {
                ?>
                <form  action="login.php" method=post>
                    <input type="submit" class="taskbarForms formbutton login hover" name="login" value="Login">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
                <?php
            }
            ?>
        </div>
        <br><p class="title">Edit</p>
        <div class="centerDiv" id="editdiv">
            <?php
            if (isset($_POST['editStory'])) {
                // assign POST variables
                $storyID = $_POST['storyID'];
                $storyTitle = $_POST['storyTitle'];
                $storyText = $_POST['storyText'];
                $storyLink = $_POST['storyLink'];
                ?>
                <form action="redirect.php" method="POST">
                    <label for="newTitle">Title:<br></label>
                        <input type="text" name="newTitle" value="<?php echo $storyTitle?>" /><br>
                    <label for="newText"><br>Text:<br></label>
                        <input type="text" name="newText" value="<?php echo $storyText?>" /><br>
                    <label for="newLink"><br>Link"<br></label>
                        <input type="text" name="newLink" value="<?php echo $storyLink?>" />
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    <input type="hidden" name="storyID" value="<?php echo $storyID?>" />
                    <input type="submit" name="newStorySubmit" value="Submit Changes" />
                </form><br><br>
                <form action="openStory.php" method="POST">
                    <input type="submit" name="storyReturn" value="Return to Story" />
                    <input type="hidden" name="storyID" value="<?php echo $storyID;?>" />
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
            <?php
            }
            if (isset($_POST['editCommentID'])) {
                // assign POST variables
                $commentID = $_POST['editCommentID'];
                $commentText = $_POST['editCommentText'];
                $commentEditStoryID = $_POST['storyID'];
                ?>
                <form action="./redirect.php" method="POST">
                    <label for="newCommentEdit">Edit Comment:<br></label>
                        <input type="text" name="newCommentEdit" value="<?php echo $commentText;?>" />
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    <input type="hidden" name="commentID" value="<?php echo $commentID;?>" />
                    <input type="hidden" name="storyID" value="<?php echo $commentEditStoryID;?>" />
                    <input type="submit" name="submitEdit" value="Submit Edit" />
                </form>
            <?php
            }
            ?>
        </div>
    </body>
</html>