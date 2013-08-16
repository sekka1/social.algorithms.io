<?php
/**
 * Creates the Batch Importers Tab CSV file 
 * 
 * Creates 2 files for the batch importer.  One node file and one relationship file
 * 
 * Input to this script is a file with each line as a person and the json output
 * of the API call.
 * 
 * * Testing:
 * php library/AlgorithmsIO/Utilities/CreateCSVImportFileAngelListUser.php
 * 
 * Running full for all ~100k users
 * nohup php library/AlgorithmsIO/Utilities/CreateCSVImportFileAngelListUser.php&
 * 
 */
ini_set('memory_limit','4024M');
include_once('library/AlgorithmsIO/Node/Import/AngelListStartup.php');
ini_set('max_execution_time', 6000);

$createCSV = new CreateCSVImport("/Users/gkan/Downloads/neo4j-stuff/source_data/angellist_startups_080113.txt");
$createCSV->create();
$createCSV->output();


class CreateCSVImport{
    
    private $fileLocation;
    private $headers;
    private $didGetHeaders;
    
    private $importNodes;
    
    private $outputNodeFile = '/Users/gkan/Downloads/node_angellist_startup.csv';
    private $outputRelationshipFile = '/Users/gkan/Downloads/relationship_angellist_startup.csv';
    private $outputNodeFileHandle;
    private $outputRelationshipFileHandle;
    
    public function __construct($file){
        $this->fileLocation = $file;
        
        $this->didGetHeaders = false;
        $this->headers = array();
        
        $this->importNodes = new \AlgorithmsIO\Node\Import\AngelListStartup();
        
        $this->outputNodeFileHandle = fopen($this->outputNodeFile, 'w');
        $this->outputRelationshipFileHandle = fopen($this->outputRelationshipFile, 'w');
    }
    /**
     * Initiates creating the AngelListUser object which holds each users information
     * which can be called to get the info.
     */
    public function create(){
   
        $n=0;
        
        $handle = @fopen($this->fileLocation, "r");
        if ($handle) {
            
            while (($buffer = fgets($handle, 8096)) !== false) {
                //echo $buffer;
                $line = json_decode($buffer, true);
//print_r($line);

                if(! $this->didGetHeaders){
                    $this->addAdditionalHeaders();
                    $this->setHeaders($line);
                    $this->didGetHeaders=true;
                    $this->importNodes->setHeader($this->headers);
                }
                
//print_r($this->headers);
                //if($n>=0)
                    $this->importNodes->add($line);
                
                //if($n==2)
                //    break;
                $n++;
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
     * 
     * And basically additional info or param names that we want to add in that is
     * not in the data arrays coming in.
     */
    private function addAdditionalHeaders(){
        array_push($this->headers, 'node_db_label');
        array_push($this->headers, 'datasource_name');
        array_push($this->headers, 'value');
    }
    /**
     * Foreach person that was taking in and produced, it will output the nodes
     * and relationships in the 2 files.
     */
    public function output(){
        
        $allNodes = $this->importNodes->getAllNodes('angelList');
        $allRels = $this->importNodes->getAllRelationships('angelList');
     
        $this->outputNodeHeaders();
        $this->outputRelHeaders();
        
        // Output Nodes
        foreach($allNodes as $anItem){
            $this->outputNodeToFile(json_decode($anItem['json'], true));
        }
        // Output Relationships
        foreach($allRels as $anItem){
            $this->outputRelationshipToFile($anItem);
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
     * Output the relationship headers.
     */
    private function outputRelHeaders(){
        $headers = array('start', 'end', 'type');
        fputcsv($this->outputRelationshipFileHandle, $headers, "\t");
    }  
    /**
     * Outputs the array to the file in tab delimited format.  
     * 
     * Array format: 
     * same as header
     * 
     * @param array $array
     */
    private function outputNodeToFile($array){
        fputcsv($this->outputNodeFileHandle, $array, "\t");
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
        fputcsv($this->outputRelationshipFileHandle, $array, "\t");
    }
}
?>
