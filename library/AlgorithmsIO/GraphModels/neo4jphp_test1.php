<?php

error_reporting(-1);
ini_set('display_errors', 1);

require("phar://neo4jphp.phar");

use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Cypher;

$client = new Client();
//$actors = new NodeIndex($client, 'actors');

$keanu = $client->makeNode()->setProperty('name', 'Keanu Reeves')->save();
addLabel($client, $keanu->getId(), 'New');


/*
$queryTemplate = 'START n=node(*) RETURN n';
$queryTemplate = 'MATCH (a:Person)
		  RETURN a.uid';

$query = new Cypher\Query($client, $queryTemplate);
$result = $query->getResultSet();

print_r($result);
*/

function addLabel($client, $nodeId, $label){

	$queryTemplate = 'START n=node('.$nodeId.')
			  SET n :New
			  RETURN n;';
	$query = new Cypher\Query($client, $queryTemplate);
	return $query->getResultSet();
}
