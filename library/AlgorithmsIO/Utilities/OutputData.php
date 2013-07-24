<?php
/*
 * Outputs data to the filesystem
 * 
 */
namespace AlgorithmsIO\Utilities{
    
    class OutputData{
        
        private $outputFileLocation;
        
        
        public function __construct($outputFile) {
            $this->outputFileLocation = $outputFile;
        }
        public function out($data){
            return file_put_contents($this->outputFileLocation, $data, FILE_APPEND);
        }
        
    }
}
?>
