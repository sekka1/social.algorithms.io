<?php
namespace AlgorithmsIO\Node\Import{
    
    include_once('ImportBase.php');
    
    class CrunchbaseUser extends CreateNodeBatchFilesBase{

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

            $nodeId = $this->setPersonNodes();
            $this->setEducationNodes($nodeId);
            $this->setEmploymentNodes($nodeId);
            
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
            $nodeId = $this->addNode($nodeArray['source_uid'], json_encode($this->fillOutDataWithAllHeaders($nodeArray)));
            
            return $nodeId;
        }
        
        
        
        
        /**
         * Education nodes
         * 
         * Structure:
         * Person-[HAS_EDUCATION]->Education:EducationMain-[ATTENDED]->Institue
         *                                      -[AWARDED]->Degree type
         * 
         * @param int $pseronNodeId ID of the main person's node
         * 
         */
        private function setEducationNodes($pseronNodeId){
            
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
                $eduNodeId = $this->addNode($edu_guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($pseronNodeId, $eduNodeId, 'HAS_EDUCATION');
                
                
                // Institute Node
                $institue = array('node_db_label'=>'Institution','value'=>$temp['institution']);
                $institutionGUID = 'institution_'.$temp['institution'];
                $institutionNodeId = $this->addNode($temp['institution'], json_encode($this->fillOutDataWithAllHeaders($institue)));
                $this->addReltionship($eduNodeId, $institutionNodeId, 'ATTENDED');
                 
                // Degree Node
                $degree = array('node_db_label'=>'Degree','value'=>$temp['type']);
                $degreeGUID = 'degree_'.$temp['type'];
                $degreeNodeId = $this->addNode($degreeGUID, json_encode($this->fillOutDataWithAllHeaders($degree)));
                $this->addReltionship($eduNodeId, $degreeNodeId, 'AWARDED');
            }
        }
        
        /**
         * Employment Nodes
         * 
         * Structure:
         * PersonGUID-[:HAS_EMPLOYMENT]->Employment:employmentMain-[:HAS_EMPLOYMENT_TITLE]->EmploymentTitle:employmentTitle
         *                                                  -[:HAS_EMPLOYMENT_FIRM]->EmploymentFirm:employmentFirm
         * 
         * @param int $pseronNodeId ID of the main person's node
         */
        private function setEmploymentNodes($pseronNodeId){
            
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
                $employmentNodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($pseronNodeId, $employmentNodeId, 'HAS_EMPLOYMENT');

                // EmploymentTitle
                //$titleGUID = $this->setBlankValue($temp['title'], 'blank_title');
                //$title = array('node_db_label'=>'EmploymentTitle','value'=>$titleGUID);
                //$titleNodeId = $this->addNode($titleGUID, json_encode($this->fillOutDataWithAllHeaders($title)));
                //$this->addReltionship($employmentNodeId, $titleNodeId, 'HAS_EMPLOYMENT_TITLE');

                // EmploymentFirm
                $firm = array('node_db_label'=>'EmploymentFirm','value'=>$temp['firm_permalink']);
                $firmNodeId = $this->addNode($temp['firm_permalink'], json_encode($this->fillOutDataWithAllHeaders($firm)));
                $this->addReltionship($employmentNodeId, $firmNodeId, 'HAS_EMPLOYMENT_FIRM');                
            }
        }
        /**
         * Checks if a value is blank.  If so, it lets the you set it to whatever
         * you want else it just returns the original value;
         * 
         * @param string $value
         * @param string $setTo
         * @return string
         */
        //private function setBlankValue($value, $setTo){
        //    if($value=='')
        //        $value = $setTo;
        //    return $value;
        //}
        
        
        
        

        
        
    }
}
?>
