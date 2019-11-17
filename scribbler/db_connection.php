<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- https://stackoverflow.com/questions/18301745/how-to-set-up-a-favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="news_logo.ico"/>
    </head>

    <body>
        <!--  https://www.cloudways.com/blog/connect-mysql-with-php/#createfolder  -->
        <?php
        
        function OpenCon()
        {
        $dbhost = "localhost";
        $dbuser = "wustl_inst";
        $dbpass = "wustl_pass";
        $db = "Scribbler";

        $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);

        return $conn;
        }

        function CloseCon($conn)
        {
        $conn -> close();
        }
        
        /*  https://stackoverflow.com/questions/9953157/mysql-php-check-if-row-exists  */
        function checkUserExist($conn, $username){
            $sql = "SELECT 1 FROM scribbler_users WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($found);
            $stmt->fetch();
            if ($found) {
                return 'Assigned';
            } else {
                return 'Unassigned';
            }
        }
        ?>
    </body>
</html>