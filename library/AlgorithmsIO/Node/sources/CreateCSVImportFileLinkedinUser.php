<?php
/**
 * Creates the Batch Importers Tab CSV file 
 * 
 * LinkedIn data
 * 
 */
ini_set('memory_limit','4024M');
include_once('library/AlgorithmsIO/Node/sources/CSVImportBase.php');
include_once('library/AlgorithmsIO/Node/Import/LinkedInUser.php');
ini_set('max_execution_time', 6000);

$mysqlHost = 'localhost';
$mysqlPort = 3306;
$mysqlUser = 'root';
$mysqlPassword = 'sunshine';
$dbname = 'akkadian';
$table_node = 'zz_node_db_import_table_not_akkadian_stuff';
$table_relationship = 'zz_relationship_db_import_table_not_akkadian_stuff';


$createCSV = new AlgorithmsIO\Node\sources\CSVImportBase();
$createCSV->setDBConnection($mysqlHost, $mysqlPort, $mysqlUser, $mysqlPassword, $dbname, $table_node, $table_relationship);
$createCSV->setGraphModel('LinkedinUser');
$createCSV->setSourceDataFile("/Users/gkan/Downloads/neo4j-stuff/source_data/linkedin_users_082013.txt");
$createCSV->setDatasourceName('Linkedin');
$createCSV->setOutPutLocation('/Users/gkan/Downloads/');
$createCSV->init();
$createCSV->create();
$createCSV->output();




?>
