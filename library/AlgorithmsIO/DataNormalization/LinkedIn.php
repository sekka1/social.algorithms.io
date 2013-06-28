<?php
/**
 * Garland
 * This class is used to retrieve values from a LinkedIn API user profile call.  
 * It will check if the value is there and if it is, it will retrieve that value and return it
 * in a normalized array.
 * 
 */
namespace AlgorithmsIO\DataNormalization{

	class LinkedIn{
		
		private $access_token = null;
		
		//private $keys = array('id','firstName','lastName','headline','industry','location_country_code','location_name','numConnections','numConnectionsCapped','pictureUrl','positions_1_company_id','positions_1_company_industry','positions_1_company_name','positions_1_company_size','positions_1_company_type','positions_1_company_isCurrent','positions_1_company_startDate','positions_1_company_title','positions_2_company_id','positions_2_company_industry','positions_2_company_name','positions_2_company_size','positions_2_company_type','positions_2_company_isCurrent','positions_2_company_startDate','positions_2_company_title','positions_3_company_id','positions_3_company_industry','positions_3_company_name','positions_3_company_size','positions_3_company_type','positions_3_company_isCurrent','positions_3_company_startDate','positions_3_company_title','positions_4_company_id','positions_4_company_industry','positions_4_company_name','positions_4_company_size','positions_4_company_type','positions_4_company_isCurrent','positions_4_company_startDate','positions_4_company_title');//,'positions_4','publicProfileUrl','specialties');
                private $keys = array('source_uid',
							'person',
							//'other_handles',
							//'data_meta_data', // Data about this data, created, update, etc
							//'educations',
							'employments'
						);
                
		public function __construct(){}
		public function setAccessToken($access_token){
			$this->access_token = $access_token;
		}
		
		/**
		 * Retrieves the list of values for a LinkedIn User Profile API call specified in the $this->keys array.
		 * 
		 * @param array $aUser
		 * @return array
		 */
		public function getUsersValues($aUser){
			$value = array();
			foreach($this->keys as $aKey){
				$function = 'value_'.$aKey;
				$value[$aKey] = $this->$function($aUser);//value_.$aKey($aUser);
			}
			return $value;
		}
		
		/**
		 * Getting Values out of a users profile
		 * 
		 * Only can get basic profile information if it is there from 1st degree users
		 * http://developer.linkedin.com/documents/profile-fields#profile
		 * 
		 * 
		 */
                
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
		 * 
		 */
		private function value_person($aUser){
			$dataArray = array();
			$dataArray['firstName'] = $this->value_firstName($aUser);
			$dataArray['lastName'] = $this->value_lastName($aUser);
			$dataArray['headline'] = $this->value_headline($aUser);
                        $dataArray['industry'] = $this->value_industry($aUser);
                        $dataArray['location_country_code'] = $this->value_location_country_code($aUser);
                        $dataArray['location_name'] = $this->value_location_name($aUser);
                        $dataArray['numConnections'] = $this->value_numConnections($aUser);
                        $dataArray['numConnectionsCapped'] = $this->value_numConnectionsCapped($aUser);
                        $dataArray['pictureUrl'] = $this->value_pictureUrl($aUser);
                        $dataArray['specialties'] = $this->value_specialties($aUser);
			$dataArray['summary'] = $this->value_summary($aUser);
                        
			return $dataArray;
		}
                
		private function value_firstName($aUser){
			if(isset($aUser->firstName))
				return $aUser->firstName;
			else
				return null;
		}
		private function value_headline($aUser){
			if(isset($aUser->headline))
				return $aUser->headline;
			else
				return null;
		}
		private function value_lastName($aUser){
			if(isset($aUser->lastName))
				return $aUser->lastName;
			else
				return null;
		}
		private function value_industry($aUser){
			if(isset($aUser->industry))
				return $aUser->industry;
			else
				return null;
		}
		private function value_location_country_code($aUser){
			$value = null;
			if(isset($aUser->location)){
				if(isset($aUser->location->country)){
					if(isset($aUser->location->country->code))
						$value = $aUser->location->country->code;
				}
			}
			return $value;
		}
		private function value_location_name($aUser){
			$value = null;
			if(isset($aUser->location)){
				if(isset($aUser->location->name))
					$value = $aUser->location->name;
			}
			return $value;
		}
		private function value_numConnections($aUser){
			if(isset($aUser->numConnections))
				return $aUser->numConnections;
			else
				return null;
		}
		private function value_numConnectionsCapped($aUser){
			if(isset($aUser->numConnectionsCapped))
				return $aUser->numConnectionsCapped;
			else
				return null;
		}
		private function value_pictureUrl($aUser){
			if(isset($aUser->pictureUrl))
				return $aUser->pictureUrl;
			else
				return null;
		}
                private function value_specialties($aUser){
			if(isset($aUser->specialties))
				return $aUser->specialties;
			else
				return null;
		}
                private function value_summary($aUser){
			if(isset($aUser->summary))
				return $aUser->summary;
			else
				return null;
		}
                
                /**
		 * Retrieves all the education and returns an array of it
		 */
		private function value_employments($aUser){
                    $dataArray = array();
                    if(isset($aUser->positions)){
                        if(isset($aUser->positions->values)){
                            foreach($aUser->positions->values as $anItem){
                                $data['position_id'] = $this->value_position_id($anItem);
                                $data['company_id'] = $this->value_positions_company_id($anItem);
                                $data['industry'] = $this->value_positions_company_industry($anItem);
                                $data['name'] = $this->value_positions_company_name($anItem);
                                $data['size'] = $this->value_positions_company_size($anItem);
                                $data['type'] = $this->value_positions_company_type($anItem);
                                $data['is_current'] = $this->value_positions_company_isCurrent($anItem);
                                $data['start_date_year'] = $this->value_positions_company_startDate_year($anItem);
                                $data['start_date_month'] = $this->value_positions_company_startDate_month($anItem);
                                $data['title'] = $this->value_positions_company_title($anItem);
                                array_push($dataArray, $data);
                            }
                        }
                    }
                    return $dataArray;
                }
                
                // change the function to take in the sub path of the value array
                
                // Need to make unit tests for these classes
                
                private function value_position_id($aUser){
			$value = null;
                       
			if(isset($aUser->id)){
                            $value = $aUser->id;
                        }
			return $value;
		}
		private function value_positions_company_id($aUser){
			$value = null;
                       
			if(isset($aUser->company)){
                            if(isset($aUser->company->id)){
                                $value = $aUser->company->id;
                            }
                        }
			return $value;
		}
		private function value_positions_company_industry($aUser){
			$value = null;
			
			if(isset($aUser->company)){
                            if(isset($aUser->company->industry))
                                $value = $aUser->company->industry;
                            
                        }
			return $value;
		}
		private function value_positions_company_name($aUser){
			$value = null;
			
			if(isset($aUser->company)){
                            if(isset($aUser->company->name))
                                $value = $aUser->company->name;
                            
                        }
			return $value;
		}
		private function value_positions_company_size($aUser){
			$value = null;
			
			if(isset($aUser->company)){
                            if(isset($aUser->company->size))
                                $value = $aUser->company->size;
                            
                        }
			return $value;
		}
		private function value_positions_company_type($aUser){
			$value = null;
			
			if(isset($aUser->company)){
                            if(isset($aUser->company->type))
                                $value = $aUser->company->type;
                            
                        }
			return $value;
		}
		private function value_positions_company_isCurrent($aUser){
			$value = null;
			
			if(isset($aUser->isCurrent))
                            $value = $aUser->isCurrent;
					
			return $value;
		}
		private function value_positions_company_startDate_year($aUser){
			$value = null;
			
			if(isset($aUser->startDate)){
                            if(isset($aUser->startDate->year))
                                $value = $aUser->startDate->year;
                            
                        }
			return $value;
		}
                private function value_positions_company_startDate_month($aUser){
			$value = null;
			
			if(isset($aUser->startDate)){
                            if(isset($aUser->startDate->month))
                                $value = $aUser->startDate->month;
                            
                        }
			return $value;
		}
		private function value_positions_company_title($aUser){
			$value = null;
			
			if(isset($aUser->title))
                            $value = $aUser->title;
					
			return $value;
		}
                
	}
}
