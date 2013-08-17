<?php
namespace AlgorithmsIO\Node\Import{
    
    include_once('library/AlgorithmsIO/Utilities/MySQL.php');
    
    class CreateNodeBatchFilesBase{

        private $rowNumber;
        
        private $mysql;
        private $mysqlHost;
        private $mysqlUser;
        private $mysqlPassword;
        private $mysqlPort;
        private $dbName;
        
        private $tableNameNode = 'zz_node_db_import_table_not_akkadian_stuff';
        private $tableNameRels = 'zz_relationship_db_import_table_not_akkadian_stuff';

        public function __construct() {

        }
        public function setDBConnection($mysqlHost=null, $mysqlPort=null, $mysqlUser=null, $mysqlPassword=null, $dbname=null, $table_node, $table_relationship){
            $this->mysqlHost = $mysqlHost;
            $this->mysqlPort = $mysqlPort;
            $this->mysqlUser = $mysqlUser;
            $this->mysqlPassword = $mysqlPassword;
            $this->dbName = $dbname;
            $this->tableNameNode = $table_node;
            $this->tableNameRels = $table_relationship;
            
            // Get data from MySQL
            $this->mysql = new \AlgorithmsIO\Utilities\MySQL();
            $this->mysql->setConnection($this->mysqlHost, $this->mysqlPort, $this->mysqlUser, $this->mysqlPassword);
            $this->mysql->setDatabaseName($dbname);
            $this->mysql->connect();
            
            $this->setRowNumber();
        }
        /**
         * Sets $rowNumber to the highest row number + 1.  This would allow for
         * re-continuing import and adding to an existing dataset
         */
        public function setRowNumber(){
            $sql = "select rowNumber from ".$this->tableNameNode." order by rowNumber desc limit 1";
            $result = $this->mysql->getConnection()->query($sql);

            if($result->num_rows==0)
                $this->rowNumber = 1;
            else{
                $obj = $result->fetch_object();
                $this->rowNumber = $obj->rowNumber+1;
            }
        }
        /**
         * 
         * @param string $guid
         * @param json $json
         * @return int last rowNumber which is the last row that was added
         */
        protected function addNode($guid, $json){
            
            // Check if GUID is in table already
            $inTableRowNumber = $this->isNodeInTable($guid);
            if($inTableRowNumber>0)
                return $inTableRowNumber;
            
            $sql = "INSERT INTO ".$this->tableNameNode." VALUES('".$this->datasource_name."',".$this->rowNumber.", '".$guid."', '".$this->mysql->real_escape_string($json)."')";
            
            if($result = $this->mysql->getConnection()->query($sql)){
                $lastRowNumber = $this->rowNumber;
                $this->rowNumber++;           
                return $lastRowNumber;
            }else{
                echo $sql;
                exit;
                echo "failed insert";
                return false;
            }
        }
        /**
         * Will replace the node's json information with the new one if this
         * guid exist
         * 
         * @param string $guid
         * @param json $json
         * @return int the rowNumber of the node that was updated
         */
        protected function updateNode($guid, $json){

            // Check if GUID is in table already
            $inTableRowNumber = $this->isNodeInTable($guid);
            if($inTableRowNumber>0){
            
                $sql = "update ".$this->tableNameNode." set json='".$this->mysql->real_escape_string($json)."' where datasource_name='".$this->datasource_name."' AND nodeGUID='".$guid."'";

                if($result = $this->mysql->getConnection()->query($sql)){
                    return $inTableRowNumber;
                }else{
                    echo $sql;
                    exit;
                    echo "failed insert";
                    return false;
                }
            }else{
                // Add node
                return $this->addNode($guid, $json);
            }
        }
        /**
         * 
         * @param int $end_rowNumber
         * @param int $end_rowNumber
         * @param string $relationship_name
         * @return bool
         */
        protected function addReltionship($start_rowNumber, $end_rowNumber, $relationship_name){
            
            if($this->relationshipExist($start_rowNumber, $end_rowNumber, $relationship_name))
                return true;
            
            $sql = "INSERT INTO ".$this->tableNameRels." VALUES('".$this->datasource_name."', ".$start_rowNumber.",".$end_rowNumber.",'".$this->mysql->real_escape_string($relationship_name)."')";
            $result = $this->mysql->getConnection()->query($sql);            
            return true;
        }
        /**
         * Checks whether the guid passed in is in the table already
         * 
         * @param type $guid
         * @return int -either the rowNumber if it was found or -1 if not found
         */
        private function isNodeInTable($guid){         
            $sql = "SELECT * FROM ".$this->tableNameNode." WHERE datasource_name='".$this->datasource_name."' AND nodeGUID='".$guid."'";
         
            if($result = $this->mysql->getConnection()->query($sql)){
                if($result->num_rows>0){
                    $obj = $result->fetch_object();
                    return $obj->rowNumber;
                }
                else
                    return -1;
            }else{
                echo "failed sql";
                return false;
            }
        }
        /**
         * Retrieves the GUID's rowNumber
         * 
         */
        private function getRowNumberByGUID($guid){            
            $sql = "select rowNumber from ".$this->tableNameNode." where datasource_name='".$this->datasource_name."' AND nodeGUID='".$guid."'";
            $result = $this->mysql->getConnection()->query($sql);
            
            if($result = $this->mysql->getConnection()->query($sql)){
                if($result->num_rows==1){
                    $obj = $result->fetch_object();
                    return $obj->rowNumber;
                }else{
                    return -1;
                }
            }else{
                return -1;
            }
        }
        /**
         * Checks if a relationship exist already or not
         * 
         * @param int $start_rowNumber
         * @param int $end_rowNumber
         * @param string $relationship_name
         * @return bool
         */
        private function relationshipExist($start_rowNumber, $end_rowNumber, $relationship_name){            
            $sql = "SELECT * FROM ".$this->tableNameRels." WHERE datasource_name='".$this->datasource_name."' AND start=".$start_rowNumber." AND end=".$end_rowNumber." AND type='".$relationship_name."'";
            $result = $this->mysql->getConnection()->query($sql);

            if($result = $this->mysql->getConnection()->query($sql)){
                if($result->num_rows==1){
                    $obj = $result->fetch_object();
                    return true;
                }else{
                    return false;
                }
            }else{
                return -1;
            }
        }
        /**
         * Fills out the node array with all the header value even if it is not
         * in this node.
         * 
         * It will also put the values in the right order on the way the header is
         * 
         * @param array $nodeArray
         * @return array
         */
        protected function fillOutDataWithAllHeaders($nodeArray){
            // Fill out array with missing headers
            $temp=array();
            foreach($this->headerArray as $val){
                if(array_key_exists($val, $nodeArray))
                    $temp[$val] = $nodeArray[$val];
                else
                    $temp[$val] = '';
            }
            return $temp;
        }
        /**
         * Returns all the nodes for a given datasource name
         * 
         * @param string $datasource_name
         * @return array
         */
        public function getAllNodes($datasource_name){
            $output = array();
            $sql = "SELECT * FROM ".$this->tableNameNode." WHERE datasource_name='".$datasource_name."' ORDER BY rowNumber asc";
            if ($result = $this->mysql->getConnection()->query($sql)) {
                while($obj = $result->fetch_object()){
                    $temp['rowNumber'] = $obj->rowNumber;
                    $temp['nodeGUID'] = $obj->nodeGUID;
                    $temp['json'] = $obj->json;
                    array_push($output, $temp);
                }
            } 
            return $output;
        }
        /**
         * Returns all the relationship for a given datasource name
         * 
         * @param string $datasource_name
         * @return array
         */
        public function getAllRelationships($datasource_name){
            $output = array();
            $sql = "SELECT * FROM ".$this->tableNameRels." WHERE datasource_name='".$datasource_name."'";
            if ($result = $this->mysql->getConnection()->query($sql)) {
                while($obj = $result->fetch_object()){
                    $temp['start'] = $obj->start;
                    $temp['end'] = $obj->end;
                    $temp['type'] = $obj->type;
                    array_push($output, $temp);
                }
            }
            return $output;
        }
        /**
         * Checks if a value is blank.  If so, it lets the you set it to whatever
         * you want else it just returns the original value;
         * 
         * @param string $value
         * @param string $setTo
         * @return string
         */
        protected function setBlankValue($value, $setTo){
            if($value=='')
                $value = $setTo;
            return $value;
        }
    }
}
?>
