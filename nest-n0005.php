<?php
        
    require_once("./inc/basic-functions.php");
    if (mysqli_connect_errno())
        { echo "Failed to connect to database: " . mysqli_connect_error(); }
        
        $StartDate = "01/13/2016";
        $EndDate = "01/15/2016";

        $sql = "SELECT * FROM (SELECT timestamp, NestCurrentKelvin, NestTargetKelvin, WeatherTempKelvin, NestHeating FROM  `rawdata` where timestamp >= STR_TO_DATE('$StartDate', '%m/%d/%Y') AND timestamp < STR_TO_DATE('$EndDate', '%m/%d/%Y') ) AS ttbl ORDER BY `timestamp` ASC;";

        printf($StartDate);
        printf($EndDate);
        
    if (!mysqli_query($con,$sql))
      { die('Error: ' . mysqli_error($con)); }
    $results = mysqli_query($con,$sql);

    $ChartData = array();
    foreach($results as $result) 
    { 
        // Although officially 1 Kelvin = -273.15 C, we're using - 273C otherwise all C degrees would be xx.85
        // $ChartData[] = array( $result['timestamp'], (int)$result['NestCurrentKelvin'] - 273,(int)$result['NestTargetKelvin'] -273, (int)$result['NestHeating'] );
        $ChartData[] = array( $result['timestamp'], (int)$result['NestCurrentKelvin'] - 273,(int)$result['NestTargetKelvin'] - 273,(int)$result['WeatherTempKelvin'] - 273,(int)$result['NestHeating'] );
    }
    $ChartData = json_encode($ChartData);
    
    // echo( $ChartData);
      
    mysqli_close($con);

?>

<input type="text" id="calendar">
<input type="text" id="calendar2">


<script>
    var myCalendar = new dhtmlXCalendarObject(["calendar","calendar2"]);

</script>
 
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
          data.addColumn('number', 'Room Temp');
          data.addColumn('number', 'Target Temp');
          data.addColumn('number', 'Outside Temp');
          data.addColumn('number', 'Heat ON');
          
          // alert( <?php echo json_encode($ChartData); ?>);
          
          var json_arr = <?php echo json_encode($ChartData); ?>; 
          data.addRows(JSON.parse(json_arr));
          

          // Set chart options
          var options = {'title':'Past 24hrs',
                         'width':2000,
                         'height':1024,
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
  