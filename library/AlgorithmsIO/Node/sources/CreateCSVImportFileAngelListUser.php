<?php
/**
 * Creates the Batch Importers Tab CSV file 
 * 
 * Creates 2 files for the batch importer.  One node file and one relationship file
 * 
 * Input to this script is a file with each line as a person and the json output
 * of the API call.
 * 
 * * Testing:
 * php library/AlgorithmsIO/Utilities/CreateCSVImportFileAngelListUser.php
 * 
 * Running full for all ~100k users
 * nohup php library/AlgorithmsIO/Utilities/CreateCSVImportFileAngelListUser.php&
 * 
 */
ini_set('memory_limit','4024M');
include_once('library/AlgorithmsIO/Node/sources/CSVImportBase.php');
include_once('library/AlgorithmsIO/Node/Import/AngelListUser.php');
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
$createCSV->setGraphModel('AngelListUser');
$createCSV->setSourceDataFile("/Users/gkan/Downloads/neo4j-stuff/source_data/angellist_users_080113.txt");
$createCSV->setDatasourceName('angelList');
$createCSV->setOutPutLocation('/Users/gkan/Downloads/');
$createCSV->init();
$createCSV->create();
$createCSV->output();




?>
