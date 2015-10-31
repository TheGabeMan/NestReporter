<?php

require 'inc/config.php';
require 'inc/class.db.php';
require 'nest-api-master/nest.class.php';
require 'inc/basic-functions.php';

define('USERNAME', $config['nest_user']);
define('PASSWORD', $config['nest_pass']);

date_default_timezone_set($config['local_tz']);

/*
 * Let's first check if we're online
 */

// CheckIfOnline($config['nest_user']);

$nest = new Nest();

printf("\nNest uitvoer");
/*
 * Get the data we  want to use
 */
$infos = $nest->getDeviceInfo();
print_r($infos);


// Current date and time
$date = date("Y-m-d H:i:s");
$logRow = $date . "," . $infos->network->last_connection . "," . $infos->current_state->mode;

/*
 * How to find your City ID on OpenWeatherMap.org: http://openweathermap.org/find?q=
 */

$locations = $nest->getUserLocations();
$jsonurl = "http://api.openweathermap.org/data/2.5/weather?q=" . $locations[0]->postal_code . "," . $locations[0]->country . "&APPID=c10eca5ef3de05f43173a50f922831d8";
$json = file_get_contents($jsonurl);
$weather = json_decode($json);
$logRow = $logRow . "," . $weather->name . "," . $weather->weather[0]->main . "," . $weather->weather[0]->description;

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
    $NestTargetTempKelvin = ($infos->target->temperature + 273);
    $NestCurrentTempKelvin = ($infos->current_state->temperature + 273);

    
}else
{
    // Convert NEST device temp values to Kelvin for storing in database
    // Although officially 1 Kelvin = -273.15 C, we're using - 273C otherwise all C degrees would be xx.85
    $NestTargetTempKelvin = (((( $infos->target->temperature - 32)*5)/9)+273);
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
      'WeatherCityName'     => print_r( $weather->name, true)
  );
  
    printf("\nNest Data = ");
    print_r( $NestData);
    printf("\nHeater on or off: ");
    printf($infos->current_state->heat==1?1:0);
 
    $db = new DB($config);
    /* check connection */
    if (mysqli_connect_errno()) {
        printf("\nConnect failed: %s\n", mysqli_connect_error());
        exit();
    }  else {
        printf("\nConnected to the database !!!\n");
    }
    
    if ($stmt = $db->res->prepare("INSERT INTO rawdata( timestamp, NestName, NestUpdated, NestCurrentKelvin, "     // 4
         . "NestTargetKelvin, NestTimeToTarget, NestHumidity, NestHeating, NestPostal_code, NestCountry, NestAutoAway, NestManualAway, WeatherMain, "  // 13
         . "WeatherDescription, WeatherTempKelvin, WeatherHumidity, WeatherTempMinKelvin, WeatherTempMaxKelvin, "   // 18
         . "WeatherPressure, WeatherWindspeed, WeatherCityName)"  // 21
         . "VALUES( ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"))
    
    
     {
        printf("\nIf true");
        $stmt->bind_param('sssiiiisssiissiiiiiis', 
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
            $NestData['WeatherCityName']
        );
        
        $stmt->execute();
        printf("\n%d Row inserted.\n", $stmt->affected_rows);
        if(mysqli_stmt_errno($stmt) > 0)
        {
            printf("Error Nr.\n", mysqli_stmt_errno($stmt));
            printf("Error  \n",mysqli_stmt_error($stmt));
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

         printf("\nIf false");
         print_r(error_get_last());
         printf("Error Nr.\n", mysqli_stmt_errno($stmt));
         printf("Error  \n",mysqli_stmt_error($stmt));
         print_r($stmt);
         printf($stmt);
         printf("\nEnd false");
     };
     printf("\nLog row = ");
     printf($logRow);
     printf("\nEnd Log\n");
     
} catch (Exception $e) {
  $errors[] = ("DB connection error! <code>" . $e->getMessage() . "</code>.");
}


?>
