<?php

$config = array('db_ip' => '127.0.0.1',  // Container value: GabeThermDB
		'db_user' => 'root',
		'db_pass' => 'L@mpMyG@B',
		'db_name' => 'nest',
		# 'nest_user' => 'thegabeman@gmail.com',   // <-- Now done through environment setting for container
		# 'nest_pass' => 'N35tG@b',     // <-- Now done through environment setting for container
		'local_tz' => 'Europe/Amsterdam' // see http://php.net/manual/en/timezones.php
		);


    define('DBHOSTNAME', getenv('dbhostname'));
    // DEBUG ==> printf("\n" . $config['db_ip'] . " Array config 0");
    
    # Making this DockerCompatible.
    # db_hostname is the name of the docker container in which MySQL runs
 
    // ----------- Database connection ----------//
    $mysql_db_hostname = $config['db_ip'];
    $mysql_db_user = "root";
    $mysql_db_password = "L@mpMyG@B";
    $mysql_db_database = "nest";  
    $con = mysqli_connect($mysql_db_hostname, $mysql_db_user, $mysql_db_password) 
                       or die("Failed to connect to MySQL: " . mysqli_connect_error());
    mysqli_select_db($con, $mysql_db_database) or die("Could not select database" . mysqli_connect_error());
   

?>

