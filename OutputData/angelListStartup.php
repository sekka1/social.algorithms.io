<?php
/**
 * Collecting Angel List Data
 * 
 * It is going to get call the api with the batch mode endpoint returning 50 people.
 * 
 * It will place all this data in a file
 * 
 * Foreach person it is going to call the "roles" endpoint to get roles this person
 * has been in.
 * 
 * Usage:
 * HTTP: http://social.signiavc.com/OutputData/angelListStartup.php
 * 
 * 
 */

ini_set('error_reporting', E_ALL);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 60000);

$outputFileLocation = '/opt/logs/angelsList/';

// Hybrid config and includes
$config = '../library/hybridauth/hybridauth/config.php';
require_once( "../library/hybridauth/hybridauth/Hybrid/Auth.php" );
require_once("../library/AlgorithmsIO/Utilities/OutputData.php");

// Data Normalization
include('../library/AlgorithmsIO/DataNormalization/AngelListStartup.php');


// Redirect for Oauth
//header('Location: https://angel.co/api/oauth/authorize?client_id=5f3f3c589e8ad4dac60ce4bb0ccd93cf&response_type=code');

// Normalization
$normalize = new \AlgorithmsIO\DataNormalization\AngelListStartup();


// Saving data gathered from AngelList
$out_angelListCompany = new \AlgorithmsIO\Utilities\OutputData($outputFileLocation.'startups.txt');




$start_number = 242278;
$stop_number = 999999;
$batchFetchAmount = 50;
//$log_file = '/opt/logs/crunchbaseCompany/adding_angelList_people_start_'.$start_number.'k.txt';


// hybridauth EP
$hybridauth = new Hybrid_Auth( $config );

// automatically try to login with the given provider
$provider = $hybridauth->getAdapter( "AngelList" );

// Loop through and collect Angellist data
for($i=$start_number;$i<$stop_number;$i++){
    
    //$i=734;
    //$i=1072;
    
    // Get data from provider.  Batch of users
    $items = $provider->get('startups/batch?ids='.  getBatchNumbers($batchFetchAmount, $i));
    //$i += $batchFetchAmount;

    // Process Items - a startup
    $itemsArray = json_decode($items['angellist']);

    foreach($itemsArray as $anItem){
        
        $normalizedItem = $normalize->getValues($anItem);

        // Save data
        $out_angelListCompany->out(json_encode($normalizedItem)."\n");
    
        //if($i==4)
        //    exit;
        
        $i++;
    }
    

    
}

/**
 * Returns a string list of numbers for the batch call to Angel List
 * 
 * @param int $numberOfResults
 * @param int $startNumber
 * @return string
 */
function getBatchNumbers($numberOfResults, $startNumber){
    $string = '';
    for($n=0;$n<$numberOfResults;$n++){
        $string .= $startNumber.',';
        $startNumber++;
    }
    echo $string;
    return $string;
}

?>
