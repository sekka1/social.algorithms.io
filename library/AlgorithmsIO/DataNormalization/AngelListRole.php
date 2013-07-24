<?php
/**
 * 
 * This class is used to normalize values from a Akkadian COmpany table.  
 * It will check if the value is there and if it is, it will retrieve that value and return it
 * in a normalized array.
 * 
 * @author garland
 * 
 */
namespace AlgorithmsIO\DataNormalization{

	class AngelListRole{
				
		// Arrays holding label specific data
		private $person;
		private $employment = array();
		private $education = array();
                private $allValues = array();
		
		// The list of keys names that we want to get out of the input
		//
		// Each of these keys has a path somewhere in the json object that is passed in.  That value is checked if it is there or not
		// before returning the value.
		//
		private $keys = array(//'source_uid',
                                        'startup_roles'
                                    );
		
		public function __construct(){}
		/**
		 * Retrieves the list of values for a Crunchbase User Profile API call specified in the $this->keys array.
		 * 
		 * Example REST Endpoint: person/david-rohrsheim.js
		 * 
		 * @param jsonObject $aUser
		 * @return array
		 */
		public function getValues($data){
                    
                    $this->allValues = array();
                    foreach($this->keys as $aKey){
                        $function = 'value_'.$aKey;
                            $this->allValues[$aKey] = $this->$function($data);
                    }
                        
                    return $this->allValues;
		}
                /**
                 * Company data.  THis is high level data that is not marked from
                 * another source.
                 * 
                 * @param type $data
                 * @return type
                 */
                private function value_startup_roles($data){
                    $dataArray = array();
                    
                    foreach($data->startup_roles as $anItem){
                        array_push($dataArray, $this->getARole($anItem));
                    }
                    
                    return $dataArray;
                }
                private function getARole($anItem){
                    $dataArray = array();
                    
                    $dataArray['role_id'] = $this->get_roleID($anItem);
                    $dataArray['role'] = $this->get_role($anItem);
                    $dataArray['created_at'] = $this->get_created_at($anItem);
                    $dataArray['started_at'] = $this->get_started_at($anItem);
                    $dataArray['ended_at'] = $this->get_ended_at($anItem);
                    $dataArray['confirmed'] = $this->get_confirmed($anItem);
                    $dataArray['startup_id'] = $this->get_startup_id($anItem);
                    $dataArray['startup_name'] = $this->get_startup_name($anItem);
                    
                    return $dataArray;
                }
                
                private function get_roleID($data){
                    if(isset($data->id))
                            return $this->pruneValue($data->id);
			else
                            return null;
                }
                private function get_role($data){
                    if(isset($data->role))
                            return $this->pruneValue($data->role);
			else
                            return null;
                }
                private function get_created_at($data){
                    if(isset($data->created_at))
                            return $this->pruneValue($data->created_at);
			else
                            return null;
                }
                private function get_started_at($data){
                    if(isset($data->started_at))
                            return $this->pruneValue($data->started_at);
			else
                            return null;
                }
                private function get_ended_at($data){
                    if(isset($data->ended_at))
                            return $this->pruneValue($data->ended_at);
			else
                            return null;
                }
                private function get_confirmed($data){
                    if(isset($data->confirmed)){
                        if($data->confirmed)
                            return "true";
                        else
                            return "false";
                    }
                    else
                        return "false";
                }
                private function get_startup_id($data){
                    if(isset($data->startup->id))
                            return $this->pruneValue($data->startup->id);
			else
                            return null;
                }
                private function get_startup_name($data){
                    if(isset($data->startup->name))
                            return $this->pruneValue($data->startup->name);
			else
                            return null;
                }
                
                /**
                 * prunes a value:
                 * -Lower case it
                 * -remove unwanted characters
                 * 
                 * @param string $value
                 * @return string
                 */
                private function pruneValue($value){
                    $value = strtolower($value);
                    $value = mysql_real_escape_string($value);
                    return $value;
                }
	}
}
