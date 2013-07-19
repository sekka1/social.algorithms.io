<?php
/**
 * 
 * Add Crunchbase data into the the graph database
 * 
 * addUser() - Addes a "user" from a CrunchBase Normalized data set into the graph database
 * 			   as one user with his various connections like jobs, education, etc
 * 
 * @author Garland
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
                private $dataSourceName = 'crunchbaseCompany';
		
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
                 * Processes a users information.  THis user might already be in the graph db
                 * or not.  Will have to check to add or update this user's information.
                 * 
                 * @return boolean
                 */
                public function process(){
                    
                    $this->insertData();
                }
               /**
                * Inserts the data into the graph db.
                * 
                * FIXME: Had to separate the insert query out into multiple sections.  If trying
                * to do one big query, the DB throws some weird error.
                */
               private function insertData(){
                   
                   $pathString = '';
                   
                   //
                   // Build Employment Firm node and properties
                   //
                   $pathString .= '// Build Employment Firm node and properties
                                    MERGE (nodeEmploymentFirm:EmploymentFirm{value:"'.$this->user['source_uid'].'"})
                                    SET nodeEmploymentFirm.crunchbase_guid = "'.$this->user['source_uid'].'"
                                    ';
                                    // Building company parameters
                                    foreach($this->user['company'] as $key=>$val){
                                        $pathString .= ' SET nodeEmploymentFirm.'.$key.' = "'.$val.'"';
                                    }
                                    // Building crunchbased meta data parameters
                                    foreach($this->user['data_meta_data'] as $key=>$val){
                                        $pathString .= ' SET nodeEmploymentFirm.'.$key.' = "'.$val.'"';
                                    }
//echo $pathString . "\n\n";                                 
                    $query = new Cypher\Query($this->client, $pathString);
                    $r = $query->getResultSet();


                    // Clear the pathString for subsequent querys.  It seems doing it all in one big query
                    // neo4j returns an error
                    $pathString = '';

                   // 
                   // Build funding round nodes, invetor nodes, and properties for them
                   // 
                   for($n=0;$n<count($this->user['funding']);$n++){
                       
                       //Creating/Setting Funding node(x) data
                       $pathString .= '// Creating/Setting Funding node(x) data
                                        MERGE (nodeEmploymentFirm:EmploymentFirm{value:"'.$this->user['source_uid'].'"})
                                        CREATE UNIQUE nodeEmploymentFirm-[:HAS_FUNDING]->(nodeFunding'.$n.':Funding{round:"'.$this->user['funding'][$n]['round'].'"})
                                        ';    
                                        // Building funding round parameters
                                        foreach($this->user['funding'][$n] as $key=>$val){
                                            if(!is_array($val)){
                                                $pathString .= ' SET nodeFunding'.$n.'.'.$key.' = "'.$val.'"';
                                            }
                                        }
//echo $pathString . "\n\n";                     
                        $query = new Cypher\Query($this->client, $pathString);
                        $r = $query->getResultSet();
                        $pathString = '';
                       
                       // 
                       // Creating/Setting Funding node1 Investors and relationship
                       // 
                       for($i=0;$i<count($this->user['funding'][$n]['investors']);$i++){
                            $pathString .= '// Creating/Setting Funding node1 Investors and relationship
                                            
                                            // The next 2 statement is in here again to setup where to place the investor nodes
                                            MERGE (nodeEmploymentFirm:EmploymentFirm{value:"'.$this->user['source_uid'].'"})
                                            CREATE UNIQUE nodeEmploymentFirm-[:HAS_FUNDING]->(nodeFunding'.$n.':Funding{round:"'.$this->user['funding'][$n]['round'].'"})

                                            MERGE (nodeInvestor'.$n.'_'.$i.':Investor{value:"'.$this->user['funding'][$n]['investors'][$i]['permalink'].'"})
                                            CREATE UNIQUE nodeFunding'.$n.'-[:HAS_INVESTOR]->(nodeInvestor'.$n.'_'.$i.')
                                            SET nodeInvestor'.$n.'_'.$i.'.name = "'.$this->user['funding'][$n]['investors'][$n]['name'].'"
                                             ';
//echo $pathString . "\n\n";                     
                            $query = new Cypher\Query($this->client, $pathString);
                            $r = $query->getResultSet();
                            $pathString = '';
                       }

                        
                        
                        $pathString = '';
                   }
               }
	}		
}
	