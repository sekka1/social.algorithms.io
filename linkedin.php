<?php
/**
 * The main purpose of this script is to show the LinkedIn login to the user so
 * they can login.  Once they are logged in, we can then grab their LinkedIn
 * session data.
 * 
 * This can be passed to a backend process where it can go and harvest their data.
 */

ini_set('error_reporting', E_ALL);

define("ALGORITHMS_IO_AUTH_TOKEN", "bf77eb13fde4b453865f7c66342a67f3");
define("ALGORITHMS_IO_URL", "http://api.algorithms.io");
define("ALGORITHMS_IO_ENDPOINT", "/v2/jobs/202");


// Hybrid config and includes
$config = 'library/hybridauth/hybridauth/config.php';
require_once( "library/hybridauth/hybridauth/Hybrid/Auth.php" );

require_once("library/AlgorithmsIO/Utilities/Utilities.php");

	try{
		// hybridauth EP
		$hybridauth = new Hybrid_Auth( $config );

		// automatically try to login with the given provider
		$provider = $hybridauth->authenticate( "LinkedIn" );
     
		// return TRUE or False <= generally will be used to check if the user is connected to twitter before getting user profile, posting stuffs, etc..
		$is_user_logged_in = $provider->isUserConnected();
                
                $hybridauth_session_data = $hybridauth->getSessionData();
                
                // Submit to Algorithms.io to crawl this user and his network
                //echo $hybridauth_session_data;
                
                $utilities = new Utilities();
                $url = ALGORITHMS_IO_URL.ALGORITHMS_IO_ENDPOINT;
                $post_params['authToken'] = ALGORITHMS_IO_AUTH_TOKEN;
                $post_params['session_key'] = $hybridauth_session_data;
                
                $response = $utilities->curlPost($url, $post_params);

            //echo "<br/><br/>".$response;
                header('Location: http://social.signiavc.com/social/index.php?signedIn=true');
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
				   $twitter->logout();
				   break;
			case 7 : echo "User not connected to the provider."; 
				   $twitter->logout();
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

function outputArrayCSV($output){
	$outputHeader = true;

	foreach($output as $anOutput){
		if($outputHeader){
			// Output Header
			$headers = $anOutput;
			foreach($headers as $key=>$val){
				echo $key . ',';
			}
			echo '<br/>';
			$outputHeader = false;
		}
		if(!$outputHeader){
			// Ouput data
			foreach($anOutput as $val){
				echo preg_replace('/\,/','',$val) . ',';
			}
			echo '<br/>';
		}
	}
}