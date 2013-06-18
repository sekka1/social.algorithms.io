<?php
ini_set('error_reporting', E_ALL);

// Hybrid config and includes
$config = 'library/hybridauth/hybridauth/config.php';
require_once( "library/hybridauth/hybridauth/Hybrid/Auth.php" );

// Data Normalization
include('library/AlgorithmsIO/DataNormalization/LinkedIn.php');

// Script Vars
$all_users = array(); 
$linkedin = new \AlgorithmsIO\LinkedIn();

session_start(); 

	try{
		// hybridauth EP
		$hybridauth = new Hybrid_Auth( $config );

		// automatically try to login with the given provider
		$provider = $hybridauth->authenticate( "LinkedIn" );

		// return TRUE or False <= generally will be used to check if the user is connected to twitter before getting user profile, posting stuffs, etc..
		$is_user_logged_in = $provider->isUserConnected();

		// get the user profile 
		$user_profile = $provider->getUserProfile();

		// access user profile data
		echo "You are connected with: <b>{$provider->id}</b><br />";
		echo "As: <b>{$user_profile->displayName}</b><br />";
		echo "And your provider user identifier is: <b>{$user_profile->identifier}</b><br />";  
		echo "Access Token: " . print_r($provider->getAccessToken()) . "<br/>";

		// or even inspect it
		echo "<pre>" . print_r( $user_profile, true ) . "</pre><br />";

		// Retrieves all the user's connections
		$connections_list = $provider->api()->profile( '~/connections?format=json', 'get' );
//print_r($connections_list);

		$connections_list_object = json_decode($connections_list['linkedin']);

		$n=0;
		// Loop through the connection and get their info
		foreach($connections_list_object->values as $aConnection){
		
			$n++;
			//if($n>4)
			//	break;

			//$aUser = $provider->api()->profile( 'id='.$aConnection->id.':(id,firstName,lastName,headline,location,industry,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,api-standard-profile-request,public-profile-url,email-address,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,related-profile-views,phone-numbers,bound-account-types,im-accounts,main-address,twitter-accounts,primary-twitter-account,connections)?format=json', 'get' );
                        $aUser = $provider->api()->profile( 'id=NULyvBV0OP:(id,firstName,lastName,headline,location,industry,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,api-standard-profile-request,public-profile-url,email-address,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,related-profile-views,phone-numbers,bound-account-types,im-accounts,main-address,twitter-accounts,primary-twitter-account,connections)?format=json', 'get' );
         print_r($aUser);
			$all_users[] = $linkedin->getUsersValues(json_decode($aUser['linkedin']));
                        
                        break;
		}
		
		// Output all users info in a csv format
		echo "<b>Your friends information:</b><br>";
		//outputArrayCSV($all_users);
echo "Xxxxxxxxx";       
print_r($all_users);
		// logout
		echo "<br/><br/>Logging out.."; 
		$provider->logout(); 

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