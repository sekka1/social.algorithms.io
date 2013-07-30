<?php
namespace AlgorithmsIO\Node\Import{
    
    include_once('ImportBase.php');
    
    class AngelListUser extends CreateNodeBatchFilesBase{

        private $userArray;
        protected $headerArray;
        private $startingRowNumber;
        private $totalRows;

        private $allNodes;
        private $allRelationships;

        protected $datasource_name = 'angelList';

        public function __construct() {
            $this->startingRowNumber=0;
            $this->totalRows=0;
            $this->allNodes=array();
            $this->allRelationships=array();

            parent::__construct();
        }
        /**
         * 
         * @param array $headers
         */
        public function setHeader($headers){
            $this->headerArray = $headers;
        }
        /**
         * 
         * @param int $row
         */
        public function setStartingRowNumber($row){
            $this->startingRowNumber = $row;
        }
        /**
         * Returns the number of rows in the allNodes array
         * 
         * @return int
         */
        public function getTotalRows(){
            return count($this->allNodes);
        }
        public function add($userArray){
            $this->userArray = $userArray;
            
            if(!isset($this->userArray['person']))
                return $this->allNodes;

            $this->setPersonNodes();
            $this->setRoleNodes();
            $this->setSkillNodes();
        }
        /**
         * Returns an array holding arrays of nodes.  Each node has properties
         * associated to it, depending what the API gave back.
         * 
         * OLD
         * 
         * @return array
         */
        public function getNodes(){

            if(!isset($this->userArray['person']))
                return $this->allNodes;

            $this->setPersonNodes();
            $this->setRoleNodes();
            $this->setSkillNodes();

    //print_r($this->allNodes);
    //print_r($this->allRelationships);
            return $this->allNodes;
        }
        /**
         * Returns an array holding arrays of relationships.  These are all based
         * on mapping of the nodes and where the startRowNumber was set to.
         * 
         * @return array
         */
        public function getRelationships(){
            return $this->allRelationships;
        }
        /**
         * This is specific to this particular API structure.
         * 
         * Calling this will go through the array specified and loop through it for
         * all the properties to be set in the node.
         * 
         * Additional details to this node is also set.
         * 
         * Will take the information in and place it in the $allNodes array
         */
        private function setPersonNodes(){
            $nodeArray = array();

            $nodeArray['node_db_label'] = 'PersonGUID';
            $nodeArray['datasource_name'] = $this->datasource_name;
            $nodeArray['source_uid'] = $this->userArray['source_uid'];

            foreach($this->userArray['person'] as $key=>$val){
                $nodeArray[$key] = $val;
            }
            
            // Fill out array with missing headers
            $this->addNode($nodeArray['source_uid'], json_encode($this->fillOutDataWithAllHeaders($nodeArray)));
        }
        /**
         * This is specific to this particular API structure.
         * 
         * Calling this will go through the array specified and loop through it for
         * all the properties to be set in the node.
         * 
         * Additional details to this node is also set.
         * 
         * Will take the information in and place it in the $allNodes array
         * 
         * Will also populate the relationship to the main node
         */
        private function setRoleNodes(){
            foreach($this->userArray['roles'] as $item){
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'AlRoles';
                    $temp[$key] = $val;
                    //array_push($this->allNodes, $temp);
                    //$this->addRelationshipToMainNode('HAS_ROLE');

                    $this->addNode($temp['role_id'], json_encode($this->fillOutDataWithAllHeaders($temp)));
                    $this->addReltionship($this->userArray['source_uid'], $temp['role_id'], 'HAS_ROLE');
                }
            }
        }
        /**
         * This is specific to this particular API structure.
         * 
         * Calling this will go through the array specified and loop through it for
         * all the properties to be set in the node.
         * 
         * Additional details to this node is also set.
         * 
         * Will take the information in and place it in the $allNodes array
         * 
         * Will also populate the relationship to the main node
         */
        private function setSkillNodes(){
            foreach($this->userArray['skills'] as $item){
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'AlSkill';
                    $temp[$key] = $val;
                    //array_push($this->allNodes, $temp);
                    //$this->addRelationshipToMainNode('HAS_SKILL');
                    
                    $this->addNode($temp['id'], json_encode($this->fillOutDataWithAllHeaders($temp)));
                    $this->addReltionship($this->userArray['source_uid'], $temp['id'], 'HAS_SKILL');
                }
            }        
        }
        /**
         * Populates the $allRelationships array with the main node to the current
         * node with a name
         * 
         * FIXME: this is not too flexible.  should find a way to do this more dynamically
         * 
         * @param string $relationship_name
         */
        private function addRelationshipToMainNode($relationship_name){
            $temp['start'] = $this->startingRowNumber;
            $temp['end'] = $this->startingRowNumber + count($this->allNodes)-1;
            $temp['type'] = $relationship_name;
            array_push($this->allRelationships, $temp);
        }

    }
}
?>
