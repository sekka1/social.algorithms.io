<?php

namespace AlgorithmsIO\GraphModels{

require("phar://".dirname(__FILE__)."/neo4jphp.phar");

use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;


	class GraphBase{
		
		protected $client;
		protected $datasourceGUIDNode; // This is a node object of the main id node
		
		public function __construct(){
			$this->client = new Client();
		}
		
		
		/**
		 * Add a node, with properties.  Returns the id of the node created.
		 * 
		 * $properties - key=>value pair array of properties
		 * return - client object resultSet()
		 * 
		 * @param array $properties
		 * @return Client
		 */
		protected function addNode($properties){
			$id = $this->client->makeNode()->setProperties($properties)->save();
			return $id;
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
		 * @param Node $nodeStartObject
		 * @param Node $nodeToObject
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
		protected function createUniqueNode(){
			
		}
	}		
}
	