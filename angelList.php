<?php
/**
 * Collecting Angel List Data
 * 
 */

ini_set('error_reporting', E_ALL);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 60000);

// Hybrid config and includes
$config = 'library/hybridauth/hybridauth/config.php';
require_once( "library/hybridauth/hybridauth/Hybrid/Auth.php" );

// Data Normalization
include('library/AlgorithmsIO/DataNormalization/AngelListUser.php');
// Graph DB
//include('library/AlgorithmsIO/GraphModels/CrunchBaseCompany.php');

// Redirect for Oauth
//header('Location: https://angel.co/api/oauth/authorize?client_id=5f3f3c589e8ad4dac60ce4bb0ccd93cf&response_type=code');

$normalize = new \AlgorithmsIO\DataNormalization\AngelListUser();

$start_number = 1;
$stop_number = 999999;
$batchFetchAmount = 50;
$log_file = '/opt/logs/crunchbaseCompany/adding_angelList_people_start_'.$start_number.'k.txt';


// hybridauth EP
$hybridauth = new Hybrid_Auth( $config );

// automatically try to login with the given provider
$provider = $hybridauth->getAdapter( "AngelList" );

// Loop through and collect Angellist data
for($i=$start_number;$i<$stop_number;$i++){
    
    // Get data from provider.  Batch of users
    $items = $provider->get('users/batch?ids='.  getBatchNumbers($batchFetchAmount, $i));
    $i += $batchFetchAmount;
    
    // Process Items - a user
    $itemsArray = json_decode($items['angellist']);
    foreach($itemsArray as $anItem){
        $normalizedItem = $normalize->getUsersValues($anItem);
        
        print_r($normalizedItem);
        exit;
    }
    
    
    
    
    echo "count: ".count(json_decode($items['angellist']))."<br/>";
    print_r($items);
    exit;
    
    // Save Raw return from provider
    
    
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
    return $string;
}

?>
