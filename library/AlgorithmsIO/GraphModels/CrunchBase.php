<?php
/**
 * Garland
 * Addes Crunchbase data into the the graph database
 * 
 * addUser() - Addes a "user" from a CrunchBase Normalized data set into the graph database
 * 			   as one user with his various connections like jobs, education, etc
 * 
 */

namespace AlgorithmsIO\GraphModels{

include_once(dirname(__FILE__).'/GraphBase.php');
		
	class CrunchBase extends GraphBase{
		
		private $user;  // User Array
		
		public function __construct(){
			parent::__construct();
		}
		
		/**
		 * Adds a user into the graph database based on a user and this user's information
		 * 
		 * $aUser - Is an array returned by AlgorithmsIO\DataNormalization\Crunchbase->getUsersValues()
		 * 
		 * @param array $aUser
		 * @return boolean
		 */
		public function addUser($aUser){
			
			$this->user = $aUser;
			
			$this->addMainIDNode();
		}
		
		private function addMainIDNode(){
			
			try{
				// Add Node
				$id = $this->client->makeNode()->setProperty('id', $this->user['id'])->save();
				// Add Label to node
				$results = $this->addLabel($id->getId(), 'Person');
			}catch(Exception $e){
				print_r($e);
			}
		}
		
	}		
}
	