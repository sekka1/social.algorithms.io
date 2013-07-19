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

	class AkkadianCompany{
				
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
		private $keys = array('source_uid',
                                        'company',
                                        'angelList',
                                        'twitter',
                                        'crunchbase',
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
                        //$this->pruneValues();
                        
			return $this->allValues;
		}
		/**
		 * Getting the UID for this user in this datasource feed
		 * 
		 */
		private function value_source_uid($data){
			if(isset($data->idCompany))
				return $data->idCompany;
			else
				return null;
		}
                /**
                 * Company data.  THis is high level data that is not marked from
                 * another source.
                 * 
                 * @param type $data
                 * @return type
                 */
                private function value_company($data){
                    $dataArray = array();
                    $dataArray['name'] = $this->get_name($data);
                    $dataArray['website'] = $this->get_website($data);
                    $dataArray['founded'] = $this->get_founded($data);
                    $dataArray['computedTotalRaised'] = $this->get_computedTotalRaised($data);
                    $dataArray['city'] = $this->get_city($data);
                    $dataArray['state'] = $this->get_state($data);
                    $dataArray['countryCode'] = $this->get_countryCode($data);
                    $dataArray['status'] = $this->get_status($data);
                    $dataArray['companyType'] = $this->get_companyType($data);
                    
                    return $dataArray;
                }
                private function get_name($data){
			if(isset($data->name))
				return $this->pruneValue($data->name);
			else
				return null;
		}
                private function get_website($data){
			if(isset($data->website))
				return $this->pruneValue($data->website);
			else
				return null;
		}
                private function get_founded($data){
			if(isset($data->founded))
				return $this->pruneValue($data->founded);
			else
				return null;
		}
                private function get_computedTotalRaised($data){
			if(isset($data->computedTotalRaised))
				return $this->pruneValue($data->computedTotalRaised);
			else
				return null;
		}
                private function get_city($data){
			if(isset($data->city))
				return $this->pruneValue($data->city);
			else
				return null;
		}
                private function get_state($data){
			if(isset($data->state))
				return $this->pruneValue($data->state);
			else
				return null;
		}
                private function get_countryCode($data){
			if(isset($data->countryCode))
				return $this->pruneValue($data->countryCode);
			else
				return null;
		}
                private function get_status($data){
			if(isset($data->status))
				return $this->pruneValue($data->status);
			else
				return null;
		}
                private function get_companyType($data){
			if(isset($data->companyType))
				return $this->pruneValue($data->companyType);
			else
				return null;
		}
                
		/**
                 * Twitter data
                 */
                private function value_twitter($data){
                    $dataArray = array();
                    $dataArray['value'] = $this->get_twitter_name($data);
                    $dataArray['username'] = $this->get_twitter_name($data);
                    
                    return $dataArray;
                }
                private function get_twitter_name($data){
			if(isset($data->twitterUserName))
				return $this->pruneValue($data->twitterUserName);
			else
				return null;
		}
                /**
                 * Angel List data
                 */
                private function value_angelList($data){
                    $dataArray = array();
                    $dataArray['value'] = $this->get_angel_list_id($data);
                    $dataArray['alid'] = $this->get_angel_list_id($data);
                    $dataArray['alQuality'] = $this->get_angel_list_alQuality($data);
                    $dataArray['alProductDescription'] = $this->get_angel_list_alProductDescription($data);
                    $dataArray['alHighConcept'] = $this->get_angel_list_alHighConcept($data);
                    $dataArray['alType'] = $this->get_angel_list_alType($data);
                    $dataArray['alMarkets'] = $this->get_angel_list_alMarkets($data);
                    
                    return $dataArray;
                }
		private function get_angel_list_id($data){
			if(isset($data->alid))
				return $this->pruneValue($data->alid);
			else
				return null;
		}
                private function get_angel_list_alQuality($data){
			if(isset($data->alQuality))
				return $this->pruneValue($data->alQuality);
			else
				return null;
		}
                private function get_angel_list_alProductDescription($data){
			if(isset($data->alProductDescription))
				return $this->pruneValue($data->alProductDescription);
			else
				return null;
		}
                private function get_angel_list_alHighConcept($data){
			if(isset($data->alHighConcept))
				return $this->pruneValue($data->alHighConcept);
			else
				return null;
		}
                private function get_angel_list_alType($data){
			if(isset($data->alType))
				return $this->pruneValue($data->alType);
			else
				return null;
		}
                private function get_angel_list_alMarkets($data){
			if(isset($data->alMarkets))
				return $this->pruneValue($data->alMarkets);
			else
				return null;
		}
                /**
                 * Crunchbase data
                 */
                private function value_crunchbase($data){
                    $dataArray = array();
                    $dataArray['value'] = $this->get_crunchbase_cbPermalink($data);
                    $dataArray['cbPermalink'] = $this->get_crunchbase_cbPermalink($data);
                    $dataArray['cbCategory'] = $this->get_crunchbase_cbCategory($data);
                    $dataArray['cbTags'] = $this->get_crunchbase_cbTags($data);
                    
                    
                    return $dataArray;
                }
                private function get_crunchbase_cbPermalink($data){
			if(isset($data->cbPermalink))
				return $this->pruneValue($data->cbPermalink);
			else
				return null;
		}
                private function get_crunchbase_cbCategory($data){
			if(isset($data->cbCategory))
				return $this->pruneValue($data->cbCategory);
			else
				return null;
		}
                private function get_crunchbase_cbTags($data){
			if(isset($data->cbTags))
				return $this->pruneValue($data->cbTags);
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
