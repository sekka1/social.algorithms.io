<?php

/*
 * Testing importing Crunchbase Users
 */
ini_set('memory_limit','4024M');
include_once('library/AlgorithmsIO/Node/sources/CSVImportBase.php');
include_once('library/AlgorithmsIO/Utilities/MySQL.php');
include_once('library/AlgorithmsIO/Node/Import/CrunchbaseUser.php');

class CreateCSVImportFileCrunchbaseUserTest extends PHPUnit_Framework_TestCase{
    
    private $mysql;
    private $mysqlHost;
    private $mysqlPort;
    private $mysqlUser;
    private $mysqlPassword;
    private $dbname;
    private $table_node;
    private $table_relationship;
    
    private $fileOutputLocation;
    
    public function setUp(){ 
        
        // Setup DB
        $this->mysqlHost = 'localhost';
        $this->mysqlPort = 3306;
        $this->mysqlUser = 'root';
        $this->mysqlPassword = 'sunshine';
        $this->dbname = 'testNodeBatchImport';
        $this->table_node = 'zz_node_db_import_table_not_akkadian_stuff';
        $this->table_relationship = 'zz_relationship_db_import_table_not_akkadian_stuff';
        
        $this->fileOutputLocation = '/Users/gkan/Downloads/';

        // Get data from MySQL
        $this->mysql = new \AlgorithmsIO\Utilities\MySQL();
        $this->mysql->setConnection($this->mysqlHost, $this->mysqlPort, $this->mysqlUser, $this->mysqlPassword);
        $this->mysql->setDatabaseName($this->dbname);
        $this->mysql->connect();
        
    }
    public function tearDown(){ 
        
        // Clean up DB.  Delete all rows
        $sql = "DELETE FROM ".$this->table_node." WHERE rowNumber>0";
        $result = $this->mysql->getConnection()->query($sql);
        
        $sql = "DELETE FROM ".$this->table_relationship." WHERE start>0";
        $result = $this->mysql->getConnection()->query($sql);
    }
    
    public function testImporting(){
        
        $createCSV = new CreateCSVImport();
        $createCSV->setDBConnection($this->mysqlHost, $this->mysqlPort, $this->mysqlUser, $this->mysqlPassword, $this->dbname, $this->table_node, $this->table_relationship);
        $createCSV->setGraphModel('CrunchbaseUser');
        $createCSV->setSourceDataFile("/Users/gkan/Downloads/neo4j-stuff/source_data/crunchbase_users_072612.txt");
        $createCSV->setDatasourceName('crunchbase');
        $createCSV->setOutPutLocation($this->fileOutputLocation);
        $createCSV->init();
        $createCSV->create();
        $createCSV->output();
        
        
        // Get DB Count
        $sql = "select * from zz_node_db_import_table_not_akkadian_stuff";
        $result = $this->mysql->getConnection()->query($sql);
        
        // Count output file
        $numberOfLines = $this->countNumberOfRowsInFile();
        
        // minus 1 b/c there is a header line in the output file
        if(($numberOfLines-1)==$result->num_rows)
            $this->assertTrue(true);
        else
            $this->assertTrue(false, 'Failed: Line number and num_rows did not match');
        
        
        // Checking Relationship file
        $lastLineArray = $this->getLastRelationshipLine();
        
        // Check if there are 3 columns in the relationship file
        if(count($lastLineArray)==3)
            $this->assertTrue(true);
        else
            $this->assertTrue(false, 'Failed: number of tabbed columns in the relationship file is not 3');
        
        // Check that the start column rowNumber is <= to the total rowNumber
        if($lastLineArray[0] <= $result->num_rows)
            $this->assertTrue(true);
        else
            $this->assertTrue(false, 'Failed: relationship, start is higher than total row number');
        
        // Check that the end column rowNumber is <= to the total rowNumber
        if($lastLineArray[1] <= $result->num_rows)
            $this->assertTrue(true);
        else
            $this->assertTrue(false, 'Failed: relationship, end is higher than total row number');
        
    }
    /**
     * Gets the count of number of lines in the output file
     * 
     * @return int
     */
    private function countNumberOfRowsInFile(){
        $fileLocation = $this->fileOutputLocation.'_node_CrunchbaseUser.csv';
        $lineNumber = 0;
        $handle = @fopen($fileLocation, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 1096)) !== false) {
                $lineNumber++;
            }
            fclose($handle);
        }
        return $lineNumber;
    }
    /**
     * Returns the last line "end" number in the relationship file.  This number
     * should be lower than the total number of lines.
     * 
     * @return array
     */
    private function getLastRelationshipLine(){
        $fileLocation = $this->fileOutputLocation.'_relationship_CrunchbaseUser.csv';
        $finalLine = '';
        $handle = @fopen($fileLocation, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 8096)) !== false) {
                $finalLine = $buffer;
            }
            fclose($handle);
        }
        
        // Parse final line
        $split = preg_split('/\t/', $finalLine);
        
        return $split;
    }
}

?>
