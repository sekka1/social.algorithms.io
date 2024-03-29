<?php
/**
 * @author Garland Kan <garland@algorithms.io>
 * 
 * Retrieves the entire list of X (person, company) from Crunchbase
 * 
 * API: http://developer.crunchbase.com/io-docs
 */

ini_set('error_reporting', E_ALL);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 6000);

// Hybrid config and includes
$config = 'library/hybridauth/hybridauth/config.php';
require_once( "library/hybridauth/hybridauth/Hybrid/Auth.php" );


session_start(); 
        
        
	try{
            
		// hybridauth EP
		$hybridauth = new Hybrid_Auth( $config );

		// automatically try to login with the given provider
		$provider = $hybridauth->getAdapter( "Crunchbase" );
		
		// Get data from crunchbase
                // Valid values: companies.js, people.js, financial-organizations, products, service-providers
                $person = $provider->get('companies.js');
		
                //print_r($person);
                
                
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
