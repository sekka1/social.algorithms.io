<?php
/**
 * This script takes in a Hybridauth session data from LinkedIn and creates a
 * session out of it.
 * 
 * The purpose of this file is to run this script on a back end process by
 * passing it the session data so that you are not holding the user's session
 * the entire time while harvesting their data.
 */
//ini_set('error_reporting', E_ALL);

// Set long time limit to run over days
set_time_limit(604800);

// Hybrid config and includes
require_once("library/hybridauth/hybridauth/Hybrid/Auth.php");
require_once("library/AlgorithmsIO/Utilities/OutputData.php");

// Data Normalization
include('library/AlgorithmsIO/DataNormalization/LinkedIn.php');

// Input Configs
define("OUTPUT_FILE_LOCATION", "/opt/crawl_output/linkedin/");
//define("OUTPUT_FILE_LOCATION", "/Users/gkan/Downloads/");

class LinkedInCrawlUser{
    
    /**
     * This is the function that the worker wrapper will call and pass in the 
     * parameters specified with this "algorithm"
     * 
     * @param array $paramsArray
     */
    public function crawl($paramsArray){
        
        // Hybrid config and includes
        $config = 'library/hybridauth/hybridauth/config.php';
        
        // Output FIle location
        $outputFileLocation = OUTPUT_FILE_LOCATION;//'/opt/crawl_output/linkedin/';
        //$outputFileLocation = '/Users/gkan/Downloads/';
        
        // This is the LinkedIn Oauth session string that gives this access to make
        // calls against it.
        //$hybridauth_session_data = 'a:4:{s:50:"hauth_session.linkedin.token.access_token_linkedin";s:229:"a:4:{s:11:"oauth_token";s:36:"d68a8ce2-a800-489d-bc56-5e8a9a983b97";s:18:"oauth_token_secret";s:36:"3bf01d9f-44ea-4087-b2c0-cc56ad75625e";s:16:"oauth_expires_in";s:7:"5183999";s:30:"oauth_authorization_expires_in";s:7:"5183999";}";s:41:"hauth_session.linkedin.token.access_token";s:44:"s:36:"d68a8ce2-a800-489d-bc56-5e8a9a983b97";";s:48:"hauth_session.linkedin.token.access_token_secret";s:44:"s:36:"3bf01d9f-44ea-4087-b2c0-cc56ad75625e";";s:35:"hauth_session.linkedin.is_logged_in";s:4:"i:1;";}';
        $hybridauth_session_data = $paramsArray['session_key'];
        
        // Saving data gathered to file
        $outputData = new \AlgorithmsIO\Utilities\OutputData($outputFileLocation.'users.txt');

        // Script Vars
        $linkedin = new \AlgorithmsIO\DataNormalization\LinkedIn();

        try{
                // Save Session key also
                $outputArray['hybridauth_session_key'] = $hybridauth_session_data;
                
                // Set crawl time
                $outputArray['create_date'] = date("Y-m-d H:i:s");
                        
                // Setting up output array.
                $outputArray['source_user'] = array();
                $outputArray['friends'] = array();

                // hybridauth EP
                $hybridauth = new Hybrid_Auth( $config );

                // Restore session data
                $hybridauth->restoreSessionData( $hybridauth_session_data );
                $provider = $hybridauth->getAdapter("LinkedIn");

                // get the user profile 
                $user_profile = $provider->getUserProfile();

                //
                // Get the user's infor that gave us this access session
                //
                $response = $provider->api()->profile( 'id='.$user_profile->identifier.':(id,firstName,lastName,headline,location,industry,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,api-standard-profile-request,public-profile-url,email-address,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,related-profile-views,phone-numbers,bound-account-types,im-accounts,main-address,twitter-accounts,primary-twitter-account,connections)?format=json', 'get' );
                $outputArray['source_user'] = $linkedin->getUsersValues(json_decode($response['linkedin']), true);


                // Retrieves all of this user's connections
                $connections_list = $provider->api()->profile( '~/connections?format=json', 'get' );
                $connections_list_object = json_decode($connections_list['linkedin']);

                //
                // Harvest Source's Friends
                //

                $n=0;
                // Loop through the connection and get their info
                foreach($connections_list_object->values as $aConnection){		

                        $n++;
                        //if($n>3)
                        //    break;
                        
                        // To avoid LinkedIn API Throtteling.  We are going to 
                        // pause after 450 calls.  We are hitting the "Other's standard profiles"
                        // 500 limit.  Setting to sleep at 450 just to be safe.  
                        // https://developer.linkedin.com/documents/throttle-limits
                        if($n==450)
                            sleep(90000);

                        $response = $provider->api()->profile( 'id='.$aConnection->id.':(id,firstName,lastName,headline,location,industry,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,api-standard-profile-request,public-profile-url,email-address,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,related-profile-views,phone-numbers,bound-account-types,im-accounts,main-address,twitter-accounts,primary-twitter-account,connections)?format=json', 'get' );
                        $aUserInfo = $linkedin->getUsersValues(json_decode($response['linkedin']));
                        array_push($outputArray['friends'], $aUserInfo);
                }

                // Output crawled data
                $outputData->out(json_encode($outputArray)."\n");

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
                                   break;
                        case 7 : echo "User not connected to the provider."; 
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
        
        
    }
    /**
     * This is a required function.  The worker wrapper will call this after the
     * "runMe" function runs to get the results.  The results will be uploaded
     * back into the caller's datasource bucket.
     * 
     * @return string
     */
    public function getResults(){
        return "Success";
    }
}


