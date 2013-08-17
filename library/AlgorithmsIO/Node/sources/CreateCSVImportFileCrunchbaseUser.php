<?php
/**
 * Creates the Batch Importers Tab CSV file 
 * 
 * Creates 2 files for the batch importer.  One node file and one relationship file
 * 
 * Input to this script is a file with each line as a person and the json output
 * of the API call.
 * 
 * The first entry in the input file should have all the fields for the header.
 * 
 * Usage:
 * -best to run on the command line
 * 
 * Testing:
 * php library/AlgorithmsIO/Utilities/CreateCSVImportFileCrunchbaseUser.php
 * 
 * Running full for all ~100k users
 * nohup php library/AlgorithmsIO/Utilities/CreateCSVImportFileCrunchbaseUser.php &
 * 
 * 
 */
ini_set('memory_limit','4024M');
include_once('library/AlgorithmsIO/Node/sources/CSVImportBase.php');
include_once('library/AlgorithmsIO/Node/Import/CrunchbaseUser.php');
ini_set('max_execution_time', 6000);

$createCSV = new CreateCSVImport();
$createCSV->setGraphModel('CrunchbaseUser');
$createCSV->setSourceDataFile("/Users/gkan/Downloads/neo4j-stuff/source_data/crunchbase_users_072612.txt");
$createCSV->setDatasourceName('crunchbase');
$createCSV->setOutPutLocation('/Users/gkan/Downloads/');
$createCSV->init();
$createCSV->create();
$createCSV->output();


?>
