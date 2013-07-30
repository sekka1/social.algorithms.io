<?php
namespace AlgorithmsIO\Node\Import{
    
    include_once('ImportBase.php');
    
    class Crunchbaseuser extends CreateNodeBatchFilesBase{

        private $userArray;
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
            
            if(!isset($this->userArray['person']))
                return $this->allNodes;

            $this->setPersonNodes();
            $this->setEducationNodes();
            $this->setEmploymentNodes();
            
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
            foreach($this->userArray['data_meta_data'] as $key=>$val){
                $nodeArray[$key] = $val;
            }
            
            // Fill out array with missing headers
            $this->addNode($nodeArray['source_uid'], json_encode($this->fillOutDataWithAllHeaders($nodeArray)));
        }
        
        
        
        
        /**
         * Education nodes
         * 
         * Structure:
         * Person-[HAS_EDUCATION]->Education:EducationMain-[ATTENDED]->Institue
         *                                      -[AWARDED]->Degree type
         * 
         */
        private function setEducationNodes(){
            
            $counter = 0; // For unique guid for an education
            
            foreach($this->userArray['educations'] as $item){
                
                // Main Node
                $temp=array();
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'Education';
                    $temp[$key] = $val;
                }
                
                // Since an education has no guid with this data we are going to make one
                $edu_guid = $this->userArray['source_uid'].'_education_'.$counter;
                $counter++;
                $this->addNode($edu_guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($this->userArray['source_uid'], $edu_guid, 'HAS_EDUCATION');
                
                
                // Institute Node
                $institue = array('node_db_label'=>'Institution','value'=>$temp['institution']);
                $this->addNode($temp['institution'], json_encode($this->fillOutDataWithAllHeaders($institue)));
                $this->addReltionship($edu_guid, $temp['institution'], 'ATTENDED');
                 
                // Degree Node
                $degree = array('node_db_label'=>'Degree','value'=>$temp['type']);
                $this->addNode($temp['type'], json_encode($this->fillOutDataWithAllHeaders($degree)));
                $this->addReltionship($edu_guid, $temp['type'], 'AWARDED');
            }
        }
        
        /**
         * Employment Nodes
         * 
         * Structure:
         * Person-[HAS_EMPLOYMEN]->Employment:employmentMain-[:HAS_EMPLOYMENT_TITLE]->EmploymentTitle:employmentTitle
         *                                                  -[:HAS_EMPLOYMENT_FIRM]->EmploymentFirm:employmentFirm
         * 
         */
        private function setEmploymentNodes(){
            
            $counter = 0; // For unique guid for an education
            
            foreach($this->userArray['employments'] as $item){
                
                // Main Node
                $temp=array();
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'Employment';
                    $temp[$key] = $val;
                }
                
                // Add employment main node
                $guid = $this->userArray['source_uid'].'_employment_'.$counter;
                $counter++;
                $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($this->userArray['source_uid'], $guid, 'HAS_EMPLOYMENT');

                // EmploymentTitle
                $title = array('node_db_label'=>'EmploymentTitle','value'=>$temp['title']);
                $this->addNode($temp['title'], json_encode($this->fillOutDataWithAllHeaders($title)));
                $this->addReltionship($guid, $temp['title'], 'HAS_EMPLOYMENT_TITLE');

                // EmploymentFirm
                $firm = array('node_db_label'=>'EmploymentFirm','value'=>$temp['firm_name']);
                $this->addNode($temp['firm_name'], json_encode($this->fillOutDataWithAllHeaders($firm)));
                $this->addReltionship($guid, $temp['title'], 'HAS_EMPLOYMENT_FIRM');                
            }
        }
        
        
        
        

        
        
    }
}
?>
