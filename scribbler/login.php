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
        <title>Scribbler Login</title>
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
        </div>

        <br><p class="title">Login</p>
        </form>

        <div class="centerDiv" id="loginDiv">
            <?php
            /* Checking User Login */
            if(isset($_POST['login_check'])) {
                if (preg_match('/^[\w_\.\-]+$/', $_POST['username']) && preg_match('/^[\w_\.\-]+$/', $_POST['password'])) {
                    if(isset($invalid_login))
                        unset($invalid_login);
                    if(isset($invalid_username))
                        unset($invalid_username);
                    if(isset($invalid_password))
                        unset($invalid_password);
                    $username_exist = checkUserExist($conn, $_POST['username']);
                    if ($username_exist == "Assigned"){
                        /* https://www.w3schools.com/php/php_mysql_select.asp */
                        $stmt = $conn->prepare("SELECT COUNT(*), user_id, password FROM scribbler_users WHERE username=?");
                        $stmt->bind_param('s', $username);
                        $username = $_POST['username'];
                        $stmt->execute();

                        $stmt->bind_result($cnt, $user_id, $password);
                        $stmt->fetch();

                        if($cnt == 1 && password_verify($_POST['password'], $password)){
                            // Login succeeded!
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['current_user'] = $username;
                            // Redirect to your target page
                        } else{
                            // Login failed; redirect back to the login screen
                            ?>
                            <p class="failedLogin">Incorrect Password</p>
                            <?php
                        }
                        $stmt->close();
                    }
                    else {
                        ?>
                        <p class="failedLogin">Invalid Username</p>
                        <?php
                    }
                }
                else{
                    if (!preg_match('/^[\w_\.\-]+$/', $_POST['username'])) {
                        $invalid_login = true;
                        $invalid_username = true;
                    }
                    if (!preg_match('/^[\w_\.\-]+$/', $_POST['password'])) {
                        $invalid_login = true;
                        $invalid_password = true;
                    }
                }
            }
            /* Checking New User Creation */
            if(isset($_POST['create_account'])) {
                if (preg_match('/^[\w_\.\-]+$/', $_POST['username']) && preg_match('/^[\w_\.\-]+$/', $_POST['password'])) {
                    if(isset($invalid_login))
                        unset($invalid_login);
                    if(isset($invalid_username))
                        unset($invalid_username);
                    if(isset($invalid_password))
                        unset($invalid_password);
                    $username_exist = checkUserExist($conn, $_POST['username']);
                    if ($username_exist == "Unassigned"){
                        /*  https://www.w3schools.com/php/php_mysql_insert.asp  */
                        $stmt = $conn->prepare("INSERT INTO scribbler_users (username, password) VALUES (?, ?)");
                        
                        if(!$stmt){
                            ?>
                            <p class="failedLogin">Account Creation Error. Try Again.</p>
                            <?php
                            exit;
                        }
                        else {
                            $stmt->bind_param('ss', $username, $password_hash);
                            $username = $_POST['username'];
                            $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
                            $stmt->execute();
                            $stmt->close();
                            
                            $stmt = $conn->prepare("SELECT COUNT(*), user_id FROM scribbler_users WHERE username=?");
                            $stmt->bind_param('s', $username);
                            $username = $_POST['username'];
                            $stmt->execute();

                            $stmt->bind_result($cnt, $user_id);
                            $stmt->fetch();
                            
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['current_user'] = $username;
                            $stmt->close();
                        }
                    }
                    else {
                        ?>
                        <p class="failedLogin">Username Already Taken</p>
                        <?php
                    }
                }
                else{
                    if (!preg_match('/^[\w_\.\-]+$/', $_POST['username'])) {
                        $invalid_login = true;
                        $invalid_username = true;
                    }
                    if (!preg_match('/^[\w_\.\-]+$/', $_POST['password'])) {
                        $invalid_login = true;
                        $invalid_password = true;
                    }
                }
            }
            if(isset($_SESSION['current_user'])) {
                if(isset($_POST['login_check'])) {
                    ?>
                    </div>
                    <div class="centerDiv center" id="login">
                    <p class="loginText">Login Successfull!</p>
                    <?php
                }
                else {
                    ?>
                    </div>
                    <div class="center" id="login">
                    <p class="loginText">Account Created Successfully!!</p>
                    <?php
                }
                ?>
                <form  action="newsHomePage.php" method=post>
                    <input type="submit" class="logButton hover" name="Return_Home" value="Return Home">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                </form>
                </div>
                <?php
            }
            else {
                ?>
                <p class="NewAccount">Login:</p>
                <form action="login.php" method="POST">
                    <label>Username: <input type="text" name="username"/> </label><br><br>
                    <label>Password: &nbsp;<input type="password" name="password"/> </label>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    <input type="submit" class="logButton hover" name="login_check" value="Login"/>
                </form>
                <br><br><p class="NewAccount">Sign Up:</p>
                <form action="login.php" method="POST">
                    <label>Username: <input type="text" name="username"/> </label><br><br>
                    <label>Password: &nbsp;<input type="password" name="password"/> </label>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    <input type="submit" class="logButton hover" name="create_account" value="Create Account"/>
                </form>
                <?php
            }
            if (isset($invalid_login)) {
                if(isset($invalid_username)) {
                    ?>
                    <br><p class="failedLogin">Invalid Username</p>
                    <?php
                }
            }
            ?>
        </div>
        
    </body>
</html>