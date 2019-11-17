<?php
include 'db_connection.php';
$conn = OpenCon();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();

if (!hash_equals($_SESSION['token'], $_POST['token'])){
    die("Request forgery detected");
}
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Scribbler Story View</title>
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
        
        <!-- Print title, user, text, and comments for POSTed story -->

        <?php
        // title of story clicked on newsHomePage.php
        $storyID = $_POST['storyID'];
        // if adding new comment, add comment to DB
        if (isset($_POST['newCommentSubmit'])) {
            echo $_POST['newCommentSubmit'];
        }
        
        // access DB for story data
        $storyStmt = $conn->prepare("select title, text, link, user_id, viewcount
                                    from scribbler_stories
                                    where story_id = ? ");
        if (!$storyStmt) {
            printf("Query Prep Failed: %s\n", $conn->error);
            exit;
        }
        $storyStmt->bind_param("i", $storyID);
        $storyStmt->execute();
        $storyStmt->bind_result($oStoryTitle, $oStoryText, $oStoryLink, $oStoryUserID, $oViewCount);
        // prints title and text
        while($storyStmt->fetch()) {
            // printf("<h1>%s</h1><br>", $oStoryTitle);
            // printf("<p>%s</p><br><a href=\"%s\">%s</a>", $oStoryText, $oStoryLink, $oStoryLink);
            $story_title = $oStoryTitle;
            $story_body = $oStoryText;
            $story_link = $oStoryLink;
            $view_count = $oViewCount;
        }
        $storyStmt->close();
        $view_count = $view_count + 1;
        
        //Updating number of story views
        $viewCountStmt = $conn->prepare("insert into scribbler_stories (viewcount) values (?)");
        if (!$viewCountStmt) {
            printf("Query Prep Failed: %s\n", $conn->error);
        }
        $viewCountStmt->bind_param("i", $view_count);
        $viewCountStmt->execute();
        $viewCountStmt->close();

        // access DB for user data
        $userStmt = $conn->prepare("select username from scribbler_users
                                    where user_id = ? ");
        if (!$userStmt) {
            printf("Query Prep Failed: %s\n", $conn->error);
        }
        $userStmt->bind_param("i", $oStoryUserID);
        $userStmt->execute();
        $userStmt->bind_result($oUsername);
        // prints story's user
        while($userStmt->fetch()) {
            // printf("<h2>%s</h2><br><br>", $oUsername);
            $story_author = $oUsername;
        }
        ?>
        <br><p class="title"><?php echo($oStoryTitle);?></p>
        <div class="centerDiv center storyWidth" id="storyDiv">
            <p class="storyText"><?php echo($story_body);?></p><br><br>
            <a href="<?php echo($story_link);?>"><?php echo($story_link);?></a>
            <p>Submitted By: <?php echo($story_author);?><br><br>

            <!-- Edit Story -->

            <?php
                if (isset($_SESSION['current_user']) && $_SESSION['current_user'] == $oUsername) {
            ?>
                    <form action="editPage.php" method="POST">
                        <input class="returnButton" type="submit" name="editStory" value="Edit Story" />
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                        <input type="hidden" name="storyID" value="<?php echo $storyID;?>" />
                        <input type="hidden" name="storyTitle" value="<?php echo $story_title;?>" />
                        <input type="hidden" name="storyText" value="<?php echo $story_body;?>" />
                        <input type="hidden" name="storyLink" value="<?php echo $story_link;?>" />
                    </form>
                    <?php
                }
                ?>

            <!-- Delete Story -->

            <?php
                if (isset($_SESSION['current_user']) && $_SESSION['current_user'] == $oUsername) {
            ?>
                    <form action="redirect.php" method="POST">
                        <input class="returnButton" type="submit" name="deleteStory" value="Delete Story" />
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                        <input type="hidden" name="storyID" value="<?php echo $storyID;?>" />
                    </form>
            <?php
                }
            ?>

            <!-- Display Comments -->

                <br><br><br><h2>Comments: </h2>
                </div>

                <div class="centerDiv commentsWidth" id="story_comments_div">
                <?php

                // access DB for comments data
                $commentStmt = $conn->prepare("select scribbler_comments.text,
                                                scribbler_comments.comment_id,
                                                scribbler_users.username
                                                from scribbler_comments
                                                join scribbler_users on
                                                (scribbler_comments.user_id=scribbler_users.user_id)
                                                where story_id = ? ");
                if (!$commentStmt) {
                    printf("Query Prep Failed: %s\n", $conn->error);
                }
                $commentStmt->bind_param("i", $storyID);
                $commentStmt->execute();
                $commentStmt->bind_result($oCommentText, $oCommentID, $oCommentUser);
                // prints comments and comments' users
                while($commentStmt->fetch()) {
                    printf("<p>%s</p><p>%s</p>", $oCommentText, "- ".$oCommentUser);
                    $commentID = $oCommentID;
                    $commentText = $oCommentText;
                    if (isset($_SESSION['current_user']) && $_SESSION['current_user'] == $oCommentUser) {
                    ?>

                    <!-- Edit Comment -->

                    <form action="editPage.php" method="POST">
                        <input type="submit" name="editCommentSubmit" value="Edit Comment" />
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                        <input type="hidden" name="editCommentID" value="<?php echo $commentID;?>" />
                        <input type="hidden" name="editCommentText" value="<?php echo $commentText;?>" />
                        <input type="hidden" name="storyID" value="<?php echo $storyID;?>" />
                    </form>

                    <!-- Delete Comment -->

                    <form action="redirect.php" method="POST">
                        <input type="submit" name="deleteComment" value="Delete Comment" />
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                        <input type="hidden" name="deleteCommentID" value="<?php echo $commentID;?>" />
                        <input type="hidden" name="storyID" value="<?php echo $storyID;?>" />
                    </form>

                    <?php
                    }
                    ?>
                    <br>
                <?php
                }
                ?>

                <?php
                // post a new comment
                if (isset($_SESSION['current_user'])) {
                    ?>
                    <form action="./redirect.php" method=POST>
                        Leave a comment below:<br>
                        <input type="text" name="commentText" />
                        <input type="submit" name="newCommentSubmit"/>
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                        <input type="hidden" name="storyID" value="<?php echo $storyID;?>" />
                        <input type="hidden" name="newComment" value="newComment" />
                    </form>
            <?php
            }
            ?>
        </div>
    </body>
</html>