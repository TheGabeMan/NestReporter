<?php

require 'inc/config.php';
require 'inc/class.db.php';
require 'nest-api-master/nest.class.php';

define('USERNAME', $config['nest_user']);
define('PASSWORD', $config['nest_pass']);

date_default_timezone_set($config['local_tz']);

$nest = new Nest();

/*
 * Get the data we  want to use
 */

// Nest data

$infos = $nest->getDeviceInfo();

printf("<h1>Device Info</h1>");
printf("<br>Last connection = %s\n", $infos->network->last_connection);
printf("<br>Mode = %s\n",$infos->current_state->mode);
printf("<br>Target temp = %s\n",$infos->target->temperature[0]);
printf("<br>Current temp = %s\n",$infos->current_state->temperature);
printf("<br>Current Humidity = %s\n",$infos->current_state->humidity);
printf("<br>Heating = %s\n",($infos->current_state->heat== 1 ? 1 : 0));

printf("<p>");
printf("<h1>Device Location</h1>");

$locations = $nest->getUserLocations();
printf("<br>The name is : %s\n", $locations[0]->name);
printf("<br>Postcal code = %s\n",$locations[0]->postal_code);
printf("<br>Country = %s\n",$locations[0]->country);
printf("<br>Away = %s\n",$locations[0]->away);


/*
 * How to find your City ID on OpenWeatherMap.org: http://openweathermap.org/find?q=
 */

$jsonurl = "http://api.openweathermap.org/data/2.5/weather?q=" . $locations[0]->postal_code . "," . $locations[0]->country;
printf("<br>",$jsonurl);
$json = file_get_contents($jsonurl);
$weather = json_decode($json);

printf("<h1>OpenWeatherMap Local weather</h1>");
printf("<br>City : %s\n",$weather->name);
printf("<br>Weer : %s\n", $weather->weather[0]->main);
printf("<br>Weer omschrijving: %s\n", $weather->weather[0]->description);

/*
 * - Temperatures read from the NEST device are in either Celcius or Fahrenheit, depending on user setting
 * - Temperatures read from OpenWeatherMaps are in Kelvin
 * - All temperatures stored in the database will be in Kelvin
 * 
 */



// Is this NEST set to Celcius of Fahrenheit?
$CelOrFahr = $nest->getDeviceTemperatureScale();
printf("<br>Celcius of Fahrenheit: %s\n", $CelOrFahr );

if ($CelOrFahr =="C")
{
    // Convert all Kelvin values to Celcius for display in test scenario
    printf("<br>Outside Temp: %s\n",$weather->main->temp - 273.15);
    printf("<br>Outside Temp Min: %s\n",$weather->main->temp_min - 273.15);
    printf("<br>Outside Temp Max: %s\n",$weather->main->temp_max - 273.15);
    
    // Convert NEST device temp values to Kelvin for storing in database
    $NestTargetTempKelvin = ($infos->target->temperature[0] + 273.15);
    $NestCurrentTempKelvin = ($infos->current_state->temperature + 273.15);

    
}else
{
    // Convert all Kelvin values to Fahrenheit for display in test scenario
    printf("<br>Outside Temp: %s\n",$weather->main->temp);
    printf("<br>Outside Temp Min: %s\n",$weather->main->temp_min );
    printf("<br>Outside Temp Max: %s\n",$weather->main->temp_max );

    // Convert NEST device temp values to Kelvin for storing in database
    $NestTargetTempKelvin = (((( $infos->target->temperature[0] - 32)*5)/9)+273.15);
    $NestCurrentTempKelvin = (((($infos->current_state->temperature - 32)*5)/9)+273.15);
    
}
printf("<br>BBBTarget temp = %s\n",$infos->target->temperature[0]);
printf("<br>BBBCurrent temp = %s\n",$infos->current_state->temperature);

printf("<br> Converted Target Temp to Kelvin: %s\n", $NestTargetTempKelvin);
printf("<br> Converted Current Temp to Kelvin: %s\n", $NestCurrentTempKelvin);

printf("<br>Outside Humidity: %s\n",$weather->main->humidity);
printf("<br>Outside Pressure: %s\n",$weather->main->pressure);
printf("<br>Wind Speed: %s\n",$weather->wind->speed);
printf("<br>Wind Directions: %s\n",$weather->wind->deg);

$date = date("Y-m-d H:i:s");
printf("<br> Tijdstip: %s\n", $date);

try {
    
  /*
  $sqlString = "INSERT INTO rawdata( timestamp, NestName, NestUpdated, NestCurrentKelvin, NestTargetKelvin, " 
          . "NestHumidity, NestHeating, NestPostal_code, NestCountry, NestAway, WeatherMain, "
          . "WeatherDescription, WeatherTempKelvin, WeatherHumidity, WeatherTempMinKelvin, WeatherTempMaxKelvin, "
          . "WeatherPressure, WeatherWindspeed, WeatherWinddeg, WeatherCityName) "
          . " VALUES( "
          . $date. ", "
          . print_r( $locations[0]->name, true) . ", "
          . print_r( $infos->network->last_connection, true) . ", "
          . $NestCurrentTempKelvin . ", "
          . $NestTargetTempKelvin . ", "
          . print_r( $infos->current_state->humidity, true) . ", "
          . print_r( $infos->current_state->mode, true) . ", "
          . print_r( $locations[0]->postal_code, true) . ", "
          . print_r( $locations[0]->country, true) . ", "
          . print_r( $locations[0]->away, true) . ", "
          . print_r( $weather->weather[0]->main, true) . ", "
          . print_r( $weather->weather[0]->description, true) . ", "
          . print_r( $weather->main->temp, true) . ", "
          . print_r( $weather->main->humidity, true) . ", "
          . print_r( $weather->main->temp_min, true) . ", "
          . print_r( $weather->main->temp_max, true) . ", "
          . print_r( $weather->main->pressure, true) . ", "
          . print_r( $weather->wind->speed, true) . ", "
          . print_r( $weather->wind->deg, true) . ", "
          . print_r( $weather->name, true) . ")" ;
  */
  
  
    
  $NestData = array(
      'timestamp'           => $date,
      'NestName'            => print_r( $locations[0]->name, true),
      'NestUpdated'         => print_r( $infos->network->last_connection, true),
      'NestCurrentKelvin'   => $NestCurrentTempKelvin,
      'NestTargetKelvin'    => $NestTargetTempKelvin,
      'NestHumidity'        => print_r( $infos->current_state->humidity, true),
      'NestHeating'         => print_r( $infos->current_state->mode, true),
      'NestPostal_code'     => print_r( $locations[0]->postal_code, true),
      'NestCountry'         => print_r( $locations[0]->country, true),
      'NestAway'            => print_r( $locations[0]->away, true),
      'WeatherMain'         => print_r( $weather->weather[0]->main, true),
      'WeatherDescription'  => print_r( $weather->weather[0]->description, true),
      'WeatherTempKelvin'   => print_r( $weather->main->temp, true),
      'WeatherHumidity'     => print_r( $weather->main->humidity, true),
      'WeatherTempMinKelvin'=> print_r( $weather->main->temp_min, true),
      'WeatherTempMinKelvin'=> print_r( $weather->main->temp_max, true),
      'WeatherPressure'     => print_r( $weather->main->pressure, true),
      'WeatherWindspeed'    => print_r( $weather->wind->speed, true),
      'WeatherWinddeg'      => print_r( $weather->wind->deg, true),
      'WeatherCityName'     => print_r( $weather->name, true)
  );
  
 
    print_r( $NestData);
 
    $db = new DB($config);
    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }  else {
        printf("<h3>Connected to the database !!!</h3>");
    }
        
    if ($stmt = $db->res->prepare("INSERT INTO rawdata( timestamp, NestName, NestUpdated, NestCurrentKelvin, " 
             . "NestTargetKelvin, NestHumidity, NestHeating, NestPostal_code, NestCountry, NestAway, WeatherMain, "
             . "WeatherDescription, WeatherTempKelvin, WeatherHumidity, WeatherTempMinKelvin, WeatherTempMaxKelvin, "
             . "WeatherPressure, WeatherWindspeed, WeatherWinddeg, WeatherCityName) "
             . "VALUES( ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"))
     {
        $stmt->bind_param('sssiiiississiiiiiiis', 
            $NestData['timestamp'],
            $NestData['NestName'],
            $NestData['NestUpdated' ],
            $NestData['NestCurrentKelvin'],
            $NestData['NestTargetKelvin'],
            $NestData['NestHumidity'],
            $NestData['NestHeating'],
            $NestData['NestPostal_code'],
            $NestData['NestCountry'],
            $NestData['NestAway'],
            $NestData['WeatherMain'],
            $NestData['WeatherDescription'],
            $NestData['WeatherTempKelvin'],
            $NestData['WeatherHumidity'],
            $NestData['WeatherTempMinKelvin'],
            $NestData['WeatherTempMinKelvin'],
            $NestData['WeatherPressure'],
            $NestData['WeatherWindspeed'],
            $NestData['WeatherWinddeg'],
            $NestData['WeatherCityName']
        );
        
        $stmt->execute();
        printf("%d Row inserted.\n", $stmt->affected_rows);
        printf("Error Nr.\n", mysqli_stmt_errno($stmt));
        printf("Error  \n",mysqli_stmt_error($stmt));
        $stmt->close();
     };
  
  
} catch (Exception $e) {
  $errors[] = ("DB connection error! <code>" . $e->getMessage() . "</code>.");
}

  
  
  //  $data = array("heating'      => ($info->current_state->heat == 1 ? 1 : 0),);

 /* 
  function c_to_f($c) {
    return ($c * 1.8) + 32;
    }
  * 
  */

?>
