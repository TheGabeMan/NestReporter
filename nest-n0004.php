<?php
        
    require_once("./inc/basic-functions.php");
    if (mysqli_connect_errno())
        { echo "Failed to connect to database: " . mysqli_connect_error(); }
    
    $today = date("Y-M-D");
    $ChartData = array();

    for ($x=0; $x<=7; $x++) 
    {
        $sqldate = date('Y-m-d', time() - 86400 * $x);
        Printf("<br>Counter = $x </br>");
        printf("<br>Datum = $sqldate </br>");
        
        // 288 samples =  each 5 min * 12 times per hour * 24 hours
        $sql = "SELECT round((COUNT(*)*5)/60,1) as Counter, date(timestamp) as WeekDay, NestHeating FROM `rawdata` WHERE NestHeating = 1 AND date(timestamp) = date('$sqldate') GROUP BY day(timestamp);";
        printf("<br>$sql</br>");
        if (!mysqli_query($con,$sql))
          { die('Error: ' . mysqli_error($con)); }
        $results = mysqli_query($con,$sql);

        printf("<br>Begin results</br>");
        foreach($results as $result) 
        { 
            $ChartData[] = array( $result['WeekDay'], floatval($result['Counter']));
            printf( ($result['WeekDay']));

        }
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
          data.addColumn('string', 'WeekDay');
          data.addColumn('number', 'Counter');
          
          // alert( <?php echo json_encode($ChartData); ?>);
          
          var json_arr = <?php echo json_encode($ChartData); ?>; 
          data.addRows(JSON.parse(json_arr));
          

          // Set chart options
          var options = {'title':'Past 24hrs',
                         'width':1400,
                         'height':800,
                         'hAxis':{'title':'Date & Time'},
                         'hAxis':{'format':'MMM d HH:mm'}
                     };
                     
          
          // Instantiate and draw our chart, passing in some options.
          var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
          chart.draw(data, options);
        }
    </script>

    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
  