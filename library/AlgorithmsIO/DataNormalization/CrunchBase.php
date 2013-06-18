<?php
/**
 * Garland
 * This class is used to retrieve values from a CrunchBase API user profile call.  
 * It will check if the value is there and if it is, it will retrieve that value and return it
 * in a normalized array.
 * 
 */
namespace AlgorithmsIO\DataNormalization{

	class CrunchBase{
		
		private $access_token = null;
		
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
							'person',
							'other_handles',
							'data_meta_data', // Data about this data, created, update, etc
							'educations',
							'employments'
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
		public function getUsersValues($aUser){
			$this->allValues = array();
			foreach($this->keys as $aKey){
				$function = 'value_'.$aKey;
				$this->allValues[$aKey] = $this->$function($aUser);
			}
                        $this->pruneValues();
                        
			return $this->allValues;
		}
		/**
		 * Getting the UID for this user in this datasource feed
		 * 
		 */
		private function value_source_uid($aUser){
			if(isset($aUser->permalink))
				return $aUser->permalink;
			else
				return null;
		}
		
		/**
		 * Retrieves information about this person
		 * 
		 */
		private function value_person($aUser){
			$dataArray = array();
			$dataArray['firstName'] = $this->value_firstName($aUser);
			$dataArray['lastName'] = $this->value_lastName($aUser);
			$dataArray['homepage_url'] = $this->value_homepage_url($aUser);
			$dataArray['birthplace'] = $this->value_birthplace($aUser);
			$dataArray['blog_url'] = $this->value_blog_url($aUser);
			$dataArray['blog_feed_url'] = $this->value_blog_feed_url($aUser);
			$dataArray['affiliation_name'] = $this->value_affiliation_name($aUser);
			$dataArray['born_year'] = $this->value_born_year($aUser);
			$dataArray['born_month'] = $this->value_born_month($aUser);
			$dataArray['born_day'] = $this->value_born_day($aUser);
			$dataArray['tag_list'] = $this->value_tag_list($aUser);
			$dataArray['alias_list'] = $this->value_alias_list($aUser);
			$dataArray['image_url'] = $this->value_image_url($aUser);
			
			return $dataArray;
		}
		private function value_firstName($aUser){
			if(isset($aUser->first_name))
				return $aUser->first_name;
			else
				return null;
		}
		private function value_lastName($aUser){
			if(isset($aUser->last_name))
				return $aUser->last_name;
			else
				return null;
		}
		private function value_crunchbase_url($aUser){
			if(isset($aUser->crunchbase_url))
				return $aUser->crunchbase_url;
			else
				return null;
		}
		private function value_homepage_url($aUser){
			if(isset($aUser->homepage_url))
				return $aUser->homepage_url;
			else
				return null;
		}		
		private function value_birthplace($aUser){
			if(isset($aUser->birthplace))
				return $aUser->birthplace;
			else
				return null;
		}
		private function value_blog_url($aUser){
			if(isset($aUser->blog_url))
				return $aUser->blog_url;
			else
				return null;
		}	
		private function value_blog_feed_url($aUser){
			if(isset($aUser->blog_feed_url))
				return $aUser->blog_feed_url;
			else
				return null;
		}
		private function value_affiliation_name($aUser){
			if(isset($aUser->affiliation_name))
				return $aUser->affiliation_name;
			else
				return null;
		}		
		private function value_born_year($aUser){
			if(isset($aUser->born_year))
				return $aUser->born_year;
			else
				return null;
		}		
		private function value_born_month($aUser){
			if(isset($aUser->born_month))
				return $aUser->born_month;
			else
				return null;
		}	
		private function value_born_day($aUser){
			if(isset($aUser->born_day))
				return $aUser->born_day;
			else
				return null;
		}	
		private function value_tag_list($aUser){
			if(isset($aUser->tag_list))
				return $aUser->tag_list;
			else
				return null;
		}					
		private function value_alias_list($aUser){
			if(isset($aUser->alias_list))
				return $aUser->alias_list;
			else
				return null;
		}		
	
		private function value_overview($aUser){
			if(isset($aUser->overview))
				return $aUser->overview;
			else
				return null;
		}			
		private function value_image_url($aUser){
			$value = null;
			if(isset($aUser->image)){
				if(isset($aUser->image->zzzzzzzzzzzz)){
					
				}
			}
		}
		/**
		 * Retrieves other user handles for this person from other networks
		 */
		private function value_other_handles($aUser){
			$dataArray = array();
			$dataArray['twitter_username'] = $this->value_twitter_username($aUser);
			return $dataArray;
		}
		private function value_twitter_username($aUser){
			if(isset($aUser->twitter_username))
				return $aUser->twitter_username;
			else
				return null;
		}
		/**
		 * Retrives meta data about this data
		 */
		private function value_data_meta_data($aUser){
			$dataArray = array();
			$dataArray['created_at'] = $this->value_created_at($aUser);
			$dataArray['updated_at'] = $this->value_updated_at($aUser);
			$dataArray['crunchbase_url'] = $this->value_crunchbase_url($aUser);
			return $dataArray;
		}
		 private function value_created_at($aUser){
			if(isset($aUser->created_at))
				return $aUser->created_at;
			else
				return null;
		}
		private function value_updated_at($aUser){
			if(isset($aUser->updated_at))
				return $aUser->updated_at;
			else
				return null;
		}	
		/**
		 * Retrieves all the education and returns an array of it
		 */
		private function value_educations($aUser){
			$dataArray = array();
			if(isset($aUser->degrees)){
				foreach($aUser->degrees as $anItem){
					$data['type'] = $this->value_education_degree_type($anItem);
					$data['subject'] = $this->value_education_subject($anItem);
					$data['institution'] = $this->value_education_institution($anItem);
					$data['graduated_year'] = $this->value_education_graduated_year($anItem);
					$data['graduated_month'] = $this->value_education_graduated_month($anItem);
					$data['graduated_day'] = $this->value_education_graduated_day($anItem);
					array_push($dataArray,$data);
				}
			}
			return $dataArray;
		}
		private function value_education_degree_type($anItem){
			$value = null;
			if(isset($anItem->degree_type)){
				return $anItem->degree_type;
			}
		}			
		private function value_education_subject($anItem){
			$value = null;
				if(isset($anItem->degree_subject)){
					return $anItem->degree_subject;
				}
		}	
		private function value_education_institution($anItem){
			$value = null;
				if(isset($anItem->institution)){
					return $anItem->institution;
				}
		}	
		private function value_education_graduated_year($anItem){
			$value = null;
				if(isset($anItem->graduated_year)){
					return $anItem->graduated_year;
				}
		}			
		private function value_education_graduated_month($anItem){
			$value = null;
				if(isset($anItem->graduated_month)){
					return $anItem->graduated_month;
				}
		}	
		private function value_education_graduated_day($anItem){
			$value = null;
				if(isset($anItem->graduated_day)){
					return $anItem->graduated_day;
				}
		}		
		
		
			
		
	
		/**
		 * Retrieves all the employments and returns an array of it
		 */
		private function value_employments($aUser){
			$dataArray = array();
			if(isset($aUser->relationships)){
				foreach($aUser->relationships as $anItem){
					$data['is_past'] = $this->value_employment_is_past($anItem);
					$data['title'] = $this->value_employment_title($anItem);
					$data['firm_name'] = $this->value_employment_firm_name($anItem);
					$data['firm_permalink'] = $this->value_employment_firm_permalink($anItem);
					$data['firm_type_of_entity'] = $this->value_employment_firm_type_of_entity($anItem);
					array_push($dataArray,$data);
				}
			}
			return $dataArray;
		}
				
		private function value_employment_is_past($anItem){
			$value = null;
				if(isset($anItem->is_past)){
					return $anItem->is_past ? 'true' : 'false';
				}
		}
		private function value_employment_title($anItem){
			$value = null;
				if(isset($anItem->title)){
					return $anItem->title;
				}
		}		
		private function value_employment_firm_name($anItem){
			$value = null;
				if(isset($anItem->firm)){
					if(isset($anItem->firm->name)){
						return $anItem->firm->name;
					}
				}
		}		
		private function value_employment_firm_permalink($anItem){
			$value = null;
				if(isset($anItem->firm)){
					if(isset($anItem->firm->permalink)){
						return $anItem->firm->permalink;
					}
				}
		}	
		private function value_employment_firm_type_of_entity($anItem){
			$value = null;
				if(isset($anItem->firm)){
					if(isset($anItem->firm->type_of_entity)){
						return $anItem->firm->type_of_entity;
					}
				}
		}	
		
		
		/**
                 * Will go through each of the arrays holding values and prune it
                 * 
                 * -lower case the value
                 * -remove '
                 * -remove "
                 */
		private function pruneValues(){
                   
                    $this->lowercase();
                    $this->removeQuotes();
                }
		private function lowercase(){
                    // Person
                    foreach($this->allValues['person'] as $key=>$val){
                        $this->allValues['person'][$key] = strtolower($val);
                    }
                    // Education
                    for($i=0;$i<count($this->allValues['educations']);$i++){
                        foreach($this->allValues['educations'][$i] as $key=>$val){
                            $this->allValues['educations'][$i][$key] = strtolower($val);
                        }
                    }
                    // Employment
                    for($i=0;$i<count($this->allValues['employments']);$i++){
                        foreach($this->allValues['employments'][$i] as $key=>$val){
                            $this->allValues['employments'][$i][$key] = strtolower($val);
                        }
                    }
                }
                private function removeQuotes(){
                    // Person
                    foreach($this->allValues['person'] as $key=>$val){
                        $this->allValues['person'][$key] = str_replace("'","",$val);
                        $this->allValues['person'][$key] = str_replace('"','', $val);
                    }
                    // Education
                    for($i=0;$i<count($this->allValues['educations']);$i++){
                        foreach($this->allValues['educations'][$i] as $key=>$val){
                            //$this->allValues['educations'][$i][$key] = str_replace("'","",$val);
                            //$this->allValues['educations'][$i][$key] = str_replace('"','', $val);
                            $this->allValues['educations'][$i][$key] = mysql_real_escape_string($val);
                        }
                    }
                    // Employment
                    for($i=0;$i<count($this->allValues['employments']);$i++){
                        foreach($this->allValues['employments'][$i] as $key=>$val){
                            //$this->allValues['employments'][$i][$key] = str_replace("'","",$val);
                            //$this->allValues['employments'][$i][$key] = str_replace('"',"",$val);
                            //$this->allValues['employments'][$i][$key] = urlencode($val);
                            $this->allValues['employments'][$i][$key] = mysql_real_escape_string($val);
                        }
                    }
                    
                }


	
			
		
		
		
						
	}
}
