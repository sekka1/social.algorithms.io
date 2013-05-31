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
		
		// The list of keys names that we want to get out of the input
		//
		// Each of these keys has a path somewhere in the json object that is passed in.  That value is checked if it is there or not
		// before returning the value.
		//
		private $keys = array('id','firstName','lastName','crunchbase_url','homepage_url','birthplace','twitter_username','blog_url',
		'blog_feed_url','affiliation_name','born_year','born_month','born_day','tag_list','alias_list','created_at','updated_at','overview','image_url',
		'education_1_degree_type','education_1_subject','education_1_institution','education_1_graduated_year','education_1_graduated_month','education_1_graduated_day',
		'education_2_degree_type','education_2_subject','education_2_institution','education_2_graduated_year','education_2_graduated_month','education_2_graduated_day',
		'education_3_degree_type','education_3_subject','education_3_institution','education_3_graduated_year','education_3_graduated_month','education_3_graduated_day',
		'education_4_degree_type','education_4_subject','education_4_institution','education_4_graduated_year','education_4_graduated_month','education_4_graduated_day',
		'education_5_degree_type','education_5_subject','education_5_institution','education_5_graduated_year','education_5_graduated_month','education_5_graduated_day',
		'education_6_degree_type','education_6_subject','education_6_institution','education_6_graduated_year','education_6_graduated_month','education_6_graduated_day',
		'employment_1_is_past', 'employment_1_title', 'employment_1_firm_name', 'employment_1_firm_permalink', 'employment_1_firm_type_of_entity',
		'employment_2_is_past', 'employment_2_title', 'employment_2_firm_name', 'employment_2_firm_permalink', 'employment_2_firm_type_of_entity',
		'employment_3_is_past', 'employment_3_title', 'employment_3_firm_name', 'employment_3_firm_permalink', 'employment_3_firm_type_of_entity',
		'employment_4_is_past', 'employment_4_title', 'employment_4_firm_name', 'employment_4_firm_permalink', 'employment_4_firm_type_of_entity',
		'employment_5_is_past', 'employment_5_title', 'employment_5_firm_name', 'employment_5_firm_permalink', 'employment_5_firm_type_of_entity',
		'employment_6_is_past', 'employment_6_title', 'employment_6_firm_name', 'employment_6_firm_permalink', 'employment_6_firm_type_of_entity',
		'employment_7_is_past', 'employment_7_title', 'employment_7_firm_name', 'employment_7_firm_permalink', 'employment_7_firm_type_of_entity',
		'employment_8_is_past', 'employment_8_title', 'employment_8_firm_name', 'employment_8_firm_permalink', 'employment_8_firm_type_of_entity',
		'employment_9_is_past', 'employment_9_title', 'employment_9_firm_name', 'employment_9_firm_permalink', 'employment_9_firm_type_of_entity',
		'employment_10_is_past', 'employment_10_title', 'employment_10_firm_name', 'employment_10_firm_permalink', 'employment_10_firm_type_of_entity',
		'employment_11_is_past', 'employment_11_title', 'employment_11_firm_name', 'employment_11_firm_permalink', 'employment_11_firm_type_of_entity',
		'employment_12_is_past', 'employment_12_title', 'employment_12_firm_name', 'employment_12_firm_permalink', 'employment_12_firm_type_of_entity'
		
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
			$value = array();
			foreach($this->keys as $aKey){
				$function = 'value_'.$aKey;
				$value[$aKey] = $this->$function($aUser);
			}
			return $value;
		}
		/**
		 * Getting Values out of a users profile
		 * 
		 */
		private function value_id($aUser){
			if(isset($aUser->permalink))
				return $aUser->permalink;
			else
				return null;
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
		private function value_twitter_username($aUser){
			if(isset($aUser->twitter_username))
				return $aUser->twitter_username;
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
		private function value_education_1_degree_type($aUser){
			$value = null;
			if(isset($aUser->degrees[0])){
				if(isset($aUser->degrees[0]->degree_type)){
					return $aUser->degrees[0]->degree_type;
				}
			}
		}			
		private function value_education_1_subject($aUser){
			$value = null;
			if(isset($aUser->degrees[0])){
				if(isset($aUser->degrees[0]->degree_subject)){
					return $aUser->degrees[0]->degree_subject;
				}
			}
		}	
		private function value_education_1_institution($aUser){
			$value = null;
			if(isset($aUser->degrees[0])){
				if(isset($aUser->degrees[0]->institution)){
					return $aUser->degrees[0]->institution;
				}
			}
		}	
		private function value_education_1_graduated_year($aUser){
			$value = null;
			if(isset($aUser->degrees[0])){
				if(isset($aUser->degrees[0]->graduated_year)){
					return $aUser->degrees[0]->graduated_year;
				}
			}
		}			
		private function value_education_1_graduated_month($aUser){
			$value = null;
			if(isset($aUser->degrees[0])){
				if(isset($aUser->degrees[0]->graduated_month)){
					return $aUser->degrees[0]->graduated_month;
				}
			}
		}	
		private function value_education_1_graduated_day($aUser){
			$value = null;
			if(isset($aUser->degrees[0])){
				if(isset($aUser->degrees[0]->graduated_day)){
					return $aUser->degrees[0]->graduated_day;
				}
			}
		}		
		private function value_education_2_degree_type($aUser){
			$value = null;
			if(isset($aUser->degrees[1])){
				if(isset($aUser->degrees[1]->degree_type)){
					return $aUser->degrees[1]->degree_type;
				}
			}
		}			
		private function value_education_2_subject($aUser){
			$value = null;
			if(isset($aUser->degrees[1])){
				if(isset($aUser->degrees[1]->degree_subject)){
					return $aUser->degrees[1]->degree_subject;
				}
			}
		}	
		private function value_education_2_institution($aUser){
			$value = null;
			if(isset($aUser->degrees[1])){
				if(isset($aUser->degrees[1]->institution)){
					return $aUser->degrees[1]->institution;
				}
			}
		}	
		private function value_education_2_graduated_year($aUser){
			$value = null;
			if(isset($aUser->degrees[1])){
				if(isset($aUser->degrees[1]->graduated_year)){
					return $aUser->degrees[1]->graduated_year;
				}
			}
		}			
		private function value_education_2_graduated_month($aUser){
			$value = null;
			if(isset($aUser->degrees[1])){
				if(isset($aUser->degrees[1]->graduated_month)){
					return $aUser->degrees[1]->graduated_month;
				}
			}
		}	
		private function value_education_2_graduated_day($aUser){
			$value = null;
			if(isset($aUser->degrees[1])){
				if(isset($aUser->degrees[1]->graduated_day)){
					return $aUser->degrees[1]->graduated_day;
				}
			}
		}	
		private function value_education_3_degree_type($aUser){
			$value = null;
			if(isset($aUser->degrees[2])){
				if(isset($aUser->degrees[2]->degree_type)){
					return $aUser->degrees[2]->degree_type;
				}
			}
		}			
		private function value_education_3_subject($aUser){
			$value = null;
			if(isset($aUser->degrees[2])){
				if(isset($aUser->degrees[2]->degree_subject)){
					return $aUser->degrees[2]->degree_subject;
				}
			}
		}	
		private function value_education_3_institution($aUser){
			$value = null;
			if(isset($aUser->degrees[2])){
				if(isset($aUser->degrees[2]->institution)){
					return $aUser->degrees[2]->institution;
				}
			}
		}	
		private function value_education_3_graduated_year($aUser){
			$value = null;
			if(isset($aUser->degrees[2])){
				if(isset($aUser->degrees[2]->graduated_year)){
					return $aUser->degrees[2]->graduated_year;
				}
			}
		}			
		private function value_education_3_graduated_month($aUser){
			$value = null;
			if(isset($aUser->degrees[2])){
				if(isset($aUser->degrees[2]->graduated_month)){
					return $aUser->degrees[2]->graduated_month;
				}
			}
		}	
		private function value_education_3_graduated_day($aUser){
			$value = null;
			if(isset($aUser->degrees[2])){
				if(isset($aUser->degrees[2]->graduated_day)){
					return $aUser->degrees[2]->graduated_day;
				}
			}
		}		
		private function value_education_4_degree_type($aUser){
			$value = null;
			if(isset($aUser->degrees[3])){
				if(isset($aUser->degrees[3]->degree_type)){
					return $aUser->degrees[3]->degree_type;
				}
			}
		}			
		private function value_education_4_subject($aUser){
			$value = null;
			if(isset($aUser->degrees[3])){
				if(isset($aUser->degrees[3]->degree_subject)){
					return $aUser->degrees[3]->degree_subject;
				}
			}
		}	
		private function value_education_4_institution($aUser){
			$value = null;
			if(isset($aUser->degrees[3])){
				if(isset($aUser->degrees[3]->institution)){
					return $aUser->degrees[3]->institution;
				}
			}
		}	
		private function value_education_4_graduated_year($aUser){
			$value = null;
			if(isset($aUser->degrees[3])){
				if(isset($aUser->degrees[3]->graduated_year)){
					return $aUser->degrees[3]->graduated_year;
				}
			}
		}			
		private function value_education_4_graduated_month($aUser){
			$value = null;
			if(isset($aUser->degrees[3])){
				if(isset($aUser->degrees[3]->graduated_month)){
					return $aUser->degrees[3]->graduated_month;
				}
			}
		}	
		private function value_education_4_graduated_day($aUser){
			$value = null;
			if(isset($aUser->degrees[3])){
				if(isset($aUser->degrees[3]->graduated_day)){
					return $aUser->degrees[3]->graduated_day;
				}
			}
		}
		private function value_education_5_degree_type($aUser){
			$value = null;
			if(isset($aUser->degrees[4])){
				if(isset($aUser->degrees[4]->degree_type)){
					return $aUser->degrees[4]->degree_type;
				}
			}
		}			
		private function value_education_5_subject($aUser){
			$value = null;
			if(isset($aUser->degrees[4])){
				if(isset($aUser->degrees[4]->degree_subject)){
					return $aUser->degrees[4]->degree_subject;
				}
			}
		}	
		private function value_education_5_institution($aUser){
			$value = null;
			if(isset($aUser->degrees[4])){
				if(isset($aUser->degrees[4]->institution)){
					return $aUser->degrees[4]->institution;
				}
			}
		}	
		private function value_education_5_graduated_year($aUser){
			$value = null;
			if(isset($aUser->degrees[4])){
				if(isset($aUser->degrees[4]->graduated_year)){
					return $aUser->degrees[4]->graduated_year;
				}
			}
		}			
		private function value_education_5_graduated_month($aUser){
			$value = null;
			if(isset($aUser->degrees[4])){
				if(isset($aUser->degrees[4]->graduated_month)){
					return $aUser->degrees[4]->graduated_month;
				}
			}
		}	
		private function value_education_5_graduated_day($aUser){
			$value = null;
			if(isset($aUser->degrees[4])){
				if(isset($aUser->degrees[4]->graduated_day)){
					return $aUser->degrees[4]->graduated_day;
				}
			}
		}
		private function value_education_6_degree_type($aUser){
			$value = null;
			if(isset($aUser->degrees[5])){
				if(isset($aUser->degrees[5]->degree_type)){
					return $aUser->degrees[5]->degree_type;
				}
			}
		}			
		private function value_education_6_subject($aUser){
			$value = null;
			if(isset($aUser->degrees[5])){
				if(isset($aUser->degrees[5]->degree_subject)){
					return $aUser->degrees[5]->degree_subject;
				}
			}
		}	
		private function value_education_6_institution($aUser){
			$value = null;
			if(isset($aUser->degrees[5])){
				if(isset($aUser->degrees[5]->institution)){
					return $aUser->degrees[5]->institution;
				}
			}
		}	
		private function value_education_6_graduated_year($aUser){
			$value = null;
			if(isset($aUser->degrees[5])){
				if(isset($aUser->degrees[5]->graduated_year)){
					return $aUser->degrees[5]->graduated_year;
				}
			}
		}			
		private function value_education_6_graduated_month($aUser){
			$value = null;
			if(isset($aUser->degrees[5])){
				if(isset($aUser->degrees[5]->graduated_month)){
					return $aUser->degrees[5]->graduated_month;
				}
			}
		}	
		private function value_education_6_graduated_day($aUser){
			$value = null;
			if(isset($aUser->degrees[5])){
				if(isset($aUser->degrees[5]->graduated_day)){
					return $aUser->degrees[5]->graduated_day;
				}
			}
		}			
		
	
		
		private function value_employment_1_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[0])){
				if(isset($aUser->relationships[0]->is_past)){
					return $aUser->relationships[0]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_1_title($aUser){
			$value = null;
			if(isset($aUser->relationships[0])){
				if(isset($aUser->relationships[0]->title)){
					return $aUser->relationships[0]->title;
				}
			}
		}		
		private function value_employment_1_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[0])){
				if(isset($aUser->relationships[0]->firm)){
					if(isset($aUser->relationships[0]->firm->name)){
						return $aUser->relationships[0]->firm->name;
					}
				}
			}
		}		
		private function value_employment_1_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[0])){
				if(isset($aUser->relationships[0]->firm)){
					if(isset($aUser->relationships[0]->firm->permalink)){
						return $aUser->relationships[0]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_1_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[0])){
				if(isset($aUser->relationships[0]->firm)){
					if(isset($aUser->relationships[0]->firm->type_of_entity)){
						return $aUser->relationships[0]->firm->type_of_entity;
					}
				}
			}
		}	
		private function value_employment_2_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[1])){
				if(isset($aUser->relationships[1]->is_past)){
					return ($aUser->relationships[1]->is_past) ? 'true' : 'false';
				}
			}
		}
		private function value_employment_2_title($aUser){
			$value = null;
			if(isset($aUser->relationships[1])){
				if(isset($aUser->relationships[1]->title)){
					return $aUser->relationships[1]->title;
				}
			}
		}		
		private function value_employment_2_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[1])){
				if(isset($aUser->relationships[1]->firm)){
					if(isset($aUser->relationships[1]->firm->name)){
						return $aUser->relationships[1]->firm->name;
					}
				}
			}
		}		
		private function value_employment_2_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[1])){
				if(isset($aUser->relationships[1]->firm)){
					if(isset($aUser->relationships[1]->firm->permalink)){
						return $aUser->relationships[1]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_2_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[1])){
				if(isset($aUser->relationships[1]->firm)){
					if(isset($aUser->relationships[1]->firm->type_of_entity)){
						return $aUser->relationships[1]->firm->type_of_entity;
					}
				}
			}
		}	
		private function value_employment_3_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[2])){
				if(isset($aUser->relationships[2]->is_past)){
					return $aUser->relationships[2]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_3_title($aUser){
			$value = null;
			if(isset($aUser->relationships[2])){
				if(isset($aUser->relationships[2]->title)){
					return $aUser->relationships[2]->title;
				}
			}
		}		
		private function value_employment_3_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[2])){
				if(isset($aUser->relationships[2]->firm)){
					if(isset($aUser->relationships[2]->firm->name)){
						return $aUser->relationships[2]->firm->name;
					}
				}
			}
		}		
		private function value_employment_3_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[2])){
				if(isset($aUser->relationships[2]->firm)){
					if(isset($aUser->relationships[2]->firm->permalink)){
						return $aUser->relationships[2]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_3_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[2])){
				if(isset($aUser->relationships[2]->firm)){
					if(isset($aUser->relationships[2]->firm->type_of_entity)){
						return $aUser->relationships[2]->firm->type_of_entity;
					}
				}
			}
		}	
		private function value_employment_4_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[3])){
				if(isset($aUser->relationships[3]->is_past)){
					return $aUser->relationships[3]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_4_title($aUser){
			$value = null;
			if(isset($aUser->relationships[3])){
				if(isset($aUser->relationships[3]->title)){
					return $aUser->relationships[3]->title;
				}
			}
		}		
		private function value_employment_4_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[3])){
				if(isset($aUser->relationships[3]->firm)){
					if(isset($aUser->relationships[3]->firm->name)){
						return $aUser->relationships[3]->firm->name;
					}
				}
			}
		}		
		private function value_employment_4_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[3])){
				if(isset($aUser->relationships[3]->firm)){
					if(isset($aUser->relationships[3]->firm->permalink)){
						return $aUser->relationships[3]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_4_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[3])){
				if(isset($aUser->relationships[3]->firm)){
					if(isset($aUser->relationships[3]->firm->type_of_entity)){
						return $aUser->relationships[3]->firm->type_of_entity;
					}
				}
			}
		}
		private function value_employment_5_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[4])){
				if(isset($aUser->relationships[4]->is_past)){
					return $aUser->relationships[4]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_5_title($aUser){
			$value = null;
			if(isset($aUser->relationships[4])){
				if(isset($aUser->relationships[4]->title)){
					return $aUser->relationships[4]->title;
				}
			}
		}		
		private function value_employment_5_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[4])){
				if(isset($aUser->relationships[4]->firm)){
					if(isset($aUser->relationships[4]->firm->name)){
						return $aUser->relationships[4]->firm->name;
					}
				}
			}
		}		
		private function value_employment_5_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[4])){
				if(isset($aUser->relationships[4]->firm)){
					if(isset($aUser->relationships[4]->firm->permalink)){
						return $aUser->relationships[4]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_5_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[4])){
				if(isset($aUser->relationships[4]->firm)){
					if(isset($aUser->relationships[4]->firm->type_of_entity)){
						return $aUser->relationships[4]->firm->type_of_entity;
					}
				}
			}
		}
		private function value_employment_6_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[5])){
				if(isset($aUser->relationships[5]->is_past)){
					return $aUser->relationships[5]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_6_title($aUser){
			$value = null;
			if(isset($aUser->relationships[5])){
				if(isset($aUser->relationships[5]->title)){
					return $aUser->relationships[5]->title;
				}
			}
		}		
		private function value_employment_6_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[5])){
				if(isset($aUser->relationships[5]->firm)){
					if(isset($aUser->relationships[5]->firm->name)){
						return $aUser->relationships[5]->firm->name;
					}
				}
			}
		}		
		private function value_employment_6_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[5])){
				if(isset($aUser->relationships[5]->firm)){
					if(isset($aUser->relationships[5]->firm->permalink)){
						return $aUser->relationships[5]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_6_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[5])){
				if(isset($aUser->relationships[5]->firm)){
					if(isset($aUser->relationships[5]->firm->type_of_entity)){
						return $aUser->relationships[5]->firm->type_of_entity;
					}
				}
			}
		}	
		private function value_employment_7_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[6])){
				if(isset($aUser->relationships[6]->is_past)){
					return $aUser->relationships[6]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_7_title($aUser){
			$value = null;
			if(isset($aUser->relationships[6])){
				if(isset($aUser->relationships[6]->title)){
					return $aUser->relationships[6]->title;
				}
			}
		}		
		private function value_employment_7_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[6])){
				if(isset($aUser->relationships[6]->firm)){
					if(isset($aUser->relationships[6]->firm->name)){
						return $aUser->relationships[6]->firm->name;
					}
				}
			}
		}		
		private function value_employment_7_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[6])){
				if(isset($aUser->relationships[6]->firm)){
					if(isset($aUser->relationships[6]->firm->permalink)){
						return $aUser->relationships[6]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_7_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[6])){
				if(isset($aUser->relationships[6]->firm)){
					if(isset($aUser->relationships[6]->firm->type_of_entity)){
						return $aUser->relationships[6]->firm->type_of_entity;
					}
				}
			}
		}
		private function value_employment_8_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[7])){
				if(isset($aUser->relationships[7]->is_past)){
					return $aUser->relationships[7]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_8_title($aUser){
			$value = null;
			if(isset($aUser->relationships[7])){
				if(isset($aUser->relationships[7]->title)){
					return $aUser->relationships[7]->title;
				}
			}
		}		
		private function value_employment_8_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[7])){
				if(isset($aUser->relationships[7]->firm)){
					if(isset($aUser->relationships[7]->firm->name)){
						return $aUser->relationships[7]->firm->name;
					}
				}
			}
		}		
		private function value_employment_8_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[7])){
				if(isset($aUser->relationships[7]->firm)){
					if(isset($aUser->relationships[7]->firm->permalink)){
						return $aUser->relationships[7]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_8_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[7])){
				if(isset($aUser->relationships[7]->firm)){
					if(isset($aUser->relationships[7]->firm->type_of_entity)){
						return $aUser->relationships[7]->firm->type_of_entity;
					}
				}
			}
		}
		private function value_employment_9_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[8])){
				if(isset($aUser->relationships[8]->is_past)){
					return $aUser->relationships[8]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_9_title($aUser){
			$value = null;
			if(isset($aUser->relationships[8])){
				if(isset($aUser->relationships[8]->title)){
					return $aUser->relationships[8]->title;
				}
			}
		}		
		private function value_employment_9_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[8])){
				if(isset($aUser->relationships[8]->firm)){
					if(isset($aUser->relationships[8]->firm->name)){
						return $aUser->relationships[8]->firm->name;
					}
				}
			}
		}		
		private function value_employment_9_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[8])){
				if(isset($aUser->relationships[8]->firm)){
					if(isset($aUser->relationships[8]->firm->permalink)){
						return $aUser->relationships[8]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_9_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[8])){
				if(isset($aUser->relationships[8]->firm)){
					if(isset($aUser->relationships[8]->firm->type_of_entity)){
						return $aUser->relationships[8]->firm->type_of_entity;
					}
				}
			}
		}
		private function value_employment_10_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[9])){
				if(isset($aUser->relationships[9]->is_past)){
					return $aUser->relationships[9]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_10_title($aUser){
			$value = null;
			if(isset($aUser->relationships[9])){
				if(isset($aUser->relationships[9]->title)){
					return $aUser->relationships[9]->title;
				}
			}
		}		
		private function value_employment_10_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[9])){
				if(isset($aUser->relationships[9]->firm)){
					if(isset($aUser->relationships[9]->firm->name)){
						return $aUser->relationships[9]->firm->name;
					}
				}
			}
		}		
		private function value_employment_10_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[9])){
				if(isset($aUser->relationships[9]->firm)){
					if(isset($aUser->relationships[9]->firm->permalink)){
						return $aUser->relationships[9]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_10_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[9])){
				if(isset($aUser->relationships[9]->firm)){
					if(isset($aUser->relationships[9]->firm->type_of_entity)){
						return $aUser->relationships[9]->firm->type_of_entity;
					}
				}
			}
		}
		private function value_employment_11_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[10])){
				if(isset($aUser->relationships[10]->is_past)){
					return $aUser->relationships[10]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_11_title($aUser){
			$value = null;
			if(isset($aUser->relationships[10])){
				if(isset($aUser->relationships[10]->title)){
					return $aUser->relationships[10]->title;
				}
			}
		}		
		private function value_employment_11_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[10])){
				if(isset($aUser->relationships[10]->firm)){
					if(isset($aUser->relationships[10]->firm->name)){
						return $aUser->relationships[10]->firm->name;
					}
				}
			}
		}		
		private function value_employment_11_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[10])){
				if(isset($aUser->relationships[10]->firm)){
					if(isset($aUser->relationships[10]->firm->permalink)){
						return $aUser->relationships[10]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_11_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[10])){
				if(isset($aUser->relationships[10]->firm)){
					if(isset($aUser->relationships[10]->firm->type_of_entity)){
						return $aUser->relationships[10]->firm->type_of_entity;
					}
				}
			}
		}
		private function value_employment_12_is_past($aUser){
			$value = null;
			if(isset($aUser->relationships[11])){
				if(isset($aUser->relationships[11]->is_past)){
					return $aUser->relationships[11]->is_past ? 'true' : 'false';
				}
			}
		}
		private function value_employment_12_title($aUser){
			$value = null;
			if(isset($aUser->relationships[11])){
				if(isset($aUser->relationships[11]->title)){
					return $aUser->relationships[11]->title;
				}
			}
		}		
		private function value_employment_12_firm_name($aUser){
			$value = null;
			if(isset($aUser->relationships[11])){
				if(isset($aUser->relationships[11]->firm)){
					if(isset($aUser->relationships[11]->firm->name)){
						return $aUser->relationships[11]->firm->name;
					}
				}
			}
		}		
		private function value_employment_12_firm_permalink($aUser){
			$value = null;
			if(isset($aUser->relationships[11])){
				if(isset($aUser->relationships[11]->firm)){
					if(isset($aUser->relationships[11]->firm->permalink)){
						return $aUser->relationships[11]->firm->permalink;
					}
				}
			}
		}	
		private function value_employment_12_firm_type_of_entity($aUser){
			$value = null;
			if(isset($aUser->relationships[11])){
				if(isset($aUser->relationships[11]->firm)){
					if(isset($aUser->relationships[11]->firm->type_of_entity)){
						return $aUser->relationships[11]->firm->type_of_entity;
					}
				}
			}
		}			


	
			
		
		
		
						
	}
}
