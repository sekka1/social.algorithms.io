<?php
/**
 * Creates the json data array to feed the Force-Directed Graphs
 * 
 */
 
 
 $createDirectForceJSON = new CreateDirectForceJSON();
 $createDirectForceJSON->setFile('flickr.csv');
 $createDirectForceJSON->createJSON();
 $jsonData = $createDirectForceJSON->getJSON();
 
 echo $jsonData;
 
 
 class CreateDirectForceJSON{
 	
	private $filePath;
	private $nodes = array();
	private $links = array();
	
	private $fileArray = array();
	
	public function __construct(){}
	
	public function setFile($filePath){
		$this->filePath = $filePath;
	}
	public function getJSON(){
		$data['nodes'] = $this->nodes;
		$data['links'] = $this->links;
		return json_encode($data);
	}
	public function createJSON(){


		if (($handle = fopen($this->filePath, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        	
				$this->setNode($data[0]);
				$this->setNode($data[1]);
				$this->setLink($data[0], $data[1], $data[2]);
				/*	
		        $num = count($data);
		        echo "<p> $num fields in line $row: <br /></p>\n";
		        $row++;
		        for ($c=0; $c < $num; $c++) {
		            echo $data[$c] . "<br />\n";
		        }
				 */
		    }
		    fclose($handle);
		}
//print_r($this->nodes);
//print_r($this->links);
	}
	/**
	 * Puts a node into the node array
	 * 
	 * @param string $nodeName
	 */
	private function setNode($nodeName){
		
		// Check if this node name is already in the array
		if(! $this->isNameInNodeArray($nodeName)){
			$data['name'] = $nodeName;
			$data['group'] = 0;
			array_push($this->nodes,$data);
		}
	}
	/**
	 * Creates a link between 2 node by names
	 * 
	 * @param string $node1
	 * @param string $node2
	 * @param int $value
	 */
	private function setLink($node1, $node2, $value=1){
		$data['source'] = $this->getNodeArrayPosition($node1);
		$data['target']	= $this->getNodeArrayPosition($node2);
		$data['value'] = $value;
		array_push($this->links,$data);
	}
	private function isNameInNodeArray($nodeName){
		$isInArray = false;
		foreach($this->nodes as $aNode){
			if($aNode['name'] == $nodeName)
				return true;
		}
		return $isInArray;
	}
	/**
	 * Returns the node position in the array
	 * 
	 * @param string $node
	 * @return int
	 */
	private function getNodeArrayPosition($node){
		$position = -1;
		for($i=0;$i<count($this->nodes);$i++){
			if($this->nodes[$i]['name']==$node)
				return $i;
		}
		return $position;
	}
	
 }
