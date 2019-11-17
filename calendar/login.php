<?php
// access db
include 'dbConnect.php';
$conn = OpenCon();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
/* Starting Session and Checking or creating token */
ini_set("session.cookie_httponly", 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
}
else {
    if (!hash_equals($_SESSION['token'], $_POST['token'])){
        die("Request forgery detected");
    }
}
$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
	die("Session hijack detected");
}else{
	$_SESSION['useragent'] = $current_ua;
}

// Since we are sending a JSON response here (not an HTML document),
// set the MIME Type to application/json
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

// Check if a user is logged in
if ($json_obj['request'] == 'checkUser') {
    if (isset($_SESSION['current_user'])) {
        $my_array = array(
            "user_set" => true,
            "current_user" => $_SESSION['current_user']
        );
    }
    else {
        $my_array = array(
            "user_set" => false
        );
    }
    echo json_encode($my_array);
}

// Login User
if ($json_obj['request'] == 'loginUser') {
    // get username and password from json
    $username = $json_obj['username'];
    $password = $json_obj['password'];
    //$password = $json_obj['password'];
    // checker variables
    $goodUser = false;
    $goodPass = false;
    // query db
    $getUserStmt = $conn->prepare("select password, user_id from users
                                    where username = ?");

    if (!$getUserStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
        // username not in db
        $goodUser = false;
    }

    $getUserStmt->bind_param("s", $username);
    $getUserStmt->execute();
    $getUserStmt->bind_result($dbCheckPassword, $bindUserID);
    // get password out of sql variable
    while ($getUserStmt->fetch()) {
        $checkPassword = $dbCheckPassword;
        $user_id = $bindUserID;
    }
    $getUserStmt->close();

    // check if password pulled from db
    if (isset($checkPassword)) {
        // username in db
        $goodUser = true;
        // check if input password and stored password match
        if (password_verify($password, $checkPassword)) {
        //if ($password == $checkPassword) {
            // username and password match
            $goodPass = true;
        }
    }

    // username and password in db
    if ($goodUser == true && $goodPass == true) {
        $_SESSION['current_user'] = $json_obj['username'];
        $_SESSION['user_id'] = $user_id;
        $my_array = array(
            "login" => true,
            "user_id" => $_SESSION['user_id']
        );
    // incorrect username
    } elseif ($goodUser == false) {
        $my_array = array(
            "login" => false,
            "failedLogin" => 'Username does not exist'
        );
    // incorrect password
    } else {
        $my_array = array(
            "login" => false,
            "failedLogin" => 'Username and password do not match',
            "Typed Password" => $password,
            "DataBase Password" => $checkPassword
        );
    }
    // do the JSON
    echo json_encode($my_array);
}

// Register New User
if ($json_obj['request'] == 'registerUser') {
    // get username and password from json
    $username = $json_obj['username'];
    $password = password_hash($json_obj['password'], PASSWORD_BCRYPT);

    // query db
    $getUserStmt = $conn->prepare("select user_id from users where username = ?");

    if (!$getUserStmt) {
        $register = false;
        $failedRegister = "Check username Query Prep Failed";
    }
    $getUserStmt->bind_param("s", $username);
    $getUserStmt->execute();
    $getUserStmt->bind_result($bindUserID);
    // get user_id out of sql variable
    while ($getUserStmt->fetch()) {
        $user_id = $bindUserID;
    }
    $getUserStmt->close();

    // check if password pulled from db
    if (isset($user_id)) {
        // username in db
        $register = false;
        $failedRegister = "Username Already Taken";
    }
    else {
        $registerUserStmt = $conn->prepare("insert into users (username, password) values (?, ?)");
        if (!$registerUserStmt) {
            $register = false;
            $failedRegister = "Register User Query Prep Failed";
        }
        else {
            $register = true;
            $my_array = array(
                "register" => $register,
                "username" => $username
            );
        }
        $registerUserStmt->bind_param("ss", $username, $password);
        $registerUserStmt->execute();
        $registerUserStmt->close();
    }
    if (isset($failedRegister)) {
        $my_array = array(
            "register" => $register,
            "failedRegister" => $failedRegister
        );
    }
    // do the JSON
    echo json_encode($my_array);
}

// logout user
if ($json_obj['request'] == "logoutUser") {
    session_destroy();
    $my_array = array(
        "login" => false,
    );
    echo json_encode($my_array);
}

// Get events from db
if ($json_obj['request'] == "getEvents") {
    // variables
    $my_array = array();
    $shared_id = array();
    $date = $json_obj['date'];
    // check if user has shared calendars
    // access db
    $checkSharedStmt = $conn->prepare("select shared_id from shared_calendars where user_id = ?");
    if (!$checkSharedStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
    }

    $checkSharedStmt->bind_param("i", $_SESSION['user_id']);
    $checkSharedStmt->execute();
    $checkSharedStmt->bind_result($getSharedID);

    while ($checkSharedStmt->fetch()) {
        array_push($shared_id, $getSharedID);
    }

    $checkSharedStmt->close();

    foreach ($shared_id as $id) {
        $getEventsStmt = $conn->prepare("select title, date, time, event_id from calendar_events
                                        where user_id = ? and date = ?");
        if (!$getEventsStmt) {
            $my_array = array(
                "success" => false,
                "error" => "Query Prep Failed"
            );
        }
    
        $getEventsStmt->bind_param("is", $id, $date);
        $getEventsStmt->execute();
        $getEventsStmt->bind_result($eTitle, $eDate, $eTime, $eEventID);

        while ($getEventsStmt->fetch()) {
            $thisArray = array(
                "title" => htmlentities($eTitle),
                "date" => htmlentities($eDate),
                "time" => htmlentities($eTime),
                "event_id" => htmlentities($eEventID)
            );
            array_push($my_array, $thisArray);
        }

        $getEventsStmt->close();
    }

    // variables
    $groupUserID = array();
    // check if user in groups
    $checkGroupsStmt = $conn->prepare("select group_id from groups where user1_id = ? or user2_id = ? or user3_id = ?");
    if (!$checkGroupsStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
    }

    $checkGroupsStmt->bind_param("iii", $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']);
    $checkGroupsStmt->execute();
    $checkGroupsStmt->bind_result($getGroupUserID);

    while ($checkGroupsStmt->fetch()) {
        array_push($groupUserID, htmlentities($getGroupUserID));
    }

    $checkGroupsStmt->close();

    foreach ($groupUserID as $groupID) {
        // variables
        $gTitle = '';
        $gDate = '';
        $gTime = '';
        $gEventID = 0;
        // get events from group_events
        $getGroupEventsStmt = $conn->prepare("select title, date, time, group_event_id from group_events
                                                where group_id = ? and date = ?");
        if (!$getGroupEventsStmt) {
            printf("Query Prep Failed: %s\n", $conn->error);
        }

        $getGroupEventsStmt->bind_param("is", $groupID, $date);
        $getGroupEventsStmt->execute();
        $getGroupEventsStmt->bind_result($gTitle, $gDate, $gTime, $gEventID);

        while ($getGroupEventsStmt->fetch()) {
            array_push($my_array, array(
                "title" => htmlentities($gTitle),
                "date" => htmlentities($gDate),
                "time" => htmlentities($gTime),
                "event_id" => htmlentities($gEventID)
            ));
        }

        $getGroupEventsStmt->close();
    }

    // get user's events from db
    $getEventsStmt = $conn->prepare("select title, date, time, event_id from calendar_events
                                        where user_id = ? and date = ? order by time");
    if (!$getEventsStmt) {
        $my_array = array(
            "success" => false,
            "error" => "Query Prep Failed"
        );
    }
    
    $getEventsStmt->bind_param("is", $_SESSION['user_id'], $date);
    $getEventsStmt->execute();
    $getEventsStmt->bind_result($eTitle, $eDate, $eTime, $eEventID);

    while ($getEventsStmt->fetch()) {
        $thisArray = array(
            "title" => htmlentities($eTitle),
            "date" => htmlentities($eDate),
            "time" => htmlentities($eTime),
            "event_id" => htmlentities($eEventID)
        );
        array_push($my_array, $thisArray);
        //$counter = $counter + 1;
    }
    $getEventsStmt->close();

    echo json_encode($my_array);
}

// create new event
if ($json_obj['request'] == "createEvent") {
    // variables
    $title = $json_obj['title'];
    $date = $json_obj['date'];
    $time = $json_obj['time'];
    $user_id = $_SESSION['user_id'];

    // insert new event data
    $insertEventStmt = $conn->prepare("insert into calendar_events (title, date, time, user_id) values (?, ?, ?, ?)");
    $insertEventStmt->bind_param("sssi", $title, $date, $time, $user_id);
    if (!$insertEventStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
        $my_array = array(
            "success" => false,
        );
    }
    else {
        $my_array = array(
            "success" => true
        );
    }
    $insertEventStmt->execute();
    $insertEventStmt->close();

    echo json_encode($my_array);
}

// edit an existing event
if ($json_obj['request'] == "editEvent") {
    // variables
    $my_array = array();
    // access db
    $editEventStmt = $conn->prepare("update calendar_events set title = ?, date = ?, time = ? where event_id = ?");
    $editEventStmt->bind_param("sssi", $json_obj['title'], $json_obj['date'], $json_obj['time'], $json_obj['event_id']);
    if (!$editEventStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
        $my_array = array(
            "success" => false
        );
    }
    else {
        $my_array = array(
            "success" => true
        );
    }
    $editEventStmt->execute();
    $editEventStmt->close();

    echo json_encode($my_array);
}


// delete an event
// TODO: incorporate $_SESSION['current_user']
if ($json_obj['request'] == "deleteEvent") {
    // variables
    $my_array = array("test" => "test");
    // delete event data
    $deleteEventStmt = $conn->prepare("delete from calendar_events where event_id = ?");
    if (!$deleteEventStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
    }

    $deleteEventStmt->bind_param("s", $json_obj['event_id']);
    $deleteEventStmt->execute();
    $deleteEventStmt->close();

    echo json_encode($my_array);
}

// share calendar with another user
if ($json_obj['request'] == "shareCalendar") {
    // variables
    $my_array = array("test" => "test");
    $shareName = $json_obj['shareName'];
    $shareNameID = 0;
    // access db for user_id of shareName
    $shareNameIDStmt = $conn->prepare("select user_id from users where username = ?");
    if (!$shareNameIDStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
    }

    $shareNameIDStmt->bind_param("s", $shareName);
    $shareNameIDStmt->execute();
    $shareNameIDStmt->bind_result($shareID);

    // put user_id into $shareNameID
    while ($shareNameIDStmt->fetch()) {
        $shareNameID = htmlentities($shareID);
    }

    $shareNameIDStmt->close();

    // insert new permission into shared_calendars
    $newShareStmt = $conn->prepare("insert into shared_calendars (user_id, shared_id) values (?, ?)");
    if (!$newShareStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
    }

    $newShareStmt->bind_param("ii", $_SESSION['user_id'], $shareNameID);
    $newShareStmt->execute();
    $newShareStmt->close();
    
    echo json_encode($my_array);
}

// create new user group
if ($json_obj['request'] == "newGroup") {
    // variables
    $my_array = array();
    $username2 = $json_obj['username2'];
    if (isset($json_obj['username3'])) {
        $username3 = $json_obj['username3'];
    }
    $user2 = '';
    $user3 = '';
    // get user_ids from db
    $getIdsStmt = $conn->prepare("select user_id from users where username = ?");
    if (!$getIdsStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
    }

    $getIdsStmt->bind_param("s", $username2);
    $getIdsStmt->execute();
    $getIdsStmt->bind_result($getUser2);

    while ($getIdsStmt->fetch()) {
        $user2 = htmlentities($getUser2);
    }

    $getIdsStmt->close();

    // get user_ids from db
    $getIdsStmt = $conn->prepare("select user_id from users where username = ?");
    //if (!$getIdsStmt) {
        //printf("Query Prep Failed: %s\n", $conn->error);
    //}

    $getIdsStmt->bind_param("s", $username3);
    $getIdsStmt->execute();
    $getIdsStmt->bind_result($getUser3);

    while ($getIdsStmt->fetch()) {
        $user3 = htmlentities($getUser3);
    }

    $getIdsStmt->close();

    // insert values into db
    $newGroupStmt = $conn->prepare("insert into groups (user1_id, user2_id, user3_id) values (?, ?, ?)");
    if (!$newGroupStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
    }

    $newGroupStmt->bind_param("iii", $_SESSION['user_id'], $user2, $user3);
    $newGroupStmt->execute();
    $newGroupStmt->close();

    echo json_encode($my_array);
}

// create new group event
if ($json_obj['request'] == "createGroupEvent") {
    // variables
    $my_array = array("test" => "test");
    // insert values into db
    $newGroupEventStmt = $conn->prepare("insert into group_events (group_id, title, date, time) values (?, ?, ?, ?)");
    if (!$newGroupEventStmt) {
        printf("Query Prep Failed: %s\n", $conn->error);
    }

    $newGroupEventStmt->bind_param("isss", $json_obj['group_id'], $json_obj['title'], $json_obj['date'], $json_obj['time']);
    $newGroupEventStmt->execute();
    $newGroupEventStmt->close();

    echo json_encode($my_array);
}

?>