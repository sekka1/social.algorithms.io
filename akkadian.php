<?php
/**
 * Importing Akkadian data from the MySQL DB into the Graph DB
 * 
 */
ini_set('error_reporting', E_ALL);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 6000);

// MySQL DB
include('library/AlgorithmsIO/Utilities/MySQL.php');
// Normalization
include('library/AlgorithmsIO/DataNormalization/AkkadianCompany.php');
// Graph DB
include('library/AlgorithmsIO/GraphModels/AkkadianCompany.php');
include('library/AlgorithmsIO/GraphModels/AkkadianRatings.php');
include('library/AlgorithmsIO/GraphModels/AkkadianRevenue.php');

// Init 
$mySQL = new \AlgorithmsIO\Utilities\MySQL();
$normalization = new \AlgorithmsIO\DataNormalization\AkkadianCompany();

// Company Model
$graphModel = new \AlgorithmsIO\GraphModels\AkkadianCompany();
// Ratings Model
//$graphModel = new \AlgorithmsIO\GraphModels\AkkadianRatings();
// Revenue Model
//$graphModel = new \AlgorithmsIO\GraphModels\AkkadianRevenue();

// Import Company data

    // Get data from MySQL
    $mysql = new \AlgorithmsIO\Utilities\MySQL();
    $mysql->setConnection('localhost', null, 'akkadian', 'akkadian1298');
    $mySQL->setDatabaseName('akkadian');
    $mySQL->connect();
    $mySQLConnection = $mySQL->getConnection();
    
    // Getting Company Information
    $start = 1117;
    $end = 99999999;
    $sql  = "SELECT idCompany, cbPermalink, name, website, founded, computedTotalRaised, city, state, countryCode, twitterUserName, cbCategory, cbTags, alid, alQuality, alType, alMarkets, status, companyType  FROM company_identity ORDER BY idCompany ASC  limit ".$start.", ".$end;
 
    // Getting Rating Information
    //$sql  = "SELECT * FROM ratings";
 
    // Getting Revenue Information
    //$sql  = "SELECT * FROM revenue";
    
    $n=$start;
    if($result = $mySQLConnection->query($sql)){
        while ($row = $result->fetch_assoc()) {
            //echo $row['idCompany'] . ' - '. $row['cbPermalink'];
      print_r($row);
            //$temp_json = json_encode($row);
            //$data = json_decode($temp_json);

          
            //$data = $normalization->getValues($row);
            //print_r($data);
            
            // Insert into graph
            $graphModel->setValues($row);
            $graphModel->process();

            //if($n>10)
            //    break;
            system("echo '".$n.") ".$row['idCompany'] . ' - '. $row['cbPermalink']."' >> /opt/adding.txt");
            
            $n++;
        }
        
    }
    
    

            
            
    // Import into GraphDB
        //pass array into Akkadian graph db class


?>
