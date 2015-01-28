<?php

    // ----------- Database connection ----------//
    $mysql_db_hostname = "127.0.0.1";
    $mysql_db_user = "nest_user";
    $mysql_db_password = "9qqTMiJqU3xCtqGcxHYp";
    $mysql_db_database = "nest";  
    $con = mysqli_connect($mysql_db_hostname, $mysql_db_user, $mysql_db_password) 
                       or die("Failed to connect to MySQL: " . mysqli_connect_error());
    mysqli_select_db($con, $mysql_db_database) or die("Could not select database" . mysqli_connect_error());
   
?>
