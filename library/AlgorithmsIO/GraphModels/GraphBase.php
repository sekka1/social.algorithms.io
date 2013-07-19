<?php

namespace AlgorithmsIO\GraphModels{

require("phar://".dirname(__FILE__)."/neo4jphp.phar");

use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;


	class GraphBase{
		
                private $db_host = '166.78.27.160';
                private $db_port = '7474';
                
		protected $client;
		protected $datasourceGUIDNode = null; // This is a node object of the main id node
		
		public function __construct(){
                    $host = '166.78.27.160';
                    $port = '7474';
                    $this->client = new Client($host, $port);
		}
		
		
		/**
		 * Add a node, with properties.  Returns the id of the node created.
                 * Can also add the label to this new node also
		 * 
		 * $properties - key=>value pair array of properties
		 * return - client object resultSet()
		 * 
		 * @param array $properties
                 * @param string $label
		 * @return Client
		 */
		protected function addNode($properties, $label=null){
			$node = $this->client->makeNode()->setProperties($properties)->save();
                        
                        if($label != null)
                            $this->addLabel($node->getId(), $label);
                        
			return $node;
		}
		
		/**
		 * Adds a label to a node.  Doing it this way b/c the Neo4J PHP API doesn yet support
		 * the v2.0 "Lables" yet.
		 * 
		 * @param int $nodeId
		 * @param string $label
		 * @return Cypher\Query\resultSet()
		 */
		protected function addLabel($nodeId, $label){
		
			$queryTemplate = 'START n=node('.$nodeId.')
					  SET n :'.$label.'
					  RETURN n;';
			$query = new Cypher\Query($this->client, $queryTemplate);
			return $query->getResultSet();
		}
		/**
		 * Creates a relationthip from start to To node with a type
		 * 
		 * @param Everyman\\Neo4j\\Node $nodeStartObject
		 * @param Everyman\\Neo4j\\Node $nodeToObject
		 * @param string $type
		 * @return client object resultSet()
		 */
		protected function addRelationship($nodeStartObject, $nodeToObject, $type){
			return $nodeStartObject->relateTo($nodeToObject, $type)->save();
		}
		/**
		 * Add a timestamp parameter to this node
		 * 
		 * @param int $nodeId
		 * @param string $timeStampName
		 * @return Cypher\Query\resultSet()
		 */
		protected function addTimestamp($nodeId, $timeStampName){
			$queryTemplate = 'START n=node('.$nodeId.')
					  SET n.'.$timeStampName.' = timestamp()
					  RETURN n;';
			$query = new Cypher\Query($this->client, $queryTemplate);
			return $query->getResultSet();
		}

   
         
        
	}	
}
	