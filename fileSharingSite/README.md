# File Sharing Site

Link - http://ec2-18-222-177-5.us-east-2.compute.amazonaws.com/~dillonrayseals/homeScreen.php

Create new "account" functionality
    -   Users can input a new username they would like to create on the home page
    -   Posts input to newUser.php
    -   Checks if input in users.txt
    -   If username in users.txt,
        -   Displays error message
        -   Prompts user to return to home page
    -   If it is free,
        -   Appends the username to users.txt in secure location
        -   Creates a new directory for user
            -   Sets appropriate permissions
        -   Displays a "success" message
        -   Prompts user to return to home page
