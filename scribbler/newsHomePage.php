<?php
/* Connecting to SQL Database */
include 'db_connection.php';
$conn = OpenCon();

/* Ensuring Database Connection was successful. */
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* Starting Session and Checking or creating token */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
}
else {
    if (!hash_equals($_SESSION['token'], $_POST['token'])){
        die("Request forgery detected");
    }
}
if(isset($_POST['logout'])) {
    unset($_SESSION['current_user']);
}
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Scribbler Home</title>
        <link rel="stylesheet" type="text/css" href="newsPage.css" />
        <!-- https://stackoverflow.com/questions/18301745/how-to-set-up-a-favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="news_logo.ico"/>
    </head>
    <body>

        <!--  Taskbar Div  -->

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

        <!-- End of Taskbar Div -->

        <br><br><br><p class="title">Welcome to the Scribbler!</p>
        <p class="bodytext">We are a news source, for the people, by the people.<br>
            Where all can both hear and be heard!</p>

<!-- Display Stories
        Access DB for stories - done
        Print title of story - done
        Open in new page - done
-->
        <div class="centerDiv">
            <h1>Stories: </h1>
            <?php
                // 330 wiki
                // access DB for stories
                $newsStmt = $conn->prepare("select title, story_id from scribbler_stories ORDER BY story_id DESC");
                if (!$newsStmt) {
                    printf("Query Prep Failed: %s\n", $conn->error);
                }
                // bind DB data to variables
                $newsStmt->execute();
                $newsStmt->bind_result($bStoryTitle, $bStoryID);
                // print story titles as form submits
                // TODO: css - make buttons look more linkish
                echo "<ul>\n";
                while($newsStmt->fetch()){
                    printf("\t
                            <form action=\"openStory.php\" method=\"POST\">
                                <input class=\"storyLink storyhover\" type=\"submit\" name=\"title\" value=\"%s\" />
                                <input type=\"hidden\" name=\"storyID\" value=\"%s\" />
                                <input type=\"hidden\" name=\"token\" value=\"%s\" />
                            </form>
                    \n", $bStoryTitle, $bStoryID, $_SESSION['token']
                    );
                }
                echo "</ul>\n";
                // close DB
                $newsStmt->close();
            ?>
        </div>
    </body>
</html>