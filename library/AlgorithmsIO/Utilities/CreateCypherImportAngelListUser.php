<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$handle = @fopen("/Users/gkan/Downloads/users.txt", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        //echo $buffer;
        $line = json_decode($buffer);
        //print_r($line);
        
        $mainNode = createMainNode($line);
        
        echo $mainNode;
        
        //exit;
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}


function createMainNode($data){
    
    $pathString = 'begin
                    // MERGE main uid node
                        MERGE (angel_list_uid_node:PersonGUID{value:'.$data->source_uid.',datasource_name:"angelList"})'.
            
                        mergeRolesNodes($data->roles)
                        .'
                        // Set properties for this node
                        '
                        .buildSetPropertyString('angel_list_uid_node', $data->person). "\n"
                        .setRolesProperties($data->roles)."\n".
                        
            
                        "// Create Relationships \n"
                        .createRolesRelationship($data->roles)
                        .";\n"
            
                        ."commit\n
                        exit\n";
    
    return $pathString;
}
function buildSetPropertyString($nodeName, $properties){
    
    $string = '';
                   
   foreach($properties as $key=>$val){
            $string .= ' SET '.$nodeName.'.'.$key.' = '.getParamQuoting($val)."\n";
   }
   return $string;
}
/**
* Returns the params either in quotes or not.  Based on
* the type.  This can be directly returned into a cypher query.
* 
* Quotes used:  " - double quotes
* 
* @param string $param
* @return string
*/
function getParamQuoting($param){
   if(is_numeric($param))
       return $param;
   else
       return '"'.$param.'"';
}
/**
 * 
 * @param type $data
 * @return string
 */
function mergeRolesNodes($data){
    $string = '';
    $n=0;
    foreach($data as $key=>$val){
        $string .= ',(role_node_'.$n.':alRole{role_id:'.$val->role_id.'})'."\n";
        $n++;
    }
    return $string;
}
/**
 * 
 * @param type $data
 * @return string
 */
function setRolesProperties($data){
    $string = '';
    $n=0;
    foreach($data as $item){
        foreach($item as $key=>$val){
            $string .= 'SET role_node_'.$n.'.'.$key.'='.getParamQuoting($val)."\n";
        }
        $n++;
    }
    return $string;    
}
function createRolesRelationship($data){
    $string = '';
    for($n=0;$n<count($data);$n++){
        $string .= "CREATE UNIQUE (angel_list_uid_node)-[:HAS_ROLE]->(role_node_".$n.")\n";
    }
    return $string;
}
?>
