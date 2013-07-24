<?php
/**
 * Creates the Batch Importers Tab CSV file 
 * 
 * Creates 2 files for the batch importer.  One node file and one relationship file
 * 
 * Input to this script is a file with each line as a person and the json output
 * of the API call.
 * 
 */
ini_set('memory_limit','2024M');


$createCSV = new CreateCSVImport("/Users/gkan/Downloads/users.txt");
$createCSV->create();
$createCSV->output();


class CreateCSVImport{
    
    private $fileLocation;
    private $headers;
    private $didGetHeaders;
    
    private $users;
    
    private $outputNodeFile = '/Users/gkan/Downloads/node.csv';
    private $outputRelationshipFile = '/Users/gkan/Downloads/relationship.csv';
    private $outputNodeFileHandle;
    private $outputRelationshipFileHandle;
    
    public function __construct($file){
        $this->fileLocation = $file;
        
        $this->didGetHeaders = false;
        $this->headers = array();
        
        $this->users = array();
        
        $this->outputNodeFileHandle = fopen($this->outputNodeFile, 'w');
        $this->outputRelationshipFileHandle = fopen($this->outputRelationshipFile, 'w');
    }
    /**
     * Initiates creating the AngelListUser object which holds each users information
     * which can be called to get the info.
     */
    public function create(){
        $handle = @fopen($this->fileLocation, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                //echo $buffer;
                $line = json_decode($buffer, true);
//print_r($line);

                if(! $this->didGetHeaders){
                    $this->addAdditionalHeaders();
                    $this->setHeaders($line);
                    $this->didGetHeaders=true;
                }
                
//print_r($this->headers);

                $newUser = new AngelListUser($line);
                
                $this->users[] = $newUser;

                //if(count($this->users)==1000)
                //    break;
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
     */
    private function addAdditionalHeaders(){
        array_push($this->headers, 'node_db_label');
        array_push($this->headers, 'datasource_name');
    }
    /**
     * Foreach person that was taking in and produced, it will output the nodes
     * and relationships in the 2 files.
     */
    public function output(){
        
        $rowNumber = 1;
        
        $this->outputNodeHeaders();
        
        foreach($this->users as $aUser){
            $aUser->setStartingRowNumber($rowNumber);
            $aUser->setHeader($this->headers);
            
            // Output this user to files
            $this->outputNodeToFile($aUser->getNodes());
            $this->outputRelationshipToFile($aUser->getRelationships());
            $rowNumber += $aUser->getTotalRows();
            $rowNumber++;
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
     * Outputs the array to the file in tab delimited format.  
     * It will also augment the indexes that are
     * missing with the array headers.  It outputs in the order of the array header
     * 
     * @param array $array
     */
    private function outputNodeToFile($array){
        $temp=array();
        // Output each node
        foreach($array as $item){
            
            // Fill out array with missing headers
            foreach($this->headers as $val){
                if(array_key_exists($val, $item))
                    $temp[$val] = $item[$val];
                else
                    $temp[$val] = '';
            }
            fputcsv($this->outputNodeFileHandle, $temp, "\t");
        }
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
        foreach ($array as $fields) {
            fputcsv($this->outputRelationshipFileHandle, $fields, "\t");
        }
    }
}

class AngelListUser{
    
    private $userArray;
    private $headerArray;
    private $startingRowNumber;
    private $totalRows;
    
    private $allNodes;
    private $allRelationships;
    
    private $datasource_name = 'angelList';
    
    public function __construct($userArray) {
        $this->userArray = $userArray;
        $this->startingRowNumber=0;
        $this->totalRows=0;
        $this->allNodes=array();
        $this->allRelationships=array();
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
    /**
     * Returns an array holding arrays of nodes.  Each node has properties
     * associated to it, depending what the API gave back.
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
        array_push($this->allNodes, $nodeArray);
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
                array_push($this->allNodes, $temp);
                $this->addRelationshipToMainNode('HAS_ROLE');
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
                array_push($this->allNodes, $temp);
                $this->addRelationshipToMainNode('HAS_SKILL');
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
?>
