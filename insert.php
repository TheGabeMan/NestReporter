<?php

require 'inc/config.php';
require 'inc/class.db.php';
require 'nest-api-master/nest.class.php';
require 'inc/basic-functions.php';

define('USERNAME', $config['nest_user']);
define('PASSWORD', $config['nest_pass']);

$logdate = date("Y-m-d H:i:s");
printf("\n$logdate ***** New pull of date *****");

date_default_timezone_set($config['local_tz']);

// printf("\nNest insert.php\n");

/*
 * Let's first check if we're online
 */

// CheckIfOnline($config['nest_user']);

$nest = new Nest();


/*
 * Get the data we  want to use
 */
$logdate = date("Y-m-d H:i:s");
printf("\n$logdate Infos is leeg.");
$InfosTry = 1;
While( empty($infos) )
{
    $logdate = date("Y-m-d H:i:s");
    printf("\n$logdate Get info try: $InfosTry");
    $infos = $nest->getDeviceInfo();
    $InfosTry=+1;
    if( $InfosTry == 10)
    {
        $logdate = date("Y-m-d H:i:s");
        printf("\n$logdate Exit Infos try $InfosTry");
        break;
    }
    
}

// Current date and time
$date = date("Y-m-d H:i:s");
$logRow = $date . "," . $infos->network->last_connection;

/*
 * How to find your City ID on OpenWeatherMap.org: http://openweathermap.org/find?q=
 *
 * 
 * Need to put this in an include as function, to be able to call it more often. Sometimes zero values are returned.
 *  */

$WeatherTry = 1;
$logdate = date("Y-m-d H:i:s");
printf("\n$logdate Weather fetch first try for openweathermap.org.");
while ( empty($weather->name))
{
    $locations = $nest->getUserLocations();
    $jsonurl = "http://api.openweathermap.org/data/2.5/weather?q=" . $locations[0]->postal_code . "," . $locations[0]->country . "&APPID=c10eca5ef3de05f43173a50f922831d8";
    $json = file_get_contents($jsonurl);
    $weather = json_decode($json);
    if( empty($weather->name) )
    {
        $logdate = date("Y-m-d H:i:s");
        printf("\n$logdate Weather fetch returned empty " . $WeatherTry );
        sleep(10);
        
        $WeatherTry =+1;
        If( $WeatherTry == 10){
            $logdate = date("Y-m-d H:i:s");
            printf("\n$logdate Tried $WeatherTry times, now giving up.");
            break;
        }
        // Now get latest record from SQL DB and import those values and write them again to not mess up the graphs / stats
    }
}   
$logdate = date("Y-m-d H:i:s");
printf("\n$logdate Weather fetch exited with ");
print_r( $weather->name );
printf("\n$logdate End of weather name");        

$logRow = $logRow . "," . $weather->name . "," . $weather->weather[0]->main . "," . $weather->weather[0]->description;


/*
 * There is a difference in the object returned depending on the state of Away.
 * When [current_state]->auto_away or [current_state]->manual_away = 1, the [target] section changes
 * away = 1 --->  [target]->temperature->Array [0] en Array[1]
 * away = 0 --->  [target]->temperature = 18
 */

if( $infos->current_state->auto_away==1 or $infos->current_state->manual_away==1 )
{
    $logdate = date("Y-m-d H:i:s");
    printf("\n$logdate Away status is 1");
    $TargetTemp = $infos->target->temperature[0];
} else {
    $logdate = date("Y-m-d H:i:s");
    printf("\n$logdate Away status is 0");
    $TargetTemp = $infos->target->temperature;
}
$logdate = date("Y-m-d H:i:s");
printf("\n$logdate TargetTemp = " . $TargetTemp);


/*
 * - Temperatures read from the NEST device are in either Celcius or Fahrenheit, depending on user setting
 * - Temperatures read from OpenWeatherMaps are in Kelvin
 * - All temperatures stored in the database will be in Kelvin
 * 
 */

// Is this NEST set to Celcius of Fahrenheit?
$CelOrFahr = $nest->getDeviceTemperatureScale();
if ($CelOrFahr =="C")
{
    // Convert NEST device temp values to Kelvin for storing in database
    // Although officially 1 Kelvin = -273.15 C, we're using - 273C otherwise all C degrees would be xx.85
    $NestTargetTempKelvin = ($TargetTemp + 273);
    $NestCurrentTempKelvin = ($infos->current_state->temperature + 273);

    
}else
{
    // Convert NEST device temp values to Kelvin for storing in database
    // Although officially 1 Kelvin = -273.15 C, we're using - 273C otherwise all C degrees would be xx.85
    $NestTargetTempKelvin = (((( $TargetTemp - 32)*5)/9)+273);
    $NestCurrentTempKelvin = (((($infos->current_state->temperature - 32)*5)/9)+273);
    
}

$logRow = $logRow . ", TargetTemp = " . $NestTargetTempKelvin . ", CurrentTemp = " . $NestCurrentTempKelvin;

try {
    
  $NestData = array(
      'timestamp'           => $date,
      'NestName'            => print_r( $locations[0]->name, true),
      'NestUpdated'         => print_r( $infos->network->last_connection, true),
      'NestCurrentKelvin'   => $NestCurrentTempKelvin,
      'NestTargetKelvin'    => $NestTargetTempKelvin,
      'NestTimeToTarget'    => print_r( $infos->target->time_to_target,true ),
      'NestHumidity'        => print_r( $infos->current_state->humidity, true),
      'NestHeating'         => print_r( $infos->current_state->heat==1?1:0, true),
      'NestPostal_code'     => print_r( $locations[0]->postal_code, true),
      'NestCountry'         => print_r( $locations[0]->country, true),
      'NestAutoAway'        => print_r( $infos->current_state->auto_away==1?1:0, true),
      'NestManualAway'      => print_r( $infos->current_state->manual_away==1?1:0, true),
      'WeatherMain'         => print_r( $weather->weather[0]->main, true),
      'WeatherDescription'  => print_r( $weather->weather[0]->description, true),
      'WeatherTempKelvin'   => print_r( $weather->main->temp, true),
      'WeatherHumidity'     => print_r( $weather->main->humidity, true),
      'WeatherTempMinKelvin'=> print_r( $weather->main->temp_min, true),
      'WeatherTempMaxKelvin'=> print_r( $weather->main->temp_max, true),
      'WeatherPressure'     => print_r( $weather->main->pressure, true),
      'WeatherWindspeed'    => print_r( $weather->wind->speed, true),
      'WeatherCityName'     => print_r( $weather->name, true),
      'WeatherCloudiness'   => print_r( $weather->clouds->all, true),
      'WeatherSunRise'      => print_r( $weather->sys->sunrise, true),
      'WeatherSunSet'       => print_r( $weather->sys->sunset, true)
  );
  
 
    $db = new DB($config);
    /* check connection */
    if (mysqli_connect_errno()) {
        $logdate = date("Y-m-d H:i:s");
        printf("\n$logdate Connect failed: %s", mysqli_connect_error());
        exit();
    }  else {
        $logdate = date("Y-m-d H:i:s");
        printf("\n$logdate Connected to the database.");
    }
    
    if ($stmt = $db->res->prepare("INSERT INTO rawdata( timestamp, NestName, NestUpdated, NestCurrentKelvin, "     
         . "NestTargetKelvin, NestTimeToTarget, NestHumidity, NestHeating, NestPostal_code, NestCountry, NestAutoAway, NestManualAway, WeatherMain, "  
         . "WeatherDescription, WeatherTempKelvin, WeatherHumidity, WeatherTempMinKelvin, WeatherTempMaxKelvin, "   
         . "WeatherPressure, WeatherWindspeed, WeatherCityName, WeatherCloudiness, WeatherSunRise, WeatherSunSet )"  // 24
         . "VALUES( ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"))
    
    
     {
        // printf("\nIf true");
        $stmt->bind_param('sssiiiiissiissiiiiiisiii', 
            $NestData['timestamp'],
            $NestData['NestName'],
            $NestData['NestUpdated' ],
            $NestData['NestCurrentKelvin'],
            $NestData['NestTargetKelvin'],
            $NestData['NestTimeToTarget'],
            $NestData['NestHumidity'],
            $NestData['NestHeating'],
            $NestData['NestPostal_code'],
            $NestData['NestCountry'],
            $NestData['NestAutoAway'],
            $NestData['NestManualAway'],
            $NestData['WeatherMain'],
            $NestData['WeatherDescription'],
            $NestData['WeatherTempKelvin'],
            $NestData['WeatherHumidity'],
            $NestData['WeatherTempMinKelvin'],
            $NestData['WeatherTempMaxKelvin'],
            $NestData['WeatherPressure'],
            $NestData['WeatherWindspeed'],
            $NestData['WeatherCityName'],
            $NestData['WeatherCloudiness'],
            $NestData['WeatherSunRise'],
            $NestData['WeatherSunSet']
        );
        
        $stmt->execute();
        $logdate = date("Y-m-d H:i:s");
        printf("\n$logdate %d Row inserted. ", $stmt->affected_rows);
        if(mysqli_stmt_errno($stmt) > 0)
        {
            $logdate = date("Y-m-d H:i:s");
            printf("\n$logdate Error Nr. ", mysqli_stmt_errno($stmt));
            printf("\n$logdate Error ",mysqli_stmt_error($stmt));
            $logRow = $logRow . "," . $stmt->affected_rows . "," . mysqli_stmt_error($stmt) . "\n";
        }
        else
        {
            $logRow = $logRow . "," . $stmt->affected_rows . ",No Errors\n";
        }
        $stmt->close();
     }
     else
     {

         print_r(error_get_last());
     };
     $logdate = date("Y-m-d H:i:s");
     printf("\n$logdate Log row = " . $logRow );
     
} catch (Exception $e) {
  $errors[] = ("DB connection error! <code>" . $e->getMessage() . "</code>.");
}

$logdate = date("Y-m-d H:i:s");
printf("\n$logdate ***** End pull of date ***** \n");


?>
