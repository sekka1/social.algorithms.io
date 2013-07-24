<?php
/**
 * Garland
 * This class is used to normalize values from a Angel List API user profile call.  
 * It will check if the value is there and if it is, it will retrieve that value and return it
 * in a normalized array.
 * 
 */
namespace AlgorithmsIO\DataNormalization{

	class AngelListUser{
				
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
					//'company',
                                        'person',
                                        'roles',
                                        'skills',
					//'data_meta_data', // Data about this data, created, update, etc
					//'funding'
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
                        //$this->pruneValues();
                        
			return $this->allValues;
		}
		/**
		 * Getting the UID for this user in this datasource feed
		 * 
		 */
		private function value_source_uid($aUser){
			if(isset($aUser->id))
				return $aUser->id;
			else
				return null;
		}
		/**
                 * Retrieves information about this person
                 */
                private function value_person($aUser){
                    $dataArray = array();
                    $dataArray['name'] = $this->value_name($aUser);
                    $dataArray['bio'] = $this->value_bio($aUser);
                    $dataArray['follower_count'] = $this->value_follower_count($aUser);
                    $dataArray['angellist_url'] = $this->value_angellist_url($aUser);
                    $dataArray['image'] = $this->value_image($aUser);
                    $dataArray['blog_url'] = $this->value_blog_url($aUser);
                    $dataArray['online_bio_url'] = $this->value_online_bio_url($aUser);
                    $dataArray['twitter_url'] = $this->value_twitter_url($aUser);
                    $dataArray['facebook_url'] = $this->value_facebook_url($aUser);
                    $dataArray['linkedin_url'] = $this->value_linkedin_url($aUser);
                    $dataArray['aboutme_url'] = $this->value_aboutme_url($aUser);
                    $dataArray['github_url'] = $this->value_github_url($aUser);
                    $dataArray['dribbble_url'] = $this->value_dribbble_url($aUser);
                    $dataArray['behance_url'] = $this->value_behance_url($aUser);
                    $dataArray['what_ive_built'] = $this->value_what_ive_built($aUser);
                    $dataArray['investor'] = $this->value_investor($aUser);
                    
                    return $dataArray;
                }
                private function value_name($aUser){
			if(isset($aUser->name))
				return $this->pruneValue($aUser->name);
			else
				return null;
		}
                private function value_bio($aUser){
			if(isset($aUser->bio))
				return $this->pruneValue($aUser->bio);
			else
				return null;
		}
                private function value_angellist_url($aUser){
			if(isset($aUser->angellist_url))
				return $this->pruneValue($aUser->angellist_url);
			else
				return null;
		}
                private function value_follower_count($aUser){
			if(isset($aUser->follower_count))
				return $this->pruneValue($aUser->follower_count);
			else
				return null;
		}
                private function value_image($aUser){
			if(isset($aUser->image))
				return $this->pruneValue($aUser->image);
			else
				return null;
		}
                private function value_blog_url($aUser){
			if(isset($aUser->blog_url))
				return $this->pruneValue($aUser->blog_url);
			else
				return null;
		}
                private function value_online_bio_url($aUser){
			if(isset($aUser->online_bio_url))
				return $this->pruneValue($aUser->online_bio_url);
			else
				return null;
		}
                private function value_twitter_url($aUser){
			if(isset($aUser->twitter_url))
				return $this->pruneValue($aUser->twitter_url);
			else
				return null;
		}
                private function value_facebook_url($aUser){
			if(isset($aUser->facebook_url))
				return $this->pruneValue($aUser->facebook_url);
			else
				return null;
		}
                private function value_linkedin_url($aUser){
			if(isset($aUser->linkedin_url))
				return $this->pruneValue($aUser->linkedin_url);
			else
				return null;
		}
                private function value_aboutme_url($aUser){
			if(isset($aUser->aboutme_url))
				return $this->pruneValue($aUser->aboutme_url);
			else
				return null;
		}
                private function value_github_url($aUser){
			if(isset($aUser->github_url))
				return $this->pruneValue($aUser->github_url);
			else
				return null;
		}
                private function value_dribbble_url($aUser){
			if(isset($aUser->dribbble_url))
				return $this->pruneValue($aUser->dribbble_url);
			else
				return null;
		}
                private function value_behance_url($aUser){
			if(isset($aUser->behance_url))
				return $this->pruneValue($aUser->behance_url);
			else
				return null;
		}
                private function value_what_ive_built($aUser){
			if(isset($aUser->what_ive_built))
				return $this->pruneValue($aUser->what_ive_built);
			else
				return null;
		}
                private function value_investor($aUser){
			if(isset($aUser->investor)){
                            if($aUser->investor)
                                return 'true';
                            else
                                return 'false';
                        }
			else
				return null;
		}
                
                /**
                 * Retrieves information about 'roles'
                 * 
                 */
                private function value_roles($aUser){
                    $dataArray = array();
                    
                    if(isset($aUser->roles)){
                        
                        foreach($aUser->roles as $anItem){
                            $data['id'] = $this->value_role_id($anItem);
                            $data['tag_type'] = $this->value_tag_type($anItem);
                            $data['name'] = $this->value_role_name($anItem);
                            $data['display_name'] = $this->value_roles_display_name($anItem);
                                    
                            array_push($dataArray, $data);
                        }
                    }
                                        
                    return $dataArray;
                }
                private function value_role_id($anItem){
			if(isset($anItem->id))
				return $this->pruneValue($anItem->id);
			else
				return null;
		}
                private function value_tag_type($anItem){
			if(isset($anItem->tag_type))
				return $this->pruneValue($anItem->tag_type);
			else
				return null;
		}
                private function value_role_name($anItem){
			if(isset($anItem->name))
				return $this->pruneValue($anItem->name);
			else
				return null;
		}
                private function value_roles_display_name($anItem){
			if(isset($anItem->name))
				return $this->pruneValue($anItem->name);
			else
				return null;
		}
                
                
                /**
                 * Retrieves information about 'skills'
                 * 
                 */
                private function value_skills($aUser){
                    $dataArray = array();
                    
                    if(isset($aUser->skills)){
                        
                        foreach($aUser->skills as $anItem){
                            $data['id'] = $this->value_skills_id($anItem);
                            $data['tag_type'] = $this->value_skills_tag_type($anItem);
                            $data['name'] = $this->value_skills_name($anItem);
                            $data['display_name'] = $this->value_skills_display_name($anItem);
                            $data['level'] = $this->value_skills_level($anItem);
                            array_push($dataArray, $data);
                        }
                    }
                                        
                    return $dataArray;
                }
                private function value_skills_id($anItem){
			if(isset($anItem->id))
				return $this->pruneValue($anItem->id);
			else
				return null;
		}
                private function value_skills_tag_type($anItem){
			if(isset($anItem->tag_type))
				return $this->pruneValue($anItem->tag_type);
			else
				return null;
		}
                private function value_skills_name($anItem){
			if(isset($anItem->name))
				return $this->pruneValue($anItem->name);
			else
				return null;
		}
                private function value_skills_display_name($anItem){
			if(isset($anItem->display_name))
				return $this->pruneValue($anItem->display_name);
			else
				return null;
		}
                private function value_skills_level($anItem){
			if(isset($anItem->level))
				return $this->pruneValue($anItem->level);
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
                    //$value = mysql_real_escape_string($value);
                    return $value;
                }
	}
}
