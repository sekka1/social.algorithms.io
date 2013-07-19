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

	class AkkadianRevenue extends GraphBase{
		
		private $data;  // main data Array
                private $dataSourceName = 'akkadian';
		
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
                   $pathString .= '// Create Akkadian Main uid node and properties
                                    MERGE (akkadian_uid_node:CompanyGUID{value:'.$this->data['idCompany'].',datasource_name:"'.$this->dataSourceName.'"}),
                                    (revenue_node:AkkadianRevenue{idCompany:'.$this->data['idCompany'].'})

                                    // Set this nodes properties
                                    //SET revenue_node.activeMI = 1
                                    '.
                                    $this->buildSetPropertyString()
                                    .'
                                            // ETC

                                    // Create Relationship
                                    CREATE UNIQUE akkadian_uid_node-[r:HAS_REVENUE_INFO]->revenue_node

                                    RETURN akkadian_uid_node, revenue_node, r';
//echo          $pathString;         
                    $query = new Cypher\Query($this->client, $pathString);
                    $r = $query->getResultSet();
//print_r($r);
               }
               /**
                * Creates the set property string for making this node
                * 
                * @return string Description
                */
               private function buildSetPropertyString(){
                   $string = '';
                   
                   foreach($this->data as $key=>$val){
                            $string .= ' SET revenue_node.'.$key.' = '.$this->getParamQuoting($val);
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
