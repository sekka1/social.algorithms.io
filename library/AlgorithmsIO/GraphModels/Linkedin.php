<?php
/**
 * Garland
 * Add LinkedIn data into the the graph database
 * 
 * addUser() - Addes a "user" from a CrunchBase Normalized data set into the graph database
 * 			   as one user with his various connections like jobs, education, etc
 * 
 */

namespace AlgorithmsIO\GraphModels{

include_once(dirname(__FILE__).'/GraphBase.php');
use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;

	class Linkedin extends GraphBase{
		
		private $user;  // User Array
                private $dataSourceName = 'linkedin';
                
                private $isHarvestSourceFriend = false;
                private $harvestSourceLinkedInGUID = null; // GUID of where this information came from.
                                                           // null if this is the source that is giving us 
                                                           // the info
		
		public function __construct(){
			parent::__construct();
		}
                /*
                 * $aUser - Is an array returned by AlgorithmsIO\DataNormalization\Crunchbase->getUsersValues()
                 * 
                 * @param array $aUser
                 */
                public function setValues($aUser){
                    $this->user = $aUser;
                }
                /**
                 * Sets is $isHarvestSource to true
                 */
                public function isHarvestSourceFriend(){
                    $this->isHarvestSourceFriend = true;
                }
                /**
                 * 
                 * @param string $guid linkedin guid of the user
                 */
                public function setHarvestSourceGUID($guid){
                    $this->harvestSourceLinkedInGUID = $guid;
                }
		/**
                 * Processes a users information.  THis user might already be in the graph db
                 * or not.  Will have to check to add or update this user's information.
                 * 
                 * @return boolean
                 */
                public function process(){
                    
                    $this->insertData();
                }

                /**
		 * Adds the main ID node.  This is the unique identifier for this person from this source
		 * 
		 * @return boolean
		 */
		private function setCurrentUser(){	
                    $userIsSet = false;

                    // MERGE on this node - which will create it if it doesnt exist or return the node
                    $queryTemplate = "MERGE (userGUIDNode:PersonGUID{source_uid:'".$this->user['source_uid']."', datasource_name:'".$this->dataSourceName."'})
                                        SET userGUIDNode.created = timestamp()
                                        RETURN userGUIDNode;";
                    $query = new Cypher\Query($this->client, $queryTemplate);
                    $r = $query->getResultSet();
                   
                   foreach ($r as $row) {
                        $this->datasourceGUIDNode = $row['userGUIDNode'];
                        $userIsSet = true;
                    }
                   
                    return $userIsSet;
		}
               /**
                * Inserts all the data into the graph db.
                * 
                */
               private function insertData(){
                   $this->insertPerson();
                   $this->insertEmployment();
                   if($this->isHarvestSourceFriend)
                       $this->createFriendRelationship();
               }
               /**
                * Inserts/update the main person node for linkedin
                * 
                */
               private function insertPerson(){
                   $pathString = '// Inserting the main person node
                                MERGE (userGUIDNode:PersonGUID{source_uid:"'.$this->user['source_uid'].'", datasource_name:"'.$this->dataSourceName.'"})
                                SET userGUIDNode.created = timestamp()';
                                
                                foreach($this->user['person'] as $key=>$val){
                                    $pathString .= ' SET userGUIDNode.'.$key.' = "'.$val.'"';
                                }

                   $pathString .= 'RETURN userGUIDNode';
echo $pathString;           
                 
                   $query = new Cypher\Query($this->client, $pathString);
                   $r = $query->getResultSet();                 
               }
               /**
                * Inserts/update employment
                */
               private function insertEmployment(){
                   $n=0;
                   foreach($this->user['employments'] as $item){
                       $pathString = '// Inserting Employment
                                    MERGE (userGUIDNode:PersonGUID{source_uid:"'.$this->user['source_uid'].'", datasource_name:"'.$this->dataSourceName.'"})
                                    MERGE (nodeEmployment'.$n.':Employment{position_id:"'.$item['position_id'].'"})
                                    CREATE UNIQUE userGUIDNode-[:HAS_EMPLOYMENT]->(nodeEmployment'.$n.')
                                    
                                    // Employment Firm/company node and the relationship to it
                                    MERGE (nodeEmploymentFirm'.$n.':EmploymentFirm{id:"'.$item['company_id'].'", name:"'.$item['name'].'"})
                                    CREATE UNIQUE (nodeEmployment'.$n.')-[:HAS_EMPLOYMENT_FIRM]->(nodeEmploymentFirm'.$n.')
                                    ';
                       
                                    foreach($item as $key=>$val){
                                        $pathString .= ' SET nodeEmployment'.$n.'.'.$key.' = "'.$val.'"';
                                    }
echo $pathString."\n\n"; 
                       $query = new Cypher\Query($this->client, $pathString);
                       $r = $query->getResultSet();
                       $n++;

                   }
               }
               /**
                * Creates a bi-directional friend relationship
                */
               private function createFriendRelationship(){
                   $pathString = '// Source Friend
                                    MERGE (sourceFriend:PersonGUID{source_uid:"'.$this->harvestSourceLinkedInGUID.'", datasource_name:"linkedin"})
                                    // Friend
                                    MERGE (friend:PersonGUID{source_uid:"'.$this->user['source_uid'].'", datasource_name:"linkedin"})
                                    // Create Friend Relationship
                                    CREATE UNIQUE (sourceFriend)-[:IS_FRIEND]->(friend)
                                    CREATE UNIQUE (friend)-[:IS_FRIEND]->(sourceFriend)
                                    RETURN sourceFriend, friend';
echo $pathString."\n\n"; 
                   $query = new Cypher\Query($this->client, $pathString);
                   $r = $query->getResultSet();
               }
	}		
}
	