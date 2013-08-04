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
        
        private $tableNameNode = 'zz_node_db_import_table_not_akkadian_stuff';
        private $tableNameRels = 'zz_relationship_db_import_table_not_akkadian_stuff';

        public function __construct() {
            
            $this->mysqlHost = 'localhost';
            $this->mysqlPort = 3306;
            $this->mysqlUser = 'root';//'akkadian';
            $this->mysqlPassword = 'sunshine';//'akkadian1298';
            
            // Get data from MySQL
            $this->mysql = new \AlgorithmsIO\Utilities\MySQL();
            $this->mysql->setConnection($this->mysqlHost, $this->mysqlPort, $this->mysqlUser, $this->mysqlPassword);
            $this->mysql->setDatabaseName('akkadian');
            $this->mysql->connect();
            //$mySQLConnection = $this->mysql->getConnection();
            
            $this->setRowNumber();
        }
        /**
         * Sets $rowNumber to the highest row number + 1.  This would allow for
         * re-continuing import and adding to an existing dataset
         */
        public function setRowNumber(){
            $sql = "select rowNumber from ".$this->tableNameNode." where datasource_name='".$this->datasource_name."' order by rowNumber desc limit 1";
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
            if($this->isNodeInTable($guid))
                return true;
            
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
            if($this->isNodeInTable($guid)){
            
                $sql = "update ".$this->tableNameNode." set json='".$this->mysql->real_escape_string($json)."' where datasource_name='".$this->datasource_name."' AND nodeGUID='".$guid."'";

                if($result = $this->mysql->getConnection()->query($sql)){
                    $this->rowNumber++;
                    $obj = $result->fetch_object();
                    return $obj->rowNumber;
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
         * @param type $start_guid
         * @param type $end_guid
         * @param type $relationship_name
         * @return bool
         */
        protected function addReltionship($start_guid, $end_guid, $relationship_name){
            
            if($this->relationshipExist($start_guid, $end_guid, $relationship_name))
                return true;
            
            $sql = "INSERT INTO ".$this->tableNameRels." VALUES('".$this->datasource_name."', ".$this->getRowNumberByGUID($start_guid).",".$this->getRowNumberByGUID($end_guid).",'".$this->mysql->real_escape_string($relationship_name)."')";
            $result = $this->mysql->getConnection()->query($sql);            
            return true;
        }
        /**
         * Checks whether the guid passed in is in the table already
         * 
         * @param type $guid
         * @return bool
         */
        private function isNodeInTable($guid){         
            $sql = "SELECT * FROM ".$this->tableNameNode." WHERE datasource_name='".$this->datasource_name."' AND nodeGUID='".$guid."'";
         
            if($result = $this->mysql->getConnection()->query($sql)){
                if($result->num_rows>0){
                    return true;
                }
                else
                    return false;
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
         * @param int $start_guid
         * @param int $end_guid
         * @param string $relationship_name
         * @return bool
         */
        private function relationshipExist($start_guid, $end_guid, $relationship_name){            
            $sql = "SELECT * FROM zz_relationship_db_import_table_not_akkadian_stuff WHERE datasource_name='".$this->datasource_name."' AND start=".$this->getRowNumberByGUID($start_guid)." AND end=".$this->getRowNumberByGUID($end_guid)." AND type='".$relationship_name."'";
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
    }
}
?>
