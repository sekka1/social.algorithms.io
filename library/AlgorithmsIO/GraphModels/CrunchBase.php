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
			
			if(! $this->addSourceUIDNode()){
				throw new Exception( "Failed to add main id node");
				exit;
			}
			$this->addDataMetaDataNodes();
			$this->addPersonNodes();
			$this->addEducationNodes();
		}
		
		/**
		 * Adds the main ID node.  This is the unique identifier for this person from this source
		 * 
		 * @return boolean
		 */
		private function addSourceUIDNode(){	
			$didAdd = false;
			
			// Add Node
			$properties['source_uid'] = $this->user['source_uid'];
			$node = $this->addNode($properties);
			$this->datasourceGUIDNode = $node;
				
			// Add Label to node
			$results = $this->addLabel($this->datasourceGUIDNode->getId(), 'Person');
			// Add timestamp
			$results = $this->addTimestamp($this->datasourceGUIDNode->getId(), 'created');
				
			$didAdd = true;
				
			return $didAdd;
		}
		/**
		 * Add nodes contained in the persons data
		 * 
		 */
		private function addPersonNodes(){
			$this->addPersonNames();
			$this->addPersonDOB();
			$this->addPersonImageURLs();
			$this->addPersonTags();
		}
		/**
		 * Add meta data about this data
		 */
		private function addDataMetaDataNodes(){
			$this->addDataMetaDataCreated();
			$this->addDataMetaDataPersonURL();
		}
		/**
		 * Add in all education nodes
		 */
		private function addEducationNodes(){
			// Add each of the education nodes in the education node structure
			foreach($this->user['educations'] as $anEducation){
				
				// Add Education node
				$education_node = $this->addEducationMainEducationNode();

				// Awarded node
				$this->addEducationAwardedNode($education_node);
				
				// Attended node
				$this->addEducationAttendedNode($education_node);	
			}
		}
		/**
		 * Add the names associated with this person and makes a relationship link to the mainIdNode
		 * 
		 * @return boolean
		 */
		private function addPersonNames(){
			$didAdd = false;
			
			// Add Node
			$properties['firstName'] = $this->user['person']['firstName'];
			$properties['lastName'] = $this->user['person']['lastName'];
			$node = $this->addNode($properties);
											
			// Add Label
			$results = $this->addLabel($node->getId(), 'Person');
			
			// Add Relationship to mainIdNode
			$this->addRelationship($this->datasourceGUIDNode, $node, 'HAS_NAME');
		}
		/**
		 * Add DOB information
		 * 
		 */
		private function addPersonDOB(){
			// Add Node
			$properties['born_year'] = $this->user['person']['born_year'];
			$properties['born_month'] = $this->user['person']['born_month'];
			$properties['born_day'] = $this->user['person']['born_day'];
			$node = $this->addNode($properties);
			// Add Label
			$results = $this->addLabel($node->getId(), 'Person');
			// Add Relationship to mainIdNode
			$this->addRelationship($this->datasourceGUIDNode, $node, 'HAS_DOB');
		}
		/**
		 * Adds image urls of this person
		 */
		private function addPersonImageURLs(){
			// Add Node
			$properties['image_url'] = $this->user['person']['image_url'];
			$node = $this->addNode($properties);
			// Add Label
			$results = $this->addLabel($node->getId(), 'Person');
			// Add Relationship to mainIdNode
			$this->addRelationship($this->datasourceGUIDNode, $node, 'HAS_IMAGE');
		}
		/**
		 * Adds tags for this person
		 */
		private function addPersonTags(){
			// Add Node
			$properties['tags'] = $this->user['person']['tag_list'];
			$node = $this->addNode($properties);
			// Add Label
			$results = $this->addLabel($node->getId(), 'Person');
			// Add Relationship to mainIdNode
			$this->addRelationship($this->datasourceGUIDNode, $node, 'HAS_TAGS');
		}
		/**
		 * Add created and modify information about this data
		 */
		private function addDataMetaDataCreated(){
			// Add Node
			$properties['created'] = $this->user['data_meta_data']['created_at'];
			$properties['updated'] = $this->user['data_meta_data']['updated_at'];
			$node = $this->addNode($properties);
			// Add Label
			$results = $this->addLabel($node->getId(), 'DataMetaData');
			// Add Relationship to mainIdNode
			$this->addRelationship($this->datasourceGUIDNode, $node, 'CREATED');
		}
		/**
		 * Add the Crunchbase URL to this information
		 */
		private function addDataMetaDataPersonURL(){
			// Add Node
			$properties['created'] = $this->user['data_meta_data']['crunchbase_url'];
			$node = $this->addNode($properties);
			// Add Label
			$results = $this->addLabel($node->getId(), 'DataMetaData');
			// Add Relationship to mainIdNode
			$this->addRelationship($this->datasourceGUIDNode, $node, 'INFO_URL');
		}
		/**
		 * Main education node
		 * 
		 * Education->[:AWARDED]->MBA
		 * 			->[:ATTENDED]->Standford
		 * 
		 * @return Client
		 */
		private function addEducationMainEducationNode(){
			// Add Node
			$properties['graduated_year'] = $this->user['graduated_year'];
			$properties['graduated_month'] = $this->user['graduated_month'];
			$properties['graduated_day'] = $this->user['graduated_day'];
			$node = $this->addNode($properties);
			// Add Label
			$results = $this->addLabel($node->getId(), 'Education');
			// Add Relationship to mainIdNode
			$this->addRelationship($this->datasourceGUIDNode, $node, 'HAS_EDUCATION');
				
			return $node;
		}
		/**
		 * AWARDED node for education
		 */
		private function addEducationAwardedNode($parentNode){
			// Add Node
			$properties['value'] = $this->user['type'];
			$node = $this->addNode($properties);
			// Add Label
			$results = $this->addLabel($node->getId(), 'Degree');
			// Add Relationship to mainIdNode
			$this->addRelationship($parentNode, $node, 'AWARDED');
		}
		/**
		 * ATTENDED node for education
		 */
		private function addEducationAttendedNode($parentNode){
			// Add Node
			$properties['value'] = $this->user['institution'];
			$node = $this->addNode($properties);
			// Add Label
			$results = $this->addLabel($node->getId(), 'Institution');
			// Add Relationship to mainIdNode
			$this->addRelationship($parentNode, $node, 'ATTENDED');
		}
	}		
}
	