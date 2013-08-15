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
        //public function getTotalRows(){
        //    return count($this->allNodes);
        //}
        public function add($userArray){
            $this->userArray = $userArray;
            
            if(!isset($this->userArray['person']))
                return $this->allNodes;

            $nodeId = $this->setPersonNodes();
            $this->setRoleNodes($nodeId);
            $this->setSkillNodes($nodeId);
        }
        /**
         * Returns an array holding arrays of nodes.  Each node has properties
         * associated to it, depending what the API gave back.
         * 
         * OLD
         * 
         * @return array
         */
        /*
        public function getNodes(){

            if(!isset($this->userArray['person']))
                return $this->allNodes;

            $nodeId = $this->setPersonNodes();
            $this->setRoleNodes($nodeId);
            $this->setSkillNodes($nodeId);

            return $this->allNodes;
        }
         * 
         */
        /**
         * Returns an array holding arrays of relationships.  These are all based
         * on mapping of the nodes and where the startRowNumber was set to.
         * 
         * @return array
         */
        //public function getRelationships(){
        //    return $this->allRelationships;
        //}
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
         * @return int ID of the node that was created for the Person
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
            $guid = 'user_'.$nodeArray['source_uid'];
            $nodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($nodeArray)));
            
            return $nodeId;
        }
        /**
         * Roles Nodes - aka employment
         * 
         * Structure:
         * PersonGUID-[:HAS_EMPLOYMENT]->Employment:employmentMain-[:HAS_EMPLOYMENT_FIRM]->EmploymentFirm:employmentFirm
         *                                                        -[:HAS_EMPLOYMENT_ROLE]->EmploymentRole:employmentRole
         * 
         * @param int $pseronNodeId ID of the main person's node
         */
        private function setRoleNodes($pseronNodeId){
            $counter = 0; // For unique guids with un-named nodes
            
            foreach($this->userArray['roles'] as $item){
                
                $temp = array();
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'AlRoles';
                    $temp[$key] = $val;
                }
                
                // Add Main Employment Node
                $guid = $this->userArray['source_uid'].'_employment_'.$counter;
                $counter++;
                $employmentNodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($pseronNodeId, $employmentNodeId, 'HAS_EMPLOYMENT');
                
                // Add Employment Firm Node
                $firm = array('node_db_label'=>'EmploymentFirm','value'=>$temp['startup_id'], 'startup_name'=>$temp['startup_name']);
                $firmGUID = 'firm_'.$temp['startup_id'];
                $firmNodeId = $this->addNode($firmGUID, json_encode($this->fillOutDataWithAllHeaders($firm)));
                $this->addReltionship($employmentNodeId, $firmNodeId, 'HAS_EMPLOYMENT_FIRM');
                
                // Add Employment Role node
                $role = array('node_db_label'=>'EmploymentRole','value'=>$temp['role_id'], 'role'=>$temp['role']);
                $roleGUID = 'role_'.$temp['role_id'];
                $roleNodeId = $this->addNode($roleGUID, json_encode($this->fillOutDataWithAllHeaders($role)));
                $this->addReltionship($employmentNodeId, $roleNodeId, 'HAS_EMPLOYMENT_ROLE');
            }
        }
        /**
         * Skills Nodes
         * 
         * Structure:
         * PersonGUID-[:HAS_SKILL]->Skill:skill
         *
         * 
         * @param int $pseronNodeId ID of the main person's node
         */
        private function setSkillNodes($pseronNodeId){
            
            foreach($this->userArray['skills'] as $item){
                
                $temp = array();
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'Skill';
                    $temp[$key] = $val;
                }
                
                // Add Skills Node
                $guid = 'skill_'.$temp['id'];
                $skillNodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($pseronNodeId, $skillNodeId, 'HAS_SKILL');
            }        
        }

    }
}
?>
