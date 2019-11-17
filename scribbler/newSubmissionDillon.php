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
        <title>Scribbler New Submission</title>
        <link rel="stylesheet" type="text/css" href="newsPage.css" />
        <!-- https://stackoverflow.com/questions/18301745/how-to-set-up-a-favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="news_logo.ico"/>
    </head>
    <body>
    <div class="taskbar" id="taskbar">
            <img src="news_logo.png" class="logo" alt="Logo">
            <p class="scribbler">Scribbler News</p>
            <div class="taskbarTabs centerDiv center" id="taskbar">
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
                    <input type="submit" class="taskbarForms pageNames newSubmission hover selectedPage" name="New Submission" value="New Submission">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
                </div>
                <div class="inner">
                <form  action="Comments.php" method=post>
                    <input type="submit" class="taskbarForms pageNames comments hover" name="Comments" value="Comments">
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
        <br><p class="title">New Submission</p>

        <!-- New Post Functionality -->

        <?php
           if (isset($_SESSION['current_user'])) {
            ?>
            <form action="./newsHomePage.php" method="POST">
                <label for="storyTitle">Story Title:<br></lable>
                <input type="text" name="storyTitle" />
                <label for="storyText"><br>Story Text:<br></label>
                <input type="text" name="storyText" />
                <label for="storyLink"><br>Link to Attach (optional):<br></label>
                <input type="text" name="storyLink" />
                <label for="submit"><br></label>
                <input type="submit" name="submit" />
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                <input type="hidden" name="newStory" value="newStory" />
            </form>
        <?php
           } 
        ?>
    </body>
</html>