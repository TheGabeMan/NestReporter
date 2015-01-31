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
print_r( $infos);

printf("<h1>Device Info</h1>");
printf("<br>Last connection = %s\n", $infos->network->last_connection);
printf("<br>Mode = %s\n",$infos->current_state->mode);
printf("<br>Target temp = %s\n",$infos->target->temperature);
printf("<br>Time to target temp = %s\n",$infos->target->time_to_target);
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

?>
