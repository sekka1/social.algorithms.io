<?php
/**
 * Given a list of Crunchbase users, it will go through them to get their information
 * from the Crunchbase API then insert it into the graph database.
 * 
 */
ini_set('error_reporting', E_ALL);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 6000);

// Hybrid config and includes
$config = 'library/hybridauth/hybridauth/config.php';
require_once( "library/hybridauth/hybridauth/Hybrid/Auth.php" );


// Data Normalization
include('library/AlgorithmsIO/DataNormalization/CrunchBase.php');
// Graph DB
include('library/AlgorithmsIO/GraphModels/CrunchBase.php');

// Script Vars
//$all_users = array(); 
$crunchbaseNormalize = new \AlgorithmsIO\DataNormalization\CrunchBase();
$crunchbaseGraphModel = new \AlgorithmsIO\GraphModels\CrunchBase();

session_start(); 

// List of all Crunchbase Users
//$crunchbase_users = '[{"first_name":"Ben","last_name":"Elowitz","permalink":"ben-elowitz"},{"first_name":"Kevin","last_name":"Flaherty","permalink":"kevin-flaherty"},{"first_name":"Raju","last_name":"Vegesna","permalink":"raju-vegesna"},{"first_name":"Ian","last_name":"Wenig","permalink":"ian-wenig"},{"first_name":"Kevin","last_name":"Rose","permalink":"kevin-rose"},{"first_name":"Jay","last_name":"Adelson","permalink":"jay-adelson"},{"first_name":"Owen","last_name":"Byrne","permalink":"owen-byrne"},{"first_name":"Ron","last_name":"Gorodetzky","permalink":"ron-gorodetzky"},{"first_name":"Mark","last_name":"Zuckerberg","permalink":"mark-zuckerberg"},{"first_name":"Dustin","last_name":"Moskovitz","permalink":"dustin-moskovitz"},{"first_name":"Owen","last_name":"Van Natta","permalink":"owen-van-natta"},{"first_name":"Matt","last_name":"Cohler","permalink":"matt-cohler"},{"first_name":"Chris","last_name":"Hughes","permalink":"chris-hughes"},{"first_name":"Alex","last_name":"Welch","permalink":"alex-welch"},{"first_name":"Darren","last_name":"Crystal","permalink":"darren-crystal"},{"first_name":"Michael","last_name":"Clark","permalink":"michael-clark"},{"first_name":"Greg","last_name":"Wimmer","permalink":"greg-wimmer"},{"first_name":"Peter","last_name":"Foster","permalink":"peter-foster"},{"first_name":"Heather","last_name":"Dana","permalink":"heather-dana"},{"first_name":"Peter","last_name":"Pham","permalink":"peter-pham"},{"first_name":"Scott","last_name":"Penberthy","permalink":"scott-penberthy"},{"first_name":"Alice","last_name":"Lankester","permalink":"alice-lankester"},{"first_name":"Alex","last_name":"Musil","permalink":"alex-musil"},{"first_name":"Peter","last_name":"Thiel","permalink":"peter-thiel"},{"first_name":"Gus","last_name":"Tai","permalink":"gus-tai"},{"first_name":"David","last_name":"Sacks","permalink":"david-sacks"},{"first_name":"Alan","last_name":"Braverman","permalink":"alan-braverman"},{"first_name":"Ken","last_name":"Howery","permalink":"ken-howery"},{"first_name":"Luke","last_name":"Nosek","permalink":"luke-nosek"},{"first_name":"Sean","last_name":"Parker","permalink":"sean-parker"},{"first_name":"George","last_name":"Zachary","permalink":"george-zachary"},{"first_name":"Greg","last_name":"Waldorf","permalink":"greg-waldorf"},{"first_name":"Jason","last_name":"Rubin","permalink":"jason-rubin"},{"first_name":"Andy","last_name":"Gavin","permalink":"andy-gavin"},{"first_name":"Jason","last_name":"Kay","permalink":"jason-kay"},{"first_name":"Evan","last_name":"Williams","permalink":"evan-williams"},{"first_name":"Garrett","last_name":"Camp","permalink":"garrett-camp"},{"first_name":"Geoff","last_name":"Smith","permalink":"geoff-smith"},{"first_name":"Justin","last_name":"LeFrance","permalink":"justin-lafrance"},{"first_name":"Jack","last_name":"Dorsey","permalink":"jack-dorsey"},{"first_name":"Jason","last_name":"Goldman","permalink":"jason-goldman"},{"first_name":"Biz","last_name":"Stone","permalink":"biz-stone"},{"first_name":"Eyal","last_name":"Gever","permalink":"eyal-gever"},{"first_name":"Efrat","last_name":"Moshkoviz","permalink":"efrat-moshkoviz"},{"first_name":"Amihay","last_name":"Zer Kavod","permalink":"amihay-zer-kavod"}]';
//$crunchbase_users = '[{"first_name":"Kevin","last_name":"Flaherty","permalink":"kevin-flaherty"}]';
$crunchbase_users = file_get_contents('data/crunchbase_users_061313.json');
//$crunchbase_users  = '[{"first_name":"Ben","last_name":"Elowitz","permalink":"trevor-legwinski"}]';
        
        
	try{
            
		// hybridauth EP
		$hybridauth = new Hybrid_Auth( $config );

		// automatically try to login with the given provider
		$provider = $hybridauth->getAdapter( "Crunchbase" );
		
		//$person = $provider->get('person/david-rohrsheim.js');
                //$person = $provider->get('person/amanda-north.js');
		
		//print_r($person);
		
		//$aUser = $crunchbaseNormalize->getUsersValues(json_decode($person['crunchbase']));
		
	//print_r($aUser);
		
                system('echo "Starting\n" > /tmp/adding_crunchbase.txt');
                $crunchbase_users_array = json_decode($crunchbase_users);
                for($i=7241;$i<count($crunchbase_users_array);$i++){
                //foreach($crunchbase_users_array as $aUser){
                
                    // Get data from crunchbase
                    $person = $provider->get('person/'.$crunchbase_users_array[$i]->permalink.'.js');
                    $aUserInfo = $crunchbaseNormalize->getUsersValues(json_decode($person['crunchbase']));

                    // Save This into the graph database.
                    $graphModel = new \AlgorithmsIO\GraphModels\CrunchBase();
                    $graphModel->setUser($aUserInfo);
                    $graphModel->processUser();
                    
                    //echo "Added user into node: ". $graphModel->getMainUserIdNode()."<br/>";
                    
                    system('echo '.$i.'") Added user into node: "'. $graphModel->getMainUserIdNode().'"\n" >> /tmp/adding_crunchbase.txt');
                    
                    //break;
                }
                

                
                //
                // Gets a list of all the people that crunchbase keeps track of
                //
                /*
                echo "<br/><br/>";
                $person = $provider->get('people.js');
                print_r($person);
                 * 
                 */
                
                
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
