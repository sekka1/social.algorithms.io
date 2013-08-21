<?php
namespace AlgorithmsIO\Node\sources{
    /**
     * The output file can be used to mass insert nodes into Neo4j using the 
     * batch-import
     * 
     * Takes a datasource file and imports it into the DB.  Which then it outputs
     * a CSV file in the Neo4j batch-import format.
     *
     */

    class CSVImportBase{

        private $fileLocation;
        private $headers;
        private $didGetHeaders;

        private $importNodes;

        private $outputLocation;

        private $outputNodeFile;
        private $outputRelationshipFile;
        private $outputNodeFileHandle;
        private $outputRelationshipFileHandle;

        private $datasource_name; // Name of the datasource in the DB

        private $graphModel;

        // DB Parameters
        private $mysql;
        private $mysqlHost;
        private $mysqlUser;
        private $mysqlPassword;
        private $mysqlPort;
        private $dbName;
        private $tableNameNode;
        private $tableNameRels;

        public function __construct(){        
            $this->didGetHeaders = false;
            $this->headers = array();
        }
        /**
         * Sets the name of the datasource in the DB
         * 
         * @param string $name
         */
        public function setDatasourceName($name){
            $this->datasource_name = $name;
        }
        public function setOutPutLocation($location){
            $this->outputLocation = $location;
        }
        public function setSourceDataFile($file){
            $this->fileLocation = $file;
        }
        public function setGraphModel($model){
            $this->graphModel = $model;
        }
        public function setDBConnection($mysqlHost, $mysqlPort, $mysqlUser, $mysqlPassword, $dbname, $table_node, $table_relationship){
            $this->mysqlHost = $mysqlHost;
            $this->mysqlPort = $mysqlPort;
            $this->mysqlUser = $mysqlUser;
            $this->mysqlPassword = $mysqlPassword;
            $this->dbName = $dbname;
            $this->tableNameNode = $table_node;
            $this->tableNameRels = $table_relationship;
        }
        public function init(){

            // Start up the model for the import. This is graph structure specific.
            // For example, Crunchbase User vs Angel List Users or Even the Company
            $model = '\AlgorithmsIO\Node\Import\\'.$this->graphModel;
            //$this->importNodes = new \AlgorithmsIO\Node\Import\CrunchbaseUser();
            $this->importNodes = new $model();
            $this->importNodes->setDBConnection($this->mysqlHost, $this->mysqlPort, $this->mysqlUser, $this->mysqlPassword, $this->dbName, $this->tableNameNode, $this->tableNameRels);

            $this->outputNodeFileHandle = fopen($this->outputLocation.'_node_'.$this->graphModel.'.csv', 'w');
            $this->outputRelationshipFileHandle = fopen($this->outputLocation.'_relationship_'.$this->graphModel.'.csv', 'w');
        }
        /**
         * Initiates creating the AngelListUser object which holds each users information
         * which can be called to get the info.
         */
        public function create(){

            $n=0;

            $handle = @fopen($this->fileLocation, "r");
            if ($handle) {

                while (($buffer = fgets($handle, 8096)) !== false) {
                    //echo $buffer;
                    $line = json_decode($buffer, true);
    //print_r($line);

                    if(! $this->didGetHeaders){
                        $this->addAdditionalHeaders();
                        $this->setHeaders($line);
                        $this->didGetHeaders=true;
                        $this->importNodes->setHeader($this->headers);
                    }

    //print_r($this->headers);
                    //if($n>=0)
                        $this->importNodes->add($line);

                    //if($n==2)
                    //    break;
                    $n++;
                }
                if (!feof($handle)) {
                    echo "Error: unexpected fgets() fail\n";
                }
                fclose($handle);
            }
        }
        /**
         * Goes through the array and retrieves all the keys to make one list which
         * will be the header values.
         * 
         * All header values are unique
         * 
         * @param type $data
         */
        private function setHeaders($data){
            foreach($data as $key=>$val){
                if(is_array($val))
                    $this->setHeaders ($val);
                else{
                    if(! in_array($key, $this->headers))
                        array_push($this->headers, $key);
                }
            }
        }
        /**
         * These are headers we are adding in to augment what is not retrieved from
         * the api and adding the label so that we can upgrade the db to Neo4j 2.x 
         * and then apply these labels to the nodes.
         * 
         * And basically additional info or param names that we want to add in that is
         * not in the data arrays coming in.
         */
        private function addAdditionalHeaders(){
            array_push($this->headers, 'node_db_label');
            array_push($this->headers, 'datasource_name');
            array_push($this->headers, 'value');
        }
        /**
         * Foreach person that was taking in and produced, it will output the nodes
         * and relationships in the 2 files.
         */
        public function output(){

            $allNodes = $this->importNodes->getAllNodes($this->datasource_name);
            $allRels = $this->importNodes->getAllRelationships($this->datasource_name);

            $this->outputNodeHeaders();
            $this->outputRelHeaders();

            // Output Nodes
            foreach($allNodes as $anItem){
                $this->outputNodeToFile(json_decode($anItem['json'], true));
            }
            // Output Relationships
            foreach($allRels as $anItem){
                $this->outputRelationshipToFile($anItem);
            }

        }
        /**
         * Output the node.csv file headers.  Based on all the fields that were passed
         * in and additional headers added.
         */
        private function outputNodeHeaders(){
            fputcsv($this->outputNodeFileHandle, $this->headers, "\t");
        }
        /**
         * Output the relationship headers.
         */
        private function outputRelHeaders(){
            $headers = array('start', 'end', 'type');
            fputcsv($this->outputRelationshipFileHandle, $headers, "\t");
        }  
        /**
         * Outputs the array to the file in tab delimited format.  
         * 
         * Array format: 
         * same as header
         * 
         * @param array $array
         */
        private function outputNodeToFile($array){
            fputcsv($this->outputNodeFileHandle, $array, "\t");
        }
        /**
         * Output the relationships of a person to a file.  
         * 
         * Array format:
         * start, end, type
         * 
         * @param array $array
         */
        private function outputRelationshipToFile($array){
            fputcsv($this->outputRelationshipFileHandle, $array, "\t");
        }
    }
}
?>
