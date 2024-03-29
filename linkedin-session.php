<?php
/**
 * This script takes in a Hybridauth session data from LinkedIn and creates a
 * session out of it.
 * 
 * The purpose of this file is to run this script on a back end process by
 * passing it the session data so that you are not holding the user's session
 * the entire time while harvesting their data.
 */

ini_set('error_reporting', E_ALL);

// Hybrid config and includes
$config = 'library/hybridauth/hybridauth/config.php';
require_once( "library/hybridauth/hybridauth/Hybrid/Auth.php" );

// Data Normalization
include('library/AlgorithmsIO/DataNormalization/LinkedIn.php');
// Graph DB
include('library/AlgorithmsIO/GraphModels/Linkedin.php');

// Script Vars
$all_users = array(); 
$linkedin = new \AlgorithmsIO\DataNormalization\LinkedIn();

$hybridauth_session_data = 'a:4:{s:50:"hauth_session.linkedin.token.access_token_linkedin";s:229:"a:4:{s:11:"oauth_token";s:36:"d68a8ce2-a800-489d-bc56-5e8a9a983b97";s:18:"oauth_token_secret";s:36:"3bf01d9f-44ea-4087-b2c0-cc56ad75625e";s:16:"oauth_expires_in";s:7:"5183999";s:30:"oauth_authorization_expires_in";s:7:"5183999";}";s:41:"hauth_session.linkedin.token.access_token";s:44:"s:36:"d68a8ce2-a800-489d-bc56-5e8a9a983b97";";s:48:"hauth_session.linkedin.token.access_token_secret";s:44:"s:36:"3bf01d9f-44ea-4087-b2c0-cc56ad75625e";";s:35:"hauth_session.linkedin.is_logged_in";s:4:"i:1;";}';

session_start(); 

	try{
		// hybridauth EP
		$hybridauth = new Hybrid_Auth( $config );

		// Restore session data
                $hybridauth->restoreSessionData( $hybridauth_session_data );
                $provider = $hybridauth->getAdapter("LinkedIn");
                
                // 
                // Can continue calling the provider as regular from this point on
                // 

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

//print_r($hybridauth->getSessionData());            
//exit;                
		// Retrieves all the user's connections
		//$connections_list = $provider->api()->profile( '~/connections?format=json', 'get' );
//print_r($connections_list);

		//$connections_list_object = json_decode($connections_list['linkedin']);
                
                //
                // Insert the user that is giving us access into the db
                //
                //$response = $provider->api()->profile( 'id='.$user_profile->identifier.':(id,firstName,lastName,headline,location,industry,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,api-standard-profile-request,public-profile-url,email-address,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,related-profile-views,phone-numbers,bound-account-types,im-accounts,main-address,twitter-accounts,primary-twitter-account,connections)?format=json', 'get' );
                $response = $provider->api()->profile( 'id=mwaHFaow9x:(id,firstName,lastName,headline,location,industry,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,api-standard-profile-request,public-profile-url,email-address,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,related-profile-views,phone-numbers,bound-account-types,im-accounts,main-address,twitter-accounts,primary-twitter-account,connections)?format=json', 'get' );

                $aUserInfo = $linkedin->getUsersValues(json_decode($response['linkedin']));
                $harvestSourceFriendGUID = $user_profile->identifier;
print_r(json_decode($response['linkedin']));
//print_r($aUserInfo);
exit;
                // Save This into the graph database.
                $graphModel = new \AlgorithmsIO\GraphModels\Linkedin();
                $graphModel->setValues($aUserInfo);
                $graphModel->process();

                
                
                //
                // Inserting Harvest Source's Friends
                //

		$n=0;
		// Loop through the connection and get their info
		foreach($connections_list_object->values as $aConnection){
echo "\n\n\n\n Processing: ".$aConnection->id."\n\n\n\n";		

			$n++;
			if($n>3)
                            break;

			$response = $provider->api()->profile( 'id='.$aConnection->id.':(id,firstName,lastName,headline,location,industry,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,api-standard-profile-request,public-profile-url,email-address,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,related-profile-views,phone-numbers,bound-account-types,im-accounts,main-address,twitter-accounts,primary-twitter-account,connections)?format=json', 'get' );
                        //$aUser = $provider->api()->profile( 'id=mwaHFaow9x:(id,firstName,lastName,headline,location,industry,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,api-standard-profile-request,public-profile-url,email-address,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,related-profile-views,phone-numbers,bound-account-types,im-accounts,main-address,twitter-accounts,primary-twitter-account,connections)?format=json', 'get' );
                        $aUserInfo = $linkedin->getUsersValues(json_decode($response['linkedin']));

         print_r(json_decode($aUser['linkedin']));
         
 echo "zzzzzzzzzzz";                         
                        // Save This into the graph database.
                        $graphModel = new \AlgorithmsIO\GraphModels\Linkedin();
                        $graphModel->setValues($aUserInfo);
                        $graphModel->setHarvestSourceGUID($harvestSourceFriendGUID);
                        $graphModel->isHarvestSourceFriend();
                        $graphModel->process();
                        
  echo "\n\n\n\n\n\n\nxxxxxxxxxxxxxxxxxxxx\n\n\n\n\n\n";                      
                        
		}
		
		// Output all users info in a csv format
		echo "<b>Your friends information:</b><br>";
		//outputArrayCSV($all_users);
echo "Xxxxxxxxx";       
//print_r($all_users);
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