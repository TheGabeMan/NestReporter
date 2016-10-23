<?php
    require_once("./inc/basic-functions.php");
    
    // Later on we'll be using the $config var, which is global. Therefore we need to repeat here that it is used as global
    // global $config;


    if (mysqli_connect_errno())
        { echo "Failed to connect to database: " . mysqli_connect_error(); }

        $sql = "SELECT * FROM (SELECT * FROM  `rawdata` ORDER BY  `rawdata`.`timestamp` DESC LIMIT 290) AS ttbl ORDER BY `timestamp` ASC;";
        
    if (!mysqli_query($con,$sql))
      { die('Error: ' . mysqli_error($con)); }
    $result = mysqli_query($con,$sql);

?>

<div class="container">
    <div class="page-header">
      <h1>Last 24h of target and real temps</h1>
    </div>
    
    <div class="row">
        <table class="table table-striped">
            <thead>
              <tr>
                <th>Time Stamp</th>
                <th>Inside Temp</th>
                <th>Target Temp</th>
                <th>Action</th>
                <th>Heater On</th>
              </tr>
            </thead>
        <?php
            while ($row = mysqli_fetch_array($result)) 
            { 
        ?>
                <tr>
                    <td><?php echo $row['timestamp']  ?></td>
                    <td><?php echo k_to_h($row['NestCurrentKelvin']); ?></td>
                    <td><?php echo k_to_h($row['NestTargetKelvin']);  ?></td>
                <?php
                    if( k_to_h($row['NestCurrentKelvin']) > k_to_h($row['NestTargetKelvin']))
                        {echo "<td>Cooling down</td>";}
                    elseif( k_to_h($row['NestCurrentKelvin']) == k_to_h($row['NestTargetKelvin']))
                        {echo "<td>Temperature reached</td>";}
                    elseif( k_to_h($row['NestCurrentKelvin']) < k_to_h($row['NestTargetKelvin']))
                        {echo "<td>Temperature rising</td>";}

                        
                    if( $row['NestHeating'] == '1')
                            {echo "<td>Yes</td>";}
                        else
                            {echo "<td>No</td>";}
                ?> 
                </tr>
            
        <?php
            }
            mysqli_close($con);
        ?>

        </table>

    </div>
</div>




