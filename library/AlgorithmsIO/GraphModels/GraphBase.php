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
		
		public function __construct(){
			$this->client = new Client();
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
		
	}		
}
	