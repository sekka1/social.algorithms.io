<?php
namespace AlgorithmsIO\Node\Import{
    
    include_once('ImportBase.php');
    
    class AngelListStartup extends CreateNodeBatchFilesBase{

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
            
            if(!isset($this->userArray['company']))
                return $this->allNodes;

            $nodeId = $this->setCompanyNodes();
            $this->setMarketNodes($nodeId);
            $this->setLocationNodes($nodeId);
            $this->setCompanyTypeNodes($nodeId);
            $this->setFundRaisingNode($nodeId);
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
         * @return int ID of the node that was created for the Person
         */
        private function setCompanyNodes(){
            $nodeArray = array();

            $nodeArray['node_db_label'] = 'EmploymentFirm';
            $nodeArray['datasource_name'] = $this->datasource_name;
            $nodeArray['source_uid'] = $this->userArray['source_uid'];

            foreach($this->userArray['company'] as $key=>$val){
                $nodeArray[$key] = $val;
            }
            
            // Fill out array with missing headers
            $guid = 'firm_'.$nodeArray['source_uid'];
            $nodeId = $this->updateNode($guid, json_encode($this->fillOutDataWithAllHeaders($nodeArray)));
            
            return $nodeId;
        }
        /**
         * Structure:
         * EmploymentFirm-[:HAS_MARKET]->Market
         * 
         * @param int $mainNodeId main node that this node is connected to
         */
        private function setMarketNodes($mainNodeId){
            
            foreach($this->userArray['markets'] as $item){
                $temp = array();
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'Market';
                    $temp[$key] = $val;
                }
                $guid = 'market_'.$temp['id'];
                $nodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($mainNodeId, $nodeId, 'HAS_MARKET');
            }
        }
        /**
         * Structure:
         * EmploymentFirm-[:HAS_LOCATION]->Location
         * 
         * @param int $mainNodeId main node that this node is connected to
         */
        private function setLocationNodes($mainNodeId){
            
            foreach($this->userArray['locations'] as $item){
                $temp = array();
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'Location';
                    $temp[$key] = $val;
                }
                $guid = 'location_'.$temp['id'];
                $nodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($mainNodeId, $nodeId, 'HAS_LOCATION');
            }
        }
        /**
         * Structure:
         * EmploymentFirm-[:HAS_COMPANY_TYPE]->CompanyType
         * 
         * @param int $mainNodeId main node that this node is connected to
         */
        private function setCompanyTypeNodes($mainNodeId){
            
            foreach($this->userArray['company_type'] as $item){
                $temp = array();
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'CompanyType';
                    $temp[$key] = $val;
                }
                $guid = 'company_type_'.$temp['id'];
                $nodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($mainNodeId, $nodeId, 'HAS_COMPANY_TYPE');
            }
        }
        /**
         * Structure:
         * EmploymentFirm-[:HAS_FUNDRAISING]->FundRaising
         * 
         * Only add this node if it has values in it
         * 
         * @param int $mainNodeId main node that this node is connected to
         */
        private function setFundRaisingNode($mainNodeId){
            
            $isAllValueEmpty = true;
            
            $temp = array();
            $temp['node_db_label'] = 'FundRaising';
            foreach($this->userArray['fundraising'] as $key=>$val){
                $temp[$key] = $val;
                if($val != '')
                    $isAllValueEmpty = false;
            }
            
            if(!$isAllValueEmpty){
                $guid = 'fundraising_'.$mainNodeId;
                $nodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($mainNodeId, $nodeId, 'HAS_FUNDRAISING');
            }
        }
        
    }
}
?>
