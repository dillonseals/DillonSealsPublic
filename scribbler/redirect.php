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
        <title>Scribbler Redirect</title>
        <link rel="stylesheet" type="text/css" href="newsPage.css" />
        <!-- https://stackoverflow.com/questions/18301745/how-to-set-up-a-favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="news_logo.ico"/>
    </head>

    <!-- New Comment Stuff -->
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
        <br><p class="title">Redirect</p>
        <div class="centerDiv center" id="editdiv">

        <!-- Create a new comment -->

            <?php
            if (isset($_POST['newComment'])) {
                // assign variables
                $storyID = $_POST['storyID'];
                $newComment = $_POST['commentText'];
                // access DB for userID
                $getUserStmt = $conn->prepare("select user_id from scribbler_users
                                                where username = ?");
                if (!$getUserStmt) {
                    printf("Query Prep Failed: %s\n", $conn->error);
                }
                $getUserStmt->bind_param("s", $_SESSION['current_user']);
                $getUserStmt->execute();
                $getUserStmt->bind_result($commentUserID);
                // get comment user out of sql variable
                while ($getUserStmt->fetch()) {
                    $ncsCommentUserID = $commentUserID;
                }
                $getUserStmt->close();
                // save comment to DB
                $newCommentStmt = $conn->prepare("insert into scribbler_comments (text, user_id, story_id)
                                                    values (?, ?, ?)");
                if (!$newCommentStmt) {
                    printf("Query Prep Failed: %s\n", $conn->error);
                }
                $newCommentStmt->bind_param("sii", $newComment, $ncsCommentUserID, $storyID);
                $newCommentStmt->execute();
                $newCommentStmt->close();
            ?>
            <!-- redirect to openStory -->
            <form action="./openStory.php" method="POST">
                Comment Submitted. Click here to return.
                (Our apologies for the inconvenience)<br>
                <input type="submit" name="redirectSubmit" />
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                <input type="hidden" name="storyID" value="<?php echo $storyID;?>" />
            </form>
            <?php
            }
            ?>

            <!-- New Story Submission -->

            <?php
            if (isset($_POST['newStory'])) {
                // assign POST variables
                $storyTitle = $_POST['storyTitle'];
                $storyText = $_POST['storyText'];
                if (isset($_POST['storyLink'])) {
                    $storyLink = $_POST['storyLink'];
                }
                // get user
                $getStoryUserStmt = $conn->prepare("select user_id from scribbler_users
                                                where username = ?");
                if (!$getStoryUserStmt) {
                    printf("Query Prep Failed: %s\n", $conn->error);
                }
                $getStoryUserStmt->bind_param("s", $_SESSION['current_user']);
                $getStoryUserStmt->execute();
                $getStoryUserStmt->bind_result($bindStoryUserID);
                // get user out of sql variable
                while ($getStoryUserStmt->fetch()) {
                    $storyUserID = $bindStoryUserID;
                }
                $getStoryUserStmt->close();
                // insert story data into DB

                /*  Need to move this somewhere else, probably to top of file and Change parameters to match this project 


                if(isset($_POST['fileUpload'])) {
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
                */

                $newStoryStmt = $conn->prepare("insert into scribbler_stories
                                                (title, text, link, user_id)
                                                values (?, ?, ?, ?)");
                if (!$newStoryStmt) {
                    printf("Query Prep Failed: %s\n", $conn->error);
                }
                $newStoryStmt->bind_param("sssi", $storyTitle, $storyText, $storyLink, $storyUserID);
                $newStoryStmt->execute();
                $newStoryStmt->close();
                ?>
                <!-- redirect to newsHomePage -->
            <form action="./newsHomePage.php" method="POST">
                Story Submitted. Click here to return.<br>
                (Our apologies for the inconvenience)<br><br>
                <input class="returnButton hover" type="submit" name="redirectSubmit" value="Return"/>
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
            </form>
            <?php    
            }
            ?>

        <!-- Story edit DB stuff -->

            <?php
            if (isset($_POST['newStorySubmit'])) {
                // assign POST variables
                $newTitle = $_POST['newTitle'];
                $newText = $_POST['newText'];
                $newLink = $_POST['newLink'];
                $newStoryID = $_POST['storyID'];
                // access DB to update changes
                $newStoryStmt = $conn->prepare("update scribbler_stories set
                                                title=?, text=?, link=?
                                                where story_id = ?");
                if (!$newStoryStmt) {
                printf("Query Prep Failed: %s\n", $conn->error);
                }
                $newStoryStmt->bind_param("sssi", $newTitle, $newText, $newLink, $newStoryID);
                $newStoryStmt->execute();
                $newStoryStmt->close();
            ?>
            <form action="./openStory.php" method="POST">
                Story Edited. Click here to return.<br>
                (Our apologies for the inconvenience)<br><br>
                <input type="submit" name="redirectSubmit" value="Return"/>
                <input type="hidden" name="storyID" value="<?php echo $newStoryID?>" />
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
            </form>
            <?php
            }
            ?>

            <!-- Comment Edit DB Stuff -->

            <?php
            if (isset($_POST['newCommentEdit'])) {
                // assign POST variables
                $newCommentEdit = $_POST['newCommentEdit'];
                $commentID = $_POST['commentID'];
                $commentEditStoryID = $_POST['storyID'];
                // access DB to update changes
                $commentEditStmt = $conn->prepare("update scribbler_comments set
                                                    text=? where comment_id=?");
                if (!$commentEditStmt) {
                    printf("Query Prep Failed: %s\n", $conn->error);
                }
                $commentEditStmt->bind_param("si", $newCommentEdit, $commentID);
                $commentEditStmt->execute();
                $commentEditStmt->close();
                ?>
                <form action="./openStory.php" method="POST">
                    Comment Edited. Click here to return.<br>
                    (Our apologies for the inconvenience)<br><br>
                    <input type="submit" name="redirectSubmit" value="Return"/>
                    <input type="hidden" name="storyID" value="<?php echo $commentEditStoryID?>" />
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
            <?php
            }
            ?>

            <!-- Delete Story DB Stuff -->
            <!-- Note - need to delete comments as well? -->

            <?php
                if (isset($_POST['deleteStory'])) {
                    // assign POST variables
                    $deleteStoryID = $_POST['storyID'];
                    // access DB
                    $deleteStoryStmt = $conn->prepare("delete from scribbler_stories
                                                        where story_id = ?");
                    if (!$deleteStoryStmt) {
                        printf("Query Prep Failed: %s\n", $conn->error);
                    }
                    $deleteStoryStmt->bind_param("i", $deleteStoryID);
                    $deleteStoryStmt->execute();
                    $deleteStoryStmt->close();
                    ?>
                    <!-- redirect -->
                    <form action="./newsHomePage.php" method="POST">
                        Story destroyed. Click here to return.<br>
                        (Our apologies for the inconvenience)<br><br>
                        <input type="submit" name="redirectSubmit" value="Return"/>
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    </form>
            <?php
                }
            ?>

            <!-- Delete Comment DB Stuff -->

            <?php
                if (isset($_POST['deleteComment'])) {
                    // assign POST variables
                    $deleteCommentID = $_POST['deleteCommentID'];
                    $deletecommentStoryID = $_POST['storyID'];
                    // access DB
                    $deleteCommentStmt = $conn->prepare("delete from scribbler_comments
                                                            where comment_id = ?");
                    if (!$deleteCommentStmt) {
                        printf("Query Prep Failed: %s\n", $conn->error);
                    }
                    $deleteCommentStmt->bind_param("i", $deleteCommentID);
                    $deleteCommentStmt->execute();
                    $deleteCommentStmt->close();
                    ?>
                    <!-- redirect -->
                    <form action="./openStory.php" method="POST">
                        Comment destroyed. Click here to return.<br>
                        (Our apologies for the inconvenience)<br><br>
                        <input type="submit" name="redirectSubmit" value="Return"/>
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                        <input type="hidden" name="storyID" value="<?php echo $_POST['storyID'];?>" />
                    </form>
            <?php
                }
            ?>
        </div>
    </body>
</html>