<?php
    error_reporting(E_ALL); 
    ini_set("display_errors", 1);
    session_start();
    
    include_once 'inc/open-database.php';
    // include_once 'inc/load-cookie.php';
    
?>
<!DOCTYPE html> 
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
            $top_include = 'nest-top.php';
            $banner_include = 'nest-banner.php';
            $body_include = 'nest-n0001.php';
            $footer_include = 'nest-footer.php';
            break;

        case "n0201":
            // Page n0201 = Search nail polish page;
            $top_include = 'nest-top.php';
            $banner_include = 'nest-banner.php';
            $body_include = 'nest-n0201.php';
            $footer_include = 'nest-footer.php';
            break;

        default:
            // If no matching case found
            $top_include = 'nest-top.php';
            $banner_include = 'nest-banner.php';
            $body_include = 'nest-index.php';
            $footer_include = 'nest-footer.php';
            break;
    }
  
 ?>

    <head>
        <?php 
        $page_include = 'nest-header.php';
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