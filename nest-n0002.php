<?php
    require_once("./inc/basic-functions.php");
    if (mysqli_connect_errno())
        { echo "Failed to connect to database: " . mysqli_connect_error(); }
        $sql = "SELECT * FROM (SELECT timestamp, NestCurrentKelvin, NestTargetKelvin, WeatherTempKelvin FROM  `rawdata` ORDER BY  `rawdata`.`timestamp` DESC LIMIT 290) AS ttbl ORDER BY `timestamp` ASC;";
    if (!mysqli_query($con,$sql))
      { die('Error: ' . mysqli_error($con)); }
    $results = mysqli_query($con,$sql);

    $ChartData = array();
    foreach($results as $result) 
    { 
        $ChartData[] = array( $result['timestamp'], (int)$result['NestCurrentKelvin'],(int)$result['NestTargetKelvin'],(int)$result['WeatherTempKelvin']);
    }
    $ChartData = json_encode($ChartData);
    
    // echo( $ChartData);
      
    mysqli_close($con);
    
    ?>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        // Load the Visualization API and the corechart package 
        // which containts most basic charts like pie, bar and column.
        google.load('visualization', '1.0', {'packages':['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        // In this 'drawChart' is the handler
        google.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the chart, passes in the data and
        // draws it.
        function drawChart() {

          // Create the data table.
          var data = new google.visualization.DataTable();
          
          // Load Arrays from PHP
          data.addColumn('string', 'timestamp');
          data.addColumn('number', 'NestCurrentKelvin');
          data.addColumn('number', 'NestTargetKelvin');
          data.addColumn('number','WeatherTempKelvin');
          data.addRows( {$ChartData} );
            
          // Set chart options
          var options = {'title':'How Much Pizza I Ate Last Night',
                         'width':400,
                         'height':300};

          // Instantiate and draw our chart, passing in some options.
          var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
          chart.draw(data, options);
        }
    </script>

    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
    