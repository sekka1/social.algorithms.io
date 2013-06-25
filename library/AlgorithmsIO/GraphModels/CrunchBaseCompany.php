<?php
/**
 * Garland
 * Add Crunchbase data into the the graph database
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

	class CrunchBaseCompany extends GraphBase{
		
		private $user;  // User Array
                private $dataSourceName = 'crunchbase';
		
		public function __construct(){
			parent::__construct();
		}
                /*
                 * $aUser - Is an array returned by AlgorithmsIO\DataNormalization\Crunchbase->getUsersValues()
                 * 
                 * @param array $aUser
                 */
                public function setUser($aUser){
                    $this->user = $aUser;
                }
                /**
                 * Gets the current main user node id
                 * @return int
                 */
                public function getMainUserIdNode(){
                    return $this->datasourceGUIDNode->getId();
                }
		/**
                 * Processes a users information.  THis user might already be in the graph db
                 * or not.  Will have to check to add or update this user's information.
                 * 
                 * @return boolean
                 */
                public function processUser(){
                    
                    $this->setCurrentUser();
                    $this->setUsersAttributes();
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
                * Adds an entire person in and updates it if the information changes
                */
               private function setUsersAttributes(){
                   $queryTemplate = "START
                                        startNode=node({startingNode})

                                        CREATE UNIQUE

                                        startNode-[:HAS_NAME]->(nodePersonNames:Person),
                                        startNode-[:HAS_DOB]->(nodePersonDOB:Person),
                                        startNode-[:HAS_IMAGE]->(nodePersonImageURL:Person),
                                        startNode-[:HAS_TAGES]->(nodePersonTags:Person)                                       

                                        CREATE UNIQUE
                                        startNode-[:DATA_FROM]->(nodeDataSourceNameNode:DatasourceName),
                                        startNode-[:CREATED]->(nodeDataMetaDataCreated:DataMetaData),
                                        startNode-[:INFO_URL]->(nodeDataMetaDataPersonURL)
                                        
                                        SET
                                        nodeDataSourceNameNode.value = {nodeDataSourceNameNode_value},
                                        nodeDataMetaDataCreated.created = {nodeDataMetaDataCreated_created},
                                        nodeDataMetaDataCreated.updated = {nodeDataMetaDataCreated_updated},
                                        nodeDataMetaDataPersonURL.value = {nodeDataMetaDataPersonURL_value},
                                        nodePersonNames.firstName = {nodePersonNames_firstName}, 
                                        nodePersonNames.lastName = {nodePersonNames_lastName}, 
                                        nodePersonDOB.born_year = {nodePersonDOB_born_year},
                                        nodePersonDOB.born_month = {nodePersonDOB_born_month},
                                        nodePersonDOB.born_day = {nodePersonDOB_born_day},
                                        nodePersonImageURL.image_url = {nodePersonImageURL_image_url},
                                        nodePersonTags.tags = {nodePersonTags_tags}

                                        RETURN 
                                        nodePersonNames";

//".$this->buildEmploymentPath()."                   
                   $parameters = array( 'startingNode'=>$this->datasourceGUIDNode->getId(),
                                        'nodeDataSourceNameNode_value'=>$this->dataSourceName,
                                        'nodeDataMetaDataCreated_created'=>$this->user['data_meta_data']['created_at'],
                                        'nodeDataMetaDataCreated_updated'=>$this->user['data_meta_data']['updated_at'],
                                        'nodeDataMetaDataPersonURL_value'=>$this->user['data_meta_data']['crunchbase_url'],
                                        'nodePersonNames_firstName'=>$this->user['person']['firstName'],
                                        'nodePersonNames_lastName'=>$this->user['person']['lastName'],
                                        'nodePersonDOB_born_year'=>$this->user['person']['born_year'],
                                        'nodePersonDOB_born_month'=>$this->user['person']['born_month'],
                                        'nodePersonDOB_born_day'=>$this->user['person']['born_day'],
                                        'nodePersonImageURL_image_url'=>$this->user['person']['image_url'],
                                        'nodePersonTags_tags'=>$this->user['person']['tag_list']
                                        );
                   //echo "<br/><br/>".$queryTemplate."<br/><br/>";
                   //echo "datasourceGUIDNode: ".$this->datasourceGUIDNode->getId()."<br/>";
                   
                    $query = new Cypher\Query($this->client, $queryTemplate, $parameters);
                    $r = $query->getResultSet();

                    // Insert Employment nodes
                    $this->buildEducationPath();
                    $this->buildEmploymentPath();
                  }
               /**
                * Builds the cypher path for all education and connecting to what this person was awarded
                * Should be used with $this->addPerson() to build the education nodes/relationships
                * 
                */
               private function buildEducationPath(){
                   //$pathString = '';
                   $count = 1;
                   foreach($this->user['educations'] as $anEducation){
                       $pathString = 'START
                                        startNode=node({startingNode})';
                       
                       // Create Unique of the education this person has
                       $pathString .= 'CREATE UNIQUE startNode-[:HAS_EDUCATION]->(nodeEducation'.$count.':Education{graduated_year:"'.$anEducation['graduated_year'].'", graduated_month:"'.$anEducation['graduated_month'].'", graduated_day:"'.$anEducation['graduated_day'].'"})';
                       
                       // MERGE - create the Degree node only if it doesnt exists
                       $pathString .= 'MERGE (nodeAwarded'.$count.':Degree {value: "'.$anEducation['type'].'"})';
                       // CREATE UNIQUE relationship from education to the degree awarded
                       $pathString .= ' CREATE UNIQUE nodeEducation'.$count.'-[:AWARDED]->(nodeAwarded'.$count.')';
                       
                       $count++;
                       
                       $parameters = array( 'startingNode'=>$this->datasourceGUIDNode->getId() );
                       $query = new Cypher\Query($this->client, $pathString, $parameters);
                       $r = $query->getResultSet();
                       
                       //if($count==8)
                       //    break;
                       
                   }
                   //return $pathString;
               }
               /**
                * Builds the cypher path for all education and connecting to what this person was awarded
                * Should be used with $this->addPerson() to build the employment nodes/relationships
                * 
                */
               private function buildEmploymentPath(){
                   //$pathString = '';
                   $count = 1;
                   foreach($this->user['employments'] as $anEmployment){
                       
                       $pathString = 'START
                                        startNode=node({startingNode})';
                       
                       // CREATE UNIQUE employment node for each employment
                       $pathString .= 'CREATE UNIQUE startNode-[:HAS_EMPLOYMENT]->(nodeEmployment'.$count.':Employment{is_past:"'.$anEmployment['is_past'].'", firm_permalink:"'.$anEmployment['firm_permalink'].'", firm_name:"'.$anEmployment['firm_name'].'"})';
                       
                       // Employment Title
                       // MERGE - create title node only if it doesnt exists
                       $pathString .= 'MERGE (nodeEmploymentTitle'.$count.':EmploymentTitle{value:"'.$anEmployment['title'].'"})';
                       // CREATE UNIQUE relationship from the employment node to this title
                       $pathString .= 'CREATE UNIQUE nodeEmployment'.$count.'-[:HAS_EMPLOYMENT_TITLE]->(nodeEmploymentTitle'.$count.')';
                       
                       // Employment Firm/Company name/worked at
                       // MERGE - create firm node only if it doesnt exists
                       $pathString .= 'MERGE (nodeEmploymentFirm'.$count.':EmploymentFirm{value:"'.$anEmployment['firm_name'].'"})';
                       // CREATE UNIQUE relationship from the employment node to this firm name
                       $pathString .= 'CREATE UNIQUE nodeEmployment'.$count.'-[:HAS_EMPLOYMENT_FIRM]->(nodeEmploymentFirm'.$count.')';
                    
                       $count++;
                       
                       $parameters = array( 'startingNode'=>$this->datasourceGUIDNode->getId() );
                       $query = new Cypher\Query($this->client, $pathString, $parameters);
                       $r = $query->getResultSet();
                       
                       //if($count==9)
                       //    break;
                   }
                   //return $pathString;
               }
	}		
}
	