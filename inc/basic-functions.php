<?php

/*
 * This PHP file contains some basic functions I use throughout the project
 */


 function objectToArray($d) {
    if (is_object($d)) 
    {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }
 
 if (is_array($d)) 
     {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return array_map(__FUNCTION__, $d);
    }
    else 
    {
        // Return array
        return $d;
    }
 }

//returns true, if domain is availible, false if not
function isDomainAvailible($domain)
{
       //check, if a valid url is provided
       if(!filter_var($domain, FILTER_VALIDATE_URL))
       {
               return false;
       }

       //initialize curl
       $curlInit = curl_init($domain);
       curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
       curl_setopt($curlInit,CURLOPT_HEADER,true);
       curl_setopt($curlInit,CURLOPT_NOBODY,true);
       curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

       //get answer
       $response = curl_exec($curlInit);

       curl_close($curlInit);

       if ($response) return true;

       return false;
}

// Check if there is an online connection
/*
 * Let's first see if we're online and NEST website is reachable
 */
function CheckIfOnline($NestUser)
{
    $URLArray = array(
        "NEST" => "http://home.nest.com/v3/mobile/" . $NestUser,
        "Google" => "http://www.google.com",
        "Google CDN" => "http://ajax.googleapis.com",
        // "Fout" => "http://doetniks.vanzanten.local",
        );

    // How many sites we check are online? If more than one, I asume 
    $URLOKArray = array();
    $Online = 0;
    foreach( $URLArray as $CheckURL )
    {
        if (!isDomainAvailible($CheckURL))
        { $URLOKArray[] = 0;}else{$URLOKArray[] = 1;$Online++;}
    }
    unset( $CheckURL);

    // print_r( $URLOKArray);
    // printf( $Online);

    // If not all three URLs are reachable, we write to our SQL log
    if( $Online < 3){
        WriteToLogDB($URLOKArray);
        }
    
}


// Write to logDB if sites were online
function WriteToLogDB($URLOKArray)
{
    require 'open-database.php';
    
    // Write to log table
    $sql = "INSERT INTO `ConnectionLog`(`timestamp`,`url01`,`url02`, `url03`) VALUES( now(), $URLOKArray[0], $URLOKArray[1],$URLOKArray[2]);";
    printf($sql);

    if (!mysqli_query($con,$sql))
      { die('Error: ' . mysqli_error($con)); }
    $results = mysqli_query($con,$sql);
    
    
    printf("%d Row inserted.\n", mysqli_affected_rows($con));
    printf("Error Nr.\n", mysqli_errno($con));
    printf("Error  \n",  mysqli_error($con));
    
    mysqli_close($con);
}

// Function k_to_h is converting from Kelvin to Human format, either Fahrenheit or Celcius
function k_to_h($temp) {
    global $config;
    if( $config['CorF'] == 'C')
        { return k_to_c($temp); }
    else
        { return k_to_f($temp); }
}

function k_to_f($temp) {
    if ( !is_numeric($temp) ) { return false; }
    return round((($temp - 273.15) * 1.8) + 32);
}

function k_to_c($temp) {
	if ( !is_numeric($temp) ) { return false; }
	return round(($temp - 273.15));
}


?>

