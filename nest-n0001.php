<?php
    require_once("./inc/basic-functions.php");
    
    if (mysqli_connect_errno())
        { echo "Failed to connect to database: " . mysqli_connect_error(); }

        $sql = "SELECT * FROM (SELECT * FROM  `rawdata` ORDER BY  `rawdata`.`timestamp` DESC LIMIT 290) AS ttbl ORDER BY `timestamp` ASC;";
        
    if (!mysqli_query($con,$sql))
      { die('Error: ' . mysqli_error($con)); }
    $result = mysqli_query($con,$sql);
    
    $ticker = 0;
?>

<table>
    <tr><td>Time Stamp</td><td>Temp</td><td>Target</td></tr>

    <?php
    while ($row = mysqli_fetch_array($result)) 
    { 
      /*  ?>
        <tr>
            <td><?php echo $row['timestamp']  ?></td>
            <td><?php echo $row['NestCurrentKelvin'] ?></td>
            <td><?php echo $row['NestTargetKelvin'];  ?></td>
        </tr>
    
        <?php
       * 
       */
        $ticker++;
        $TimeStamp[] = $ticker;
        $Current[] = intval($row['NestCurrentKelvin']-273.15 );
        $Target[] = intval( $row['NestTargetKelvin'] -273.15 );
        
        /*
         * If Outside temperature was 0 Kelvin, then we haven't read the data correctly when fetching.
         * Therefore I now check for a zero value and replace it with 
         */
        if( $row['WeatherTempKelvin'] <> 0)
        {
            $OutSide[] = intval( $row['WeatherTempKelvin'] -273.15 );
        }else{ $OutSide[] = 0; }
    }  
    mysqli_close($con);
    
    ?>
    
</table>


<?php 


?>
