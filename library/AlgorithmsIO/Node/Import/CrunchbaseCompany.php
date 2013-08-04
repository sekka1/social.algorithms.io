<?php
namespace AlgorithmsIO\Node\Import{
    
    include_once('ImportBase.php');
    
    class CrunchbaseCompany extends CreateNodeBatchFilesBase{

        private $userArray; // Data array
        protected $headerArray;
        private $startingRowNumber;
        private $totalRows;

        private $allNodes;
        private $allRelationships;

        protected $datasource_name = 'crunchbase';

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
         * Adds all the nodes into the db with relationships
         * 
         * @param array $userArray
         * @return type
         */
        public function add($userArray){
            $this->userArray = $userArray;
            
            if(!isset($this->userArray['company']))
                return $this->allNodes;

            $nodeId = $this->setEmploymentFirmNodes();
            $this->setFundingNodes($nodeId);
        }
        
        
        
        
        
        /**
         * This is the main node.  This node might already exist from the Person
         * nodes insertion.  A person might have already worked at this which would
         * of created the "EmploymentFirm" node.
         * 
         * This is going to look for the node, and if it exist it will update it.
         * This one should have more information.
         * 
         * @return int -node id of the firm's node
         */
        private function setEmploymentFirmNodes(){
            $nodeArray = array();

            $nodeArray['node_db_label'] = 'EmploymentFirm';
            $nodeArray['datasource_name'] = $this->datasource_name;
            $nodeArray['source_uid'] = $this->userArray['source_uid'];
            
            foreach($this->userArray['company'] as $key=>$val){
                $nodeArray[$key] = $val;
            }
            foreach($this->userArray['data_meta_data'] as $key=>$val){
                $nodeArray[$key] = $val;
            }
          
            $nodeId = $this->updateNode($nodeArray['source_uid'], json_encode($this->fillOutDataWithAllHeaders($nodeArray)));
            
            return $nodeId;
        }
        
        
        
        
        /**
         * Funding rounds nodes
         * 
         * Structure:
         * EmploymentFirm-[HAS_FUNDING]->Round-[HAS_INVESTOR]->Investor
         * 
         * @param int $firmNodeId Node id/row id of the EmploymentFirm's node
         * 
         */
        private function setFundingNodes($firmNodeId){
            $counter = 0; // For unique guids
            
            foreach($this->userArray['funding'] as $item){
                
                // Main Node
                $temp=array();
                foreach($item as $key=>$val){
                    if($key!='investors'){
                        $temp[$key] = $val;
                    }
                }
                
                // Funding node has no guid associated with it.  Making one
                $guid = $this->userArray['source_uid'].'_funding_'.$counter;
                $temp['node_db_label'] = 'Funding';
                $counter++;
                $fundingNodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($firmNodeId, $fundingNodeId, 'HAS_FUNDING');
                
                $this->setInvestors($fundingNodeId, $item['investors']);
            }
        }
        /**
         * This adds the investor nodes to the Funding node
         * 
         * @param int $parentGUID Fundin nodes Id/row
         * @param array $investors array of the investor's attributes/properties
         */
        private function setInvestors($parentGUID, $investors){
            $temp=array();
            foreach($investors as $item){
                foreach($item as $key=>$val){
                    $temp[$key] = $val;
                }
                $guid = $temp['permalink'];
                $temp['node_db_label'] = 'Investors';
                $temp['value'] = $temp['permalink'];
                $investorNodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($parentGUID, $investorNodeId, 'HAS_INVESTOR');
            }
        }
        
        
        
        
        

        
        
    }
}
?>
