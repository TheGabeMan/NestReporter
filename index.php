<?php
    error_reporting(E_ALL); 
    ini_set("display_errors", 1);
    session_start();
    
    include_once 'inc/tstat-config.php';
    // include_once 'inc/load-cookie.php';
    
    
?>
<!DOCTYPE html> 
<script type="text/javascript" src="inc/dhtmlxcalendar.js"></script>
<meta charset="utf-8">
<html>
 <?php
    // Let's find out what values were send through GET
    $PageNr = "";
    $PageQuery = "";
    
    if( !empty($_GET['page']))
        { $PageNr = $_GET['page']; }
        
    switch( $PageNr){
        case "n0001":
            // Page n0001 = index page;
            $top_include = 'tstat-top.php';
            $banner_include = 'nest-banner.php';
            $body_include = 'nest-n0001.php';
            $footer_include = 'nest-footer.php';
            break;

        case "n0002":
            // Page n0002 = D3 test
            $top_include = 'tstat-top.php';
            $banner_include = 'nest-banner.php';
            $body_include = 'nest-n0002.php';
            $footer_include = 'nest-footer.php';
            break;
        
        case "n0003":
            // Page n0003 = Big Graph
            $top_include = 'tstat-top.php';
            $banner_include = 'nest-banner.php';
            $body_include = 'nest-n0003.php';
            $footer_include = 'nest-footer.php';
            break;

        case "n0004":
            // Page n0004 = Big Graph
            $top_include = 'tstat-top.php';
            $banner_include = 'nest-banner.php';
            $body_include = 'nest-n0004.php';
            $footer_include = 'nest-footer.php';
            break;

        case "n0005":
            // Page n0005 = Graph between dates
            $top_include = 'tstat-top.php';
            $banner_include = 'nest-banner.php';
            $body_include = 'nest-n0005.php';
            $footer_include = 'nest-footer.php';
            break;
        

        default:
            // If no matching case found
            $top_include = 'tstat-top.php';
            $banner_include = 'nest-banner.php';
            $body_include = 'nest-index.php';
            $footer_include = 'nest-footer.php';
            break;
    }
  
 ?>

    <head>
        <?php 
        $page_include = 'tstat-header.php';
        include ($page_include);
        ?>
    </head>

    
    <body>
        <!-- Top Section -->
        <div id ="top">
            <?php
            include( $top_include );
            ?>
        </div>
        
        <!-- Banner Section -->
            <?php
            include( $banner_include );
            ?>
        <!-- Body Section -->
            <?php

            include( $body_include );
            ?>

        <!-- Footer Section -->
        <div id ="footer">
            <?php
            include( $footer_include );
            ?>
        </div>    

    </body>
</html>