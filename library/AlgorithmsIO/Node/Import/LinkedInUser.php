<?php
namespace AlgorithmsIO\Node\Import{
    
    include_once('ImportBase.php');
    
    /**
     * Imports user from the LinkedIn crawling
     * 
     * There will be a "source_user" and a "friends".  Source_user is where the data
     * was pulled from.  The friend is the friend of this source user.
     * 
     * Incoming data format: 
     * {"source_user":{"source_uid":"6NLkKJNvpP","person":{"firstName":"anthony","lastName":"yee","headline":"software development manager at skyhigh networks","industry":"computer software","location_country_code":"us","location_name":"san francisco bay area","numConnections":"258","numConnectionsCapped":"","pictureUrl":null,"emailAddress":"smyee@yahoo.com","honors":null,"publicProfileUrl":"http:\/\/www.linkedin.com\/pub\/anthony-yee\/1\/a10\/230","twitter_handle_1":"yeesy","twitter_id_1":"32882536","twitter_handle_2":null,"twitter_id_2":null},"educations":[{"activities":null,"degree":"bachelor of engineering","endDate":"2004","fieldOfStudy":"computer engineering","id":"2307861","notes":null,"schoolName":"mcgill university"}],"employments":[{"position_id":"400138417","company_id":null,"industry":"computer software","name":"skyhigh networks","size":null,"type":null,"is_current":"1","start_date_year":"2013","start_date_month":"4","title":"software development manager"},{"position_id":"140347171","company_id":"9022","industry":"computer & network security","name":"arcsight","size":"10,001+ employees","type":"public company","is_current":"1","start_date_year":"2006","start_date_month":"12","title":"software development manager"},{"position_id":"7648478","company_id":null,"industry":"telecommunications","name":"intellambda","size":null,"type":null,"is_current":"","start_date_year":"2004","start_date_month":"8","title":"lead software engineer"},{"position_id":"9400710","company_id":"3960","industry":"telecommunications","name":"ciena","size":"1001-5000 employees","type":"public company","is_current":"","start_date_year":"2000","start_date_month":"9","title":"senior software engineer"},{"position_id":"103115210","company_id":null,"industry":"computer software","name":"oni","size":null,"type":null,"is_current":"","start_date_year":"1999","start_date_month":null,"title":"senior s\/w engineer (network planning software)"},{"position_id":"28449598","company_id":"1057","industry":"telecommunications","name":"newbridge networks","size":"1001-5000 employees","type":"public company","is_current":"","start_date_year":"1995","start_date_month":null,"title":"software engineer"}],"employmentsPastThree":[{"position_id":"7648478","company_id":null,"industry":"telecommunications","name":"intellambda","size":null,"type":null,"is_current":"","start_date_year":"2004","start_date_month":"8","title":"lead software engineer"},{"position_id":"9400710","company_id":"3960","industry":"telecommunications","name":"ciena","size":"1001-5000 employees","type":"public company","is_current":"","start_date_year":"2000","start_date_month":"9","title":"senior software engineer"},{"position_id":"103115210","company_id":null,"industry":"computer software","name":"oni","size":null,"type":null,"is_current":"","start_date_year":"1999","start_date_month":null,"title":"senior s\/w engineer (network planning software)"}],"employmentsCurrentThree":[{"position_id":"400138417","company_id":null,"industry":"computer software","name":"skyhigh networks","size":null,"type":null,"is_current":"1","start_date_year":"2013","start_date_month":"4","title":"software development manager"},{"position_id":"140347171","company_id":"9022","industry":"computer & network security","name":"arcsight","size":"10,001+ employees","type":"public company","is_current":"1","start_date_year":"2006","start_date_month":"12","title":"software development manager"}]},"friends":{"source_uid":"nPK-PXnt56","person":{"firstName":"stefan","lastName":"zier","headline":"lead architect at sumo logic","industry":"computer software","location_country_code":"us","location_name":"san francisco bay area","numConnections":"500","numConnectionsCapped":"1","pictureUrl":"http:\/\/m.c.lnkd.licdn.com\/mpr\/mprx\/0_ukj160od4obw4izzgivu6gw34w-q4ieza5du6y4rfm6nfbxvhcd0epuo9ltmuxwjsbrddron4xmd","emailAddress":null,"honors":null,"publicProfileUrl":"http:\/\/www.linkedin.com\/in\/szier","twitter_handle_1":null,"twitter_id_1":null,"twitter_handle_2":null,"twitter_id_2":null},"educations":[],"employments":[{"position_id":"430108663","company_id":"1037816","industry":"internet","name":"sumo logic","size":"51-200 employees","type":"privately held","is_current":"1","start_date_year":"2013","start_date_month":"8","title":"lead architect"}],"employmentsPastThree":[],"employmentsCurrentThree":[]}}
     */
    class LinkedInUser extends CreateNodeBatchFilesBase{

        private $userArray;
        protected $headerArray;
        private $startingRowNumber;
        private $totalRows;

        private $allNodes;
        private $allRelationships;

        protected $datasource_name = 'Linkedin';

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
            
            if(!isset($this->userArray['source_user']))
                return $this->allNodes;

            //$nodeId = $this->setPersonNodes();
            //$this->setRoleNodes($nodeId);
            //$this->setSkillNodes($nodeId);
            
            
            // Set Sourcse person node
            $source_guid = $this->userArray['source_user']['source_uid'];
            $source_nodeId = $this->isNodeInTable($source_guid);
                // Check if this person is in the DB already.  If not insert person
                if($source_nodeId==-1){
                    // Insert person
                    $source_nodeId = $this->setPersonNodes($this->userArray['source_user']);
                    $this->setEducationNodes($source_nodeId, $this->userArray['source_user']);
                    $this->setEmploymentNodes($source_nodeId, $this->userArray['source_user']);
                }
                    
            // Set friend node
            $friend_nodeId = $this->setPersonNodes($this->userArray['friends']);
            $this->setEducationNodes($friend_nodeId, $this->userArray['friends']);
            $this->setEmploymentNodes($friend_nodeId, $this->userArray['friends']);
            
            // Add "KNOWS" Relationship between source user and friend
            // in both direction
            $this->addReltionship($source_nodeId, $friend_nodeId, 'KNOWS');
            $this->addReltionship($friend_nodeId, $source_nodeId, 'KNOWS');
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
        private function setPersonNodes($data){
            $nodeArray = array();

            $nodeArray['node_db_label'] = 'PersonGUID';
            $nodeArray['datasource_name'] = $this->datasource_name;
            $nodeArray['source_uid'] = $data['source_uid'];

            foreach($data['person'] as $key=>$val){
                $nodeArray[$key] = $val;
            }
            
            // Fill out array with missing headers
            $guid = 'user_'.$nodeArray['source_uid'];
            $nodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($nodeArray)));
            
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
        private function setEducationNodes($pseronNodeId, $data){
            
            $counter = 0; // For unique guid for an education
            
            foreach($data['educations'] as $item){
                
                // Main Node
                $temp=array();
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'Education';
                    $temp[$key] = $val;
                }
                
                // Since an education has no guid with this data we are going to make one
                $edu_guid = $data['source_uid'].'_education_'.$counter;
                $counter++;
                $eduNodeId = $this->addNode($edu_guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($pseronNodeId, $eduNodeId, 'HAS_EDUCATION');
                
                
                // Institute Node
                $institue = array('node_db_label'=>'Institution','value'=>$temp['schoolName']);
                $institutionGUID = 'institution_'.$temp['id'];
                $institutionNodeId = $this->addNode($institutionGUID, json_encode($this->fillOutDataWithAllHeaders($institue)));
                $this->addReltionship($eduNodeId, $institutionNodeId, 'ATTENDED');
                 
                // Degree Node
                $degree = array('node_db_label'=>'Degree','value'=>$temp['degree']);
                $degreeGUID = 'degree_'.$temp['degree'];
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
        private function setEmploymentNodes($pseronNodeId, $data){
            
            $counter = 0; // For unique guid for an education
            
            foreach($data['employments'] as $item){
                
                // Main Node
                $temp=array();
                foreach($item as $key=>$val){
                    $temp['node_db_label'] = 'Employment';
                    $temp[$key] = $val;
                }
                
                // Add employment main node
                $guid = $data['source_uid'].'_employment_'.$counter;
                $counter++;
                $employmentNodeId = $this->addNode($guid, json_encode($this->fillOutDataWithAllHeaders($temp)));
                $this->addReltionship($pseronNodeId, $employmentNodeId, 'HAS_EMPLOYMENT');

                // EmploymentTitle
                //$titleGUID = $this->setBlankValue($temp['title'], 'blank_title');
                //$title = array('node_db_label'=>'EmploymentTitle','value'=>$titleGUID);
                //$titleNodeId = $this->addNode($titleGUID, json_encode($this->fillOutDataWithAllHeaders($title)));
                //$this->addReltionship($employmentNodeId, $titleNodeId, 'HAS_EMPLOYMENT_TITLE');

                // EmploymentFirm
                $firm = array('node_db_label'=>'EmploymentFirm','value'=>$temp['name']);
                $firmGUID = 'EmploymentFirm_'.$temp['position_id'];
                $firmNodeId = $this->addNode($firmGUID, json_encode($this->fillOutDataWithAllHeaders($firm)));
                $this->addReltionship($employmentNodeId, $firmNodeId, 'HAS_EMPLOYMENT_FIRM');                
            }
        }

    }
}
?>
