<?php
    require_once("./phpChart/conf.php");
    require_once("./inc/basic-functions.php");
    
    if (mysqli_connect_errno())
        { echo "Failed to connect to database: " . mysqli_connect_error(); }

        $sql = "SELECT * FROM (SELECT * FROM  `rawdata` ORDER BY  `rawdata`.`timestamp` DESC LIMIT 290) AS ttbl ORDER BY `timestamp` ASC;";
        
    if (!mysqli_query($con,$sql))
      { die('Error: ' . mysqli_error($con)); }
    $result = mysqli_query($con,$sql);
?>

<table>
    <tr><td>Time Stamp</td><td>Temp</td><td>Target</td></tr>

    <?php
    while ($row = mysqli_fetch_array($result)) 
    { 
        ?>
        <tr>
            <td><?php echo $row['timestamp']; $time[] = $row['timestamp'];  ?></td>
            <td><?php echo $row['NestCurrentKelvin']; $Current[] = intval($row['NestCurrentKelvin'])-273.15; ?></td>
            <td><?php echo $row['NestTargetKelvin']; $Target[] = $row['NestTargetKelvin']; ?></td>
        </tr>
    
        <?php
    }
    mysqli_close($con);
    
    ?>
    
</table>


<?php 
    print_r($Current);
    $pc = new C_PhpChartX(array($Current),'basic_chart');
    // $pc = new C_PhpChartX(array(array(11, 9, 5, 12, 14)),'basic_chart');
    $pc->draw();


/* 
 ___chart1= $.jqplot("__chart1", [["293.000","293.000","293.000","293.000","293.000","293.000","293.000","294.000","294.000","294.000","294.000","294.000","294.000","294.000","294.000","294.000","294.000","294.000","294.000","294.000","295.000",
 "295.000","295.000","295.000","295.000","295.000","295.000","295.000","295.000","295.000"]], ___chart1_plot_properties);

*/
?>
