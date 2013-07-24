<?php
/**
 * Imports Akkadian Company data into the graph DB
 * 
 * @author garland
 */
namespace AlgorithmsIO\GraphModels{

include_once(dirname(__FILE__).'/GraphBase.php');
use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;

	class AngelListRole extends GraphBase{
		
		private $data;  // main data Array
                private $dataSourceName = 'angelList';
                
                private $personGUID;
		
		public function __construct(){
			parent::__construct();
		}
                /*
                 * $aUser - Is an array returned by AlgorithmsIO\DataNormalization\Crunchbase->getUsersValues()
                 * 
                 * @param array $aUser
                 */
                public function setValues($data){
                    $this->data = $data;
                }
                /**
                 * Setting the person's guid whos these role's belong to
                 *
                 * @param int $guid
                 */
                public function setPersonGUID($guid){
                    $this->personGUID = $guid;
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
                * Need to pass in the person who owns all these roles.
                * 
                * 
                */
               private function insertData(){
                   
                   //
                   //
                   //
                   foreach($this->data['startup_roles'] as $item){
                       
                      $pathString = '';
                      
                      $pathString .= '// MERGE main uid node
                                        MERGE (angel_list_uid_node:PersonGUID{value:'.$this->personGUID.',datasource_name:"'.$this->dataSourceName.'"}),
                                        (role_node:alRole{role_id:"'.$item['role_id'].'"})
                                        // Set properties for this node
                                        //SET role_node.role = "employee"
                                        //SET role_node.created_at = "2012-11-28t02:26:13z"
                                        
                                        '.$this->buildSetPropertyString($item).'

                                                // ETC
                                        // Create Relationship
                                        CREATE UNIQUE (angel_list_uid_node)-[:HAS_ROLE]->(role_node)';

        //echo          $pathString."\n\n";         

                            try{
                                $query = new Cypher\Query($this->client, $pathString);

                                $r = $query->getResultSet();
                            }catch(\Everyman\Neo4j\Exception $e){
                                echo $e;
                                echo $pathString."\n";
                            }
                   }
//print_r($r);

               }
               /**
                * Creates the set property string for making this node
                * 
                * @return string Description
                */
               private function buildSetPropertyString($properties){
                   $string = '';
                   
                   foreach($properties as $key=>$val){
                            $string .= ' SET role_node.'.$key.' = '.$this->getParamQuoting($val);
                   }
                   return $string;
               }
               /**
                * Returns the params either in quotes or not.  Based on
                * the type.  This can be directly returned into a cypher query.
                * 
                * Quotes used:  " - double quotes
                * 
                * @param string $param
                * @return string
                */
               private function getParamQuoting($param){
                   if(is_numeric($param))
                       return $param;
                   else
                       return '"'.$param.'"';
               }
	}		
}


?>
