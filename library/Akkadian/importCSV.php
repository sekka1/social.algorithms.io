<?php

/*
 * Imports a CSV file into MySQL.
 * 
 * If the file has a header and the header names matches the column names in 
 * the db.  You can just input it in and it will import everything in the
 * right place.
 * 
 */

$importCSV = new ImportCSV();
$importCSV->setCSVFile('/tmp/RatingsH22012.csv');
$importCSV->setMySQLConnection('localhost', 'akkadian', 'akkadian1298');
$importCSV->setDatabaseName('akkadian');
$importCSV->setTablName('ratings');
$importCSV->mySQLConnect();
$importCSV->useHeaderAsFieldNames();
//$importCSV->stopOnError();
$importCSV->import();



class ImportCSV{
    
    private $mysqlConnection;
    private $mysqlHost;
    private $mysqlUser;
    private $mysqlPassword;
    private $databaseName;
    private $tableName;
    
    
    private $csvFilePath;
    
    private $useHeaderAsFieldNames = false;
    
    private $fileHandle = null;
    private $currentImportingRow;
    
    private $headerArray = array();
    
    private $stopOnError = false;
    
    public function __construct() {
        $this->currentImportingRow = 0;
    }
    /**
     * 
     * @param string $csvFile - full path to the file including file name
     */
    public function setCSVFile($csvFile){
        $this->csvFilePath = $csvFile;
    }
    /**
     * 
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function setMySQLConnection($host, $user, $password){
        $this->mysqlHost = $host;
        $this->mysqlUser = $user;
        $this->mysqlPassword = $password;
    }
    /**
     * 
     * @param string $name
     */
    public function setDatabaseName($name){
        $this->databaseName = $name;
    }
    /**
     * 
     * @param string $name
     */
    public function setTablName($name){
        $this->tableName = $name;
    }
    /**
     * Use the first row as header names.  Dont import this row.
     */
    public function useHeaderAsFieldNames(){
        $this->useHeaderAsFieldNames = true;
    }
    /**
     * Make MySQL connection
     */
    public function mySQLConnect(){
        $this->mysqlConnection = new mysqli($this->mysqlHost, $this->mysqlUser, $this->mysqlPassword, $this->databaseName);
        if (!$this->mysqlConnection) {
            die('Could not connect: ' . mysql_error());
        }
    }
    public function stopOnError(){
        $this->stopOnError = true;
    }
    /**
     * Starts the import process
     */
    public function import(){
        $this->openFile();
        $this->startImport();
        $this->closeFile();
        $this->closeMySQLConnection();
    }
    /**
     * 
     */
    private function openFile(){
        if (($handle = fopen($this->csvFilePath, "r")) !== FALSE) {
            $this->fileHandle = $handle;
        }else{
            die('Could not open file: '.$this->csvFilePath);
        }
    }
    /**
     * This will loop throught he file to pull out the header and the data and
     * insert it into the DB.
     */
    private function startImport(){
        while (($data = fgetcsv($this->fileHandle, 0, ",")) !== FALSE) {
            
            // Save Headers in the first row
            if($this->currentImportingRow==0 && $this->useHeaderAsFieldNames){
                $this->headerArray = $data;
            }else{
                // Process data rows
                for($n=0;$n<count($data);$n++){
                    $sql = 'INSERT INTO '.$this->tableName.' ';
                    $sql .= $this->insertSQLHeaderNames();
                    $sql .= 'VALUES ';
                    $sql .= $this->insertSQLDataValues($data);
                    
                    $status = $this->mysqlConnection->query($sql);
                    if(!$status){
                        echo "Failed On: ".$sql."\n";
                        if($this->stopOnError)
                            exit;
                    }
                }
                
            }
            
            $this->currentImportingRow++;
        }
    }
    /**
     * 
     */
    private function closeFile(){
        fclose($this->fileHandle);
    }
    /**
     * 
     */
    private function closeMySQLConnection(){
        mysqli_close($this->mysqlConnection);
    }
    /**
     * Returns the name field string for the INSERT statement into the DB
     * 
     * @return string
     */
    private function insertSQLHeaderNames(){
        $string = ' (';
        foreach($this->headerArray as $anItem){
            $string .= $anItem.', ';
        }
        $string = preg_replace('/,\s+$/', '', $string);
        $string .= ') ';
        return $string;
    }
    /**
     * Returns the data string for the INSERT statement into the DB
     * 
     * @param array $data
     * @return string
     */
    private function insertSQLDataValues($data){
        $string = ' (';
        foreach($data as $anItem){
            if(!is_numeric($anItem))
                $string .= "'".$this->mysqlConnection->real_escape_string($anItem)."', ";
            else
                $string .= $anItem.', ';
        }
        $string = preg_replace('/,\s+$/', '', $string);
        $string .= ') ';
        return $string;    
    }
}



?>
