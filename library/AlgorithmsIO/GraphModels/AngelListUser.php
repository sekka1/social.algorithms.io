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

	class AngelListUser extends GraphBase{
		
		private $data;  // main data Array
                private $dataSourceName = 'angelList';
		
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
                 * Processes a users information.  THis user might already be in the graph db
                 * or not.  Will have to check to add or update this user's information.
                 * 
                 * @return boolean
                 */
                public function process(){
                    
                    $this->insertData();
                    $this->addSkills();
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
                   //
                   //
                   $pathString .= '// MERGE main uid node
                                        MERGE (angel_list_uid_node:PersonGUID{value:'.$this->data['source_uid'].',datasource_name:"'.$this->dataSourceName.'"})
                                        // Set properties for this node
                                        //SET angel_list_uid_node.name = "brandon leonardo"
                                        //SET angel_list_uid_node.follower_count = 531'
                            
                                        .$this->buildSetPropertyString($this->data['person']);
                   
//echo          $pathString."\n\n";         

                    try{
                        $query = new Cypher\Query($this->client, $pathString);

                        $r = $query->getResultSet();
                    }catch(\Everyman\Neo4j\Exception $e){
                        echo $e;
                        echo $pathString."\n";
                    }
//print_r($r);
               }
               /**
                * Adding skills
                */
               private function addSkills(){
                   
                   foreach($this->data['skills'] as $anItem){
                        $pathString = '';

                        //
                        //
                        //
                        $pathString .= '// MERGE main uid node
                                             MERGE (angel_list_uid_node:PersonGUID{value:'.$this->data['source_uid'].',datasource_name:"'.$this->dataSourceName.'"}),
                                             (skill_node:alSkill{id:'.$anItem['id'].'})
                                             // Set properties for this node
                                             //SET skill_node.tag_type = "skilltag"
                                             //SET skill_node.name = "ruby on rails"
                                                     // ETC
                                             '.$this->buildSetPropertyString($anItem).'


                                             // Create Relationship
                                             CREATE UNIQUE (angel_list_uid_node)-[:HAS_SKILL]->(skill_node)';

     //echo          $pathString."\n\n"; 
     
                         try{
                             $query = new Cypher\Query($this->client, $pathString);

                             $r = $query->getResultSet();
                         }catch(\Everyman\Neo4j\Exception $e){
                             echo $e;
                             echo $pathString."\n";
                         }
                   }
               }
               /**
                * Creates the set property string for making this node
                * 
                * @return string Description
                */
               private function buildSetPropertyString($properties){
                   $string = '';
                   
                   foreach($properties as $key=>$val){
                            $string .= ' SET angel_list_uid_node.'.$key.' = '.$this->getParamQuoting($val);
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
