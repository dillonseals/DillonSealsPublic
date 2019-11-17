<?php
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
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Calender</title>
    <link rel="stylesheet" type="text/css" href="./calendar.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js" type="text/javascript"></script>
</head>
<body>
    <!-- User Login/Display Div -->
    <div class="inline" id="User">

    </div>

    <!-- Create New Events Info Div -->
    <div class="inline newEventsDiv" id="newEventInputs">

    </div>

    <!-- Calender Container Div -->
    <br><div class="calendar-container">
        <!--    Row of Days Names -->
        <div class="titleRow calendarDivider">
            <div id="titleRowContent">
            </div>
        </div>
        <div class="calendarDivider dayOfWeek col1">
            <p>Sunday</p>
        </div>
        <div class="calendarDivider dayOfWeek col2">
            <p>Monday</p>
        </div>
        <div class="calendarDivider dayOfWeek col3">
            <p>Tuesday</p>
        </div>
        <div class="calendarDivider dayOfWeek col4">
            <p>Wednesday</p>
        </div>
        <div class="calendarDivider dayOfWeek col5">
            <p>Thursday</p>
        </div>
        <div class="calendarDivider dayOfWeek col6">
            <p>Friday</p>
        </div>
        <div class="calendarDivider dayOfWeek col7">
            <p>Saturday</p>
        </div>

        <!-- Week One -->
        <div class="calendarDivider week1 col1">
        </div>
        <div class="calendarDivider week1 col2">
        </div>
        <div class="calendarDivider week1 col3">
        </div>
        <div class="calendarDivider week1 col4">
        </div>
        <div class="calendarDivider week1 col5">
        </div>
        <div class="calendarDivider week1 col6">
        </div>
        <div class="calendarDivider week1  col7">
        </div>

        <!-- Week Two -->
        <div class="calendarDivider week2 col1">
        </div>
        <div class="calendarDivider week2 col2">
        </div>
        <div class="calendarDivider week2 col3">
        </div>
        <div class="calendarDivider week2 col4">
        </div>
        <div class="calendarDivider week2 col5">
        </div>
        <div class="calendarDivider week2 col6">
        </div>
        <div class="calendarDivider week2  col7">
        </div>

        <!-- Week Three -->
        <div class="calendarDivider week3 col1">
        </div>
        <div class="calendarDivider week3 col2">
        </div>
        <div class="calendarDivider week3 col3">
        </div>
        <div class="calendarDivider week3 col4">
        </div>
        <div class="calendarDivider week3 col5">
        </div>
        <div class="calendarDivider week3 col6">
        </div>
        <div class="calendarDivider week3  col7">
        </div>

        <!-- Week Four -->
        <div class="calendarDivider week4 col1">
        </div>
        <div class="calendarDivider week4 col2">
        </div>
        <div class="calendarDivider week4 col3">
        </div>
        <div class="calendarDivider week4 col4">
        </div>
        <div class="calendarDivider week4 col5">
        </div>
        <div class="calendarDivider week4 col6">
        </div>
        <div class="calendarDivider week4  col7">
        </div>

        <!-- Week Five -->
        <div class="calendarDivider week5 col1">
        </div>
        <div class="calendarDivider week5 col2">
        </div>
        <div class="calendarDivider week5 col3">
        </div>
        <div class="calendarDivider week5 col4">
        </div>
        <div class="calendarDivider week5 col5">
        </div>
        <div class="calendarDivider week5 col6">
        </div>
        <div class="calendarDivider week5  col7">
        </div>
        
    </div>
 
    <script>
        // wiki calendar-generation functions
        (function(){
            Date.prototype.deltaDays=function(c){
                return new Date(this.getFullYear(),this.getMonth(),this.getDate()+c)
            };
            Date.prototype.getSunday=function(){
                return this.deltaDays(-1*this.getDay())
            }
        })();
        // wiki stuff
        function Week(c){
            this.sunday=c.getSunday();
            this.nextWeek=function(){
                return new Week(this.sunday.deltaDays(7))
                };
            this.prevWeek=function(){
                return new Week(this.sunday.deltaDays(-7))
            };
            this.contains=function(b){
                return this.sunday.valueOf()===b.getSunday().valueOf()
            };
            this.getDates=function(){
                for(var b=[],a=0;7>a;a++)b.push(this.sunday.deltaDays(a));
                return b
            }
        }
        // wiki stuff
        function Month(c,b){
            this.year=c;
            this.month=b;
            this.nextMonth=function(){
                return new Month(c+Math.floor((b+1)/12),(b+1)%12)
            };
            this.prevMonth=function(){
                return new Month(c+Math.floor((b-1)/12),(b+11)%12)
            };
            this.getDateObject=function(a){
                return new Date(this.year,this.month,a)
            };
            this.getWeeks=function(){
                var a=this.getDateObject(1),b=this.nextMonth().getDateObject(0),c=[],a=new Week(a);
                for(c.push(a);!a.contains(b);)a=a.nextWeek(),c.push(a);
                return c
            }
        };
        // wiki stuff
        var today = new Date();
        var currentMonth = new Month(today.getFullYear(), today.getMonth());

        // This updateCalendar() function only alerts the dates in the currently specified month.  You need to write
        // it to modify the DOM (optionally using jQuery) to display the days and weeks in the current month.
        // TODO: add event fetcher function
        function updateCalendar(){
            var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            document.getElementById('titleRowContent').innerHTML = "&nbsp&nbsp" + months[currentMonth.month] + ",&nbsp" + currentMonth.year + "&nbsp" +
            '<button class="hover" type="button" id="prev_month_btn">Prev Month</button>&nbsp&nbsp' +
            '<button class="hover" type="button" id="next_month_btn">Next Month</button>';
            
            document.getElementById("next_month_btn").addEventListener("click", function(event){
                currentMonth = currentMonth.nextMonth();
                updateCalendar(); // Whenever the month is updated, we'll need to re-render the calendar in HTML
                //alert("The new month is "+currentMonth.month+" "+currentMonth.year);
            }, false);

            // Change the month when the "prev" button is pressed
            document.getElementById("prev_month_btn").addEventListener("click", function(event){
                currentMonth = currentMonth.prevMonth();
                updateCalendar(); // Whenever the month is updated, we'll need to re-render the calendar in HTML
                //alert("The new month is "+currentMonth.month+" "+currentMonth.year);
            }, false);
            
            var weeks = currentMonth.getWeeks();
            
            for(var w in weeks){
                var days = weeks[w].getDates();
                // days contains normal JavaScript Date objects.
                
                //alert("Week starting on "+days[0]);
                
                for(var d in days){
                    // You can see console.log() output in your JavaScript debugging tool, like Firebug,
                    // WebWit Inspector, or Dragonfly.
                    console.log(days[d].toISOString());
                }
            }
            var today = new Date();
            var actualMonth = new Month(today.getFullYear(), today.getMonth());

            // wiki stuff
            var weekNum = 1;
            for(var w in weeks) {
                for(var dayNum = 0; dayNum < 7; dayNum++) {
                    // day = weeks[w].getDates()[dayNum].getDate()
                    // month = currentMonth.month -- Jan = month 0, Dec = month 11
                    // year = currentMonth.year
                    if (months[currentMonth.month] == months[actualMonth.month] && currentMonth.year == actualMonth.year && today.getDate() == weeks[w].getDates()[dayNum].getDate()) {
                        document.getElementsByClassName('week' + weekNum)[dayNum].innerHTML = "<b>" + weeks[w].getDates()[dayNum].getDate() + "</b>";
                    }
                    else {
                        document.getElementsByClassName('week' + weekNum)[dayNum].innerHTML = weeks[w].getDates()[dayNum].getDate();
                    }
                    // below calls eventFetcher(Month, Day, Year) where Month, Day, and Year is the current date displayed in div currently being updated in calendar
                    eventFetcher(weeks[w].getDates()[dayNum].getMonth(), weeks[w].getDates()[dayNum].getDate(), weeks[w].getDates()[dayNum].getFullYear(), 'week' + weekNum, dayNum);
                }
                weekNum++;
            }
        }

        // update login/register user div
        // 'login' arg fetched from login.php
        // TODO: register functionality - not technically required
        function updateUserDiv(login){
            // user logged-in
            if (login == true) {
                document.getElementById('User').className += "inline UserDiv";
                document.getElementById('User').innerHTML = '<div class="align_right inline"><h3 class="inline">' + window.username + '&nbsp&nbsp</h3>' +
                '<button class="button inline hover" type="button" id="logout_btn">Logout</button></div><br>' +
                '<form class = inline hover><input class="inline hover" type="text" id="share" placeholder="username" />' +
                '<button class="button inline hover" type="button" id="share_button">Share Calendar</button></form><br>' +
                '<form class="inline hover"><input class="inline hover" type="text" id="new_group_user1" placeholder="username 1" />' +
                '<input class="inline hover" type="text" id="new_group_user2" placeholder="username 2 (optional)" />' +
                '<button class="button inline hover" type="button" id="new_group_button">Create New Group</button></form></div><br>';
                if (typeof newevent != 'undefined') {
                    document.getElementById('newEventInputs').innerHTML = '<p style="color:blue">Event Created Successfully!</p><br><div style="align-self: Center;" id="event_btn_div">' +
                    '<button class="button inline hover" type="button" id="createEvent">Create New Event</button>' +
                    '<button class="button inline hover" type="button" id="createGroupEvent">Create New Group Event</button></div>';
                    delete window.newevent;
                }
                else {
                    document.getElementById('newEventInputs').innerHTML = '<br><div style="align-self: Center;" id="event_btn_div">' +
                    '<button class="button inline hover" type="button" id="createEvent">Create New Event</button><br>' + 
                    '<button class="button inline hover" type="button" id="createGroupEvent">Create New Group Event</button></div>';
                }
                // event listeners
                document.getElementById('logout_btn').addEventListener('click', logoutUser, false);
                document.getElementById('createEvent').addEventListener('click', showNewInputs, false);
                document.getElementById('createGroupEvent').addEventListener('click', showNewGroupEventInputs, false);
                document.getElementById('share_button').addEventListener('click', shareCalendar, false);
                document.getElementById('new_group_button').addEventListener('click', newGroup, false);
            }
            // login or register div
            else {
                document.getElementById("User").className = "inline";
                if (typeof failedLogin != 'undefined') {
                    document.getElementById('User').innerHTML = '<h3>Login:</h3><form><label>Username: &nbsp;<input type = "text" id="username"/></label>&nbsp;&nbsp;' + 
                    '<label>Password: &nbsp;<input type = "password" id="password"/></label>&nbsp;&nbsp;' +
                    '<button class="hover inline button" type="button" id="login_btn">Login</button></form>' +
                    '<h3>Register New User:</h3><form><label>Username: &nbsp;<input type = "text" id="new_username"/></label>&nbsp;&nbsp;' + 
                    '<label>Password: &nbsp;<input type = "password" id="new_password"/></label>&nbsp;&nbsp;' +
                    '<button class="hover inline button" type="button" id="register_btn">Register</button></form><br>' +
                    '<p class="error">' + failedLogin + '</p>';
                }
                else {
                    document.getElementById('User').innerHTML = '<h3>Login:</h3><form><label>Username: &nbsp;<input type = "text" id="username"/></label>&nbsp;&nbsp;' + 
                    '<label>Password: &nbsp;<input type = "password" id="password"/></label>&nbsp;&nbsp;' +
                    '<button class="hover inline button" type="button" id="login_btn">Login</button></form>' +
                    '<h3>Register New User:</h3><form><label>Username: &nbsp;<input type = "text" id="new_username"/></label>&nbsp;&nbsp;' + 
                    '<label>Password: &nbsp;<input type = "password" id="new_password"/></label>&nbsp;&nbsp;' +
                    '<button class="hover inline button" type="button" id="register_btn">Register</button></form><br>';
                }

                document.getElementById('login_btn').addEventListener('click', loginUser, false);
                document.getElementById('register_btn').addEventListener('click', registerUser, false);
            }
        }

        // Attempt to login a user
        const loginUser = function() {
            if (typeof failedLogin != 'undefined') {
                delete window.failedLogin;
            }
            const data = { request: 'loginUser', username: document.getElementById('username').value, password: document.getElementById('password').value};
            fetch("./login.php", {
                method: 'POST',
                body: JSON.stringify(data),
            })
            .then(res => res.json())
            .then(function(response) {
                console.log(response);
                if (response.login == true){
                    window.username = document.getElementById('username').value;
                }
                else {
                    window.failedLogin = response.failedLogin;
                }
                updateUserDiv(response.login);
                updateCalendar();
            })
            .catch(function(error) {
            });
        }

        // Attempt to register a new user
        // TODO - display name in corner on register
        function registerUser(){
            const data = {request: 'registerUser', username: document.getElementById('new_username').value, password: document.getElementById('new_password').value};
            console.log("registerUser");
            console.log(data.username);
            console.log(document.getElementById('new_username').value);
            fetch("./login.php", {
                method: "POST",
                body: JSON.stringify(data),
            })
            .then(res => res.json())
            .then(function(response) {
                if (response.register) {
                    window.username = response.username;
                    console.log("window.username = " + window.username);
                    updateUserDiv(true);
                    eventFetcher();
                    updateCalendar();
                } else {
                    window.failedRegister = response.failedRegister;
                }
                /* if (response.register) {
                    updateUserDiv(false);
                    eventFetcher();
                    updateCalendar();
                } */
            })
            .catch(function(error) {
            });
        }

        // logout the user
        const logoutUser = function() {
            const data = {request: 'logoutUser'}
            fetch("./login.php", {
                method: "POST",
                body: JSON.stringify(data),
            })
            .then(res => res.json())
            .then(function(response) {
                updateUserDiv(response.login);
                document.getElementById("newEventInputs").innerHTML = '';
            })
            .catch(function(error) {
                console.log("Found an error " + error);
            });
            updateCalendar();
        }

        // Checking if a user is logged in
        const checkUser = function(){
            const data = { request: 'checkUser', checkUser: true };
            fetch("./login.php", {
                method: 'POST',
                body: JSON.stringify(data),
            })
            .then(res => res.json())
            .then(function(response) {
                console.log(response);
                if (response.user_set == true) {
                    window.username = response.current_user;
                    window.login = true;
                }
                else {
                    window.login = false;
                }
                updateUserDiv(login);
            })
            .catch(error => console.error('Error:', error))
        }

        // Fetching Events
        // called each time through loop as it's updating content for each div in calendar
        // The date represented by current div being updated is input into this function
        const eventFetcher = function(thisMonth, thisDay, thisYear, thisWeek, dayOfWeek) {
            let thisDate = (thisMonth + 1).toString().concat("/", thisDay.toString(), "/", thisYear.toString());
            const data = {request: 'getEvents', date: thisDate};
            fetch("./login.php", {
                method: "POST",
                body: JSON.stringify(data),
            })
            .then(res => res.json())
            .then(function(response) {
                console.log(response);
                document.getElementsByClassName(thisWeek)[dayOfWeek].appendChild(document.createElement("Br"));
                document.getElementsByClassName(thisWeek)[dayOfWeek].appendChild(document.createElement("Br"));
                for (i = 0; i < response.length; i++) {
                    console.log(response[i]);
                    var newNode = document.getElementsByClassName(thisWeek)[dayOfWeek].appendChild(document.createElement("Div"));
                    newNode.className = "eventDiv";
                    newNode.id = response[i].event_id;
                    newNode.innerHTML = '&nbsp*' + response[i].time + "&nbsp-&nbsp" + response[i].title;
                    document.getElementById(response[i].event_id).addEventListener('click', editEvent, false);
                }
            })
            .catch(error => console.error('Error:', error))
        }

        const editEvent = function() {
            window.currentEventID = this.id;
            console.log("Event\xa0" + this.id + "\xa0has been Clicked!!");
            document.getElementById("newEventInputs").innerHTML = '<p>Edit Event:</p><div><form class="inline" id="editEventForm">' +
            '<label for="newTitle">Title</label>&nbsp<input type="text" name="newTitle" placeholder="Text" id="newTitle" />&nbsp&nbsp' +
            '<label for="newDate">Date</label>&nbsp<input type="text" name="newDate" placeholder="MM/DD/YYYY" id="newDate" />&nbsp&nbsp' +
            '<label for="newTime">Time</label>&nbsp<input type="text" name="newTime" placeholder="eg. 0900, 1200, 1830" id="newTime" />&nbsp&nbsp' +
            '<button class="button hover" type="button" id="change" name="change">Change</button>&nbsp&nbsp' +
            '<button class="button hover" type="button" id="delete" name="delete">Delete</button>&nbsp&nbsp' +
            '<button class="button hover" type="button" id="cancel" name="cancel">Cancel</button></div><br>';
            document.getElementById("change").addEventListener("click", changeEvent, false);
            document.getElementById("delete").addEventListener("click", deleteEvent, false);
            document.getElementById("cancel").addEventListener("click", checkUser, false);
        }

        const changeEvent = function() {
            console.log('ChangeEvent Button Was pressed');
            console.log(currentEventID);
            let title = document.getElementById("newTitle").value;
            let date = document.getElementById("newDate").value;
            let time = document.getElementById("newTime").value;
            const data = {request: 'editEvent', event_id: currentEventID, title: title, date: date, time: time};
            fetch("./login.php", {
                method: "POST",
                body: JSON.stringify(data),
            })
            .then(res => res.json())
            .then(function(response) {
                console.log(response);
                updateCalendar();
            })
            .catch(error => console.error('Error:', error))
        }

        const deleteEvent = function() {
            console.log('deleteEvent Button Was pressed');
            const data = {request: 'deleteEvent', event_id: currentEventID};
            fetch("./login.php", {
                method: "POST",
                body: JSON.stringify(data),
            })
            .then(res => res.json())
            .then(function(response) {
                console.log(response);
                updateCalendar();
            })
            .catch(error => console.error('Error:', error))
        }

        // show input fields for new event creation
        // TODO: security stuff (filter inputs)
        const showNewInputs = function() {
            document.getElementById("newEventInputs").innerHTML = '<p>* Indicates Required Field</p><div><form class="inline" id="newEventForm">' +
            '<label for="newTitle">Title*</label>&nbsp<input type="text" name="newTitle" placeholder="Text" id="newTitle" />&nbsp&nbsp' +
            '<label for="newDate">Date*</label>&nbsp<input type="text" name="newDate" placeholder="MM/DD/YYYY" id="newDate" />&nbsp&nbsp' +
            '<label for="newTime">Time*</label>&nbsp<input type="text" name="newTime" placeholder="eg. 0900, 1200, 1830" id="newTime" />&nbsp&nbsp' +
            '<button class="button hover" type="button" id="create" name="Create">Create</button>' +
            '<button class="button hover" type="button" id="cancel" name="cancel">Cancel</button></div><br>';
            document.getElementById("create").addEventListener("click", createEvent, false);
            document.getElementById("cancel").addEventListener("click", checkUser, false);
        }

        // display inputs for new group event
        const showNewGroupEventInputs = function() {
            document.getElementById("newEventInputs").innerHTML = '<p>* Indicates Required Field</p><div><form class="inline" id="newGroupEventForm">' +
            '<label for="groupID">Group ID*</label>&nbsp<input type="number" name="groupID" placeholder="ID" id="groupID" />&nbsp&nbsp' +
            '<label for="newGroupTitle">Title*</label>&nbsp<input type="text" name="newTitle" placeholder="Text" id="newGroupTitle" />&nbsp&nbsp' +
            '<label for="newGroupDate">Date*</label>&nbsp<input type="text" name="newDate" placeholder="MM/DD/YYYY" id="newGroupDate" />&nbsp&nbsp' +
            '<label for="newGroupTime">Time*</label>&nbsp<input type="text" name="newTime" placeholder="eg. 0900, 1200, 1830" id="newGroupTime" />&nbsp&nbsp' +
            '<button class="button hover" type="button" id="groupCreate" name="Create">Create</button>' +
            '<button class="button hover" type="button" id="cancel" name="cancel">Cancel</button></div><br>';
            document.getElementById("groupCreate").addEventListener("click", createGroupEvent, false);
            document.getElementById("cancel").addEventListener("click", checkUser, false);
        }

        // create new event
        // TODO - display event on creation
        const createEvent = function() {
            let titlePattern = /\w+/;
            let datePattern = /\d\d\/\d\d\/\d\d\d\d/;
            let timePattern = /\d\d\d\d/;
            let blank = /^\s*$/;
            // get values from new inputs form
            let newTitle = document.getElementById("newTitle").value;
            let newDate = document.getElementById("newDate").value;
            let newTime = document.getElementById("newTime").value;
            // regex testing
            if (titlePattern.test(newTitle) && datePattern.test(newDate) && timePattern.test(newTime)) {
                const data = {request: 'createEvent', title: newTitle, date: newDate, time: newTime};
                fetch("./login.php", {
                    method: "POST",
                    body: JSON.stringify(data),
                })
                .then(res => res.json())
                .then(function(response) {
                    // reset user div
                    if (response.success == true) {
                        window.newevent = true;
                        console.log(newevent);
                    }
                    updateUserDiv(true);
                    updateCalendar();
                })
                .catch(error => console.error('Error:', error))
            } else {
                console.log("regex not matched");
            }
            //updateUserDiv(true);
            //updateCalendar();
        }

        // create new group event
        const createGroupEvent = function() {
            let idPattern = /\d+/;
            let titlePattern = /\w+/;
            let datePattern = /\d\d\/\d\d\/\d\d\d\d/;
            let timePattern = /\d\d\d\d/;
            let blank = /^\s*$/;
            // get values from new inputs form
            let group_id = document.getElementById("groupID").value;
            let newGroupTitle = document.getElementById("newGroupTitle").value;
            let newGroupDate = document.getElementById("newGroupDate").value;
            let newGroupTime = document.getElementById("newGroupTime").value;
            // regex testing
            if (idPattern.test(group_id) && titlePattern.test(newGroupTitle) && datePattern.test(newGroupDate) && timePattern.test(newGroupTime)) {
                const data = {request: 'createGroupEvent', group_id: group_id, title: newGroupTitle, date: newGroupDate, time: newGroupTime};
                fetch("./login.php", {
                    method: "POST",
                    body: JSON.stringify(data),
                })
                .then(res => res.json())
                .then(function(response) {
                    // reset user div
                    if (response.success == true) {
                        window.newevent = true;
                        console.log(newevent);
                    }
                    updateUserDiv(true);
                    updateCalendar();
                })
                .catch(error => console.error('Error:', error))
            } else {
                console.log("regex not matched");
            }
        }

        // share calendar with another user
        const shareCalendar = function() {
            let shareName = document.getElementById("share").value;
            const data = {request: "shareCalendar", shareName: shareName}
            fetch("./login.php", {
                method: "POST",
                body: JSON.stringify(data),
            })
            .then(res => res.json())
            .then(function(response) {
                updateUserDiv(true);
                updateCalendar();
            })
            .catch(error => console.error('Error:', error))
        }

        // create new user group
        // note - if no third user select, user3_id = 0
        const newGroup = function() {
            let username1 = document.getElementById("new_group_user1").value;
            let username2 = document.getElementById("new_group_user2").value;
            const data = {request: "newGroup", username2: username1, username3: username2}
            fetch("./login.php", {
                method: "POST",
                body: JSON.stringify(data),
            })
            .then(res => res.json())
            .then(function(response) {
                console.log(response);
                updateUserDiv(true);
                updateCalendar();
            })
            .catch(error => console.error('Error:', error))
        }

        // Updating Calendar Display and Login status on Page Load
        document.addEventListener("DOMContentLoaded", updateCalendar, false);
        document.addEventListener("DOMContentLoaded", checkUser, false);
    </script>
</body>
</html>