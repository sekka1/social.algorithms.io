<?php
ini_set('error_reporting', E_ALL);

// Hybrid config and includes
$config = 'library/hybridauth/hybridauth/config.php';
require_once( "library/hybridauth/hybridauth/Hybrid/Auth.php" );

// Data Normalization
include('library/DataNormalization/CrunchBase.php');

// Script Vars
$all_users = array(); 
$crunchbase = new \AlgorithmsIO\CrunchBase();

session_start(); 

	try{
		// hybridauth EP
		$hybridauth = new Hybrid_Auth( $config );

		// automatically try to login with the given provider
		$provider = $hybridauth->getAdapter( "Crunchbase" );
		
		$person = $provider->get('person/david-rohrsheim.js');
		
		print_r($person);
		
		$all_users[] = $crunchbase->getUsersValues(json_decode($person['crunchbase']));
		
		print_r($all_users);
echo "done";		

	}
	catch( Exception $e ){  
		// In case we have errors 6 or 7, then we have to use Hybrid_Provider_Adapter::logout() to 
		// let hybridauth forget all about the user so we can try to authenticate again.

		// Display the recived error, 
		// to know more please refer to Exceptions handling section on the userguide
		switch( $e->getCode() ){ 
			case 0 : echo "Unspecified error."; break;
			case 1 : echo "Hybridauth configuration error."; break;
			case 2 : echo "Provider not properly configured."; break;
			case 3 : echo "Unknown or disabled provider."; break;
			case 4 : echo "Missing provider application credentials."; break;
			case 5 : echo "Authentication failed. " 
					  . "The user has canceled the authentication or the provider refused the connection."; 
				   break;
			case 6 : echo "User profile request failed. Most likely the user is not connected "
					  . "to the provider and he should to authenticate again."; 
				   //$twitter->logout();
				   break;
			case 7 : echo "User not connected to the provider."; 
				   //$twitter->logout();
				   break;
			case 8 : echo "Provider does not support this feature."; break;
		} 

		// well, basically your should not display this to the end user, just give him a hint and move on..
		echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();

		echo "<hr /><h3>Trace</h3> <pre>" . $e->getTraceAsString() . "</pre>"; 

		/*
			// If you want to get the previous exception - PHP 5.3.0+ 
			// http://www.php.net/manual/en/language.exceptions.extending.php
			if ( $e->getPrevious() ) {
				echo "<h4>Previous exception</h4> " . $e->getPrevious()->getMessage() . "<pre>" . $e->getPrevious()->getTraceAsString() . "</pre>";
			}
		*/
	}
