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
							'educations',
							'employments',
                                                        'employmentsPastThree',
                                                        'employmentsCurrentThree'
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
                        //$dataArray['specialties'] = $this->value_specialties($aUser);
			//$dataArray['summary'] = $this->value_summary($aUser);
                        $dataArray['emailAddress'] = $this->value_emailAddress($aUser);
                        $dataArray['honors'] = $this->value_honors($aUser);
                        $dataArray['publicProfileUrl'] = $this->value_publicProfileUrl($aUser);
                        $dataArray['twitter_handle_1'] = $this->value_twitterHandle($aUser,0);
                        $dataArray['twitter_id_1'] = $this->value_twitterID($aUser,0);
                        $dataArray['twitter_handle_2'] = $this->value_twitterHandle($aUser,1);
                        $dataArray['twitter_id_2'] = $this->value_twitterID($aUser,1);
                        
			return $dataArray;
		}
                
		private function value_firstName($aUser){
			if(isset($aUser->firstName))
				return $this->pruneValue($aUser->firstName);
			else
				return null;
		}
		private function value_headline($aUser){
			if(isset($aUser->headline))
				return $this->pruneValue($aUser->headline);
			else
				return null;
		}
		private function value_lastName($aUser){
			if(isset($aUser->lastName))
				return $this->pruneValue($aUser->lastName);
			else
				return null;
		}
		private function value_industry($aUser){
			if(isset($aUser->industry))
				return $this->pruneValue($aUser->industry);
			else
				return null;
		}
		private function value_location_country_code($aUser){
			$value = null;
			if(isset($aUser->location)){
				if(isset($aUser->location->country)){
					if(isset($aUser->location->country->code))
						$value = $this->pruneValue($aUser->location->country->code);
				}
			}
			return $value;
		}
		private function value_location_name($aUser){
			$value = null;
			if(isset($aUser->location)){
				if(isset($aUser->location->name))
					$value = $this->pruneValue($aUser->location->name);
			}
			return $value;
		}
		private function value_numConnections($aUser){
			if(isset($aUser->numConnections))
				return $this->pruneValue($aUser->numConnections);
			else
				return null;
		}
		private function value_numConnectionsCapped($aUser){
			if(isset($aUser->numConnectionsCapped))
				return $this->pruneValue($aUser->numConnectionsCapped);
			else
				return null;
		}
		private function value_pictureUrl($aUser){
			if(isset($aUser->pictureUrl))
				return $this->pruneValue($aUser->pictureUrl);
			else
				return null;
		}
                private function value_specialties($aUser){
			if(isset($aUser->specialties))
				return $this->pruneValue($aUser->specialties);
			else
				return null;
		}
                private function value_summary($aUser){
			if(isset($aUser->summary))
				return $this->pruneValue($aUser->summary);
			else
				return null;
		}
                private function value_emailAddress($aUser){
                    if(isset($aUser->emailAddress))
				return $this->pruneValue($aUser->emailAddress);
			else
				return null;
                }
                private function value_honors($aUser){
                    if(isset($aUser->honors))
				return $this->pruneValue($aUser->honors);
			else
				return null;
                }
                private function value_publicProfileUrl($aUser){
                    if(isset($aUser->publicProfileUrl))
				return $this->pruneValue($aUser->publicProfileUrl);
			else
				return null;
                }
                /**
                 * There can be more than one Twitter handle
                 * 
                 * @param type $aUser
                 * @param int $arrayPos
                 * @return type
                 */
                private function value_twitterHandle($aUser, $arrayPos){
                    $value=null;
                    if(isset($aUser->twitterAccounts)){
                        if(isset($aUser->twitterAccounts->values[$arrayPos])){
                            if(isset($aUser->twitterAccounts->values[$arrayPos]->providerAccountName))
                                $value = $this->pruneValue($aUser->twitterAccounts->values[$arrayPos]->providerAccountName);
                        }
                    }
                    return $value;  
                }
                /**
                 * There can be more than one Twitter handle
                 * 
                 * @param type $aUser
                 * @param int $arrayPos
                 * @return type
                 */
                private function value_twitterID($aUser, $arrayPos){
                    $value=null;
                    if(isset($aUser->twitterAccounts)){
                        if(isset($aUser->twitterAccounts->values[$arrayPos])){
                            if(isset($aUser->twitterAccounts->values[$arrayPos]->providerAccountId))
                                $value = $this->pruneValue($aUser->twitterAccounts->values[$arrayPos]->providerAccountId);
                        }
                    }
                    return $value;    
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
                /**
		 * Retrieves all the past three employment and returns an array of it
		 */
		private function value_employmentsPastThree($aUser){
                    $dataArray = array();
                    if(isset($aUser->threePastPositions)){
                        if(isset($aUser->threePastPositions->values)){
                            foreach($aUser->threePastPositions->values as $anItem){
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
                /**
		 * Retrieves all the current three employment and returns an array of it
		 */
		private function value_employmentsCurrentThree($aUser){
                    $dataArray = array();
                    if(isset($aUser->threeCurrentPositions)){
                        if(isset($aUser->threeCurrentPositions->values)){
                            foreach($aUser->threeCurrentPositions->values as $anItem){
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
                            $value = $this->pruneValue($aUser->id);
                        }
			return $value;
		}
		private function value_positions_company_id($aUser){
			$value = null;
                       
			if(isset($aUser->company)){
                            if(isset($aUser->company->id)){
                                $value = $this->pruneValue($aUser->company->id);
                            }
                        }
			return $value;
		}
		private function value_positions_company_industry($aUser){
			$value = null;
			
			if(isset($aUser->company)){
                            if(isset($aUser->company->industry))
                                $value = $this->pruneValue($aUser->company->industry);
                            
                        }
			return $value;
		}
		private function value_positions_company_name($aUser){
			$value = null;
			
			if(isset($aUser->company)){
                            if(isset($aUser->company->name))
                                $value = $this->pruneValue($aUser->company->name);
                            
                        }
			return $value;
		}
		private function value_positions_company_size($aUser){
			$value = null;
			
			if(isset($aUser->company)){
                            if(isset($aUser->company->size))
                                $value = $this->pruneValue($aUser->company->size);
                            
                        }
			return $value;
		}
		private function value_positions_company_type($aUser){
			$value = null;
			
			if(isset($aUser->company)){
                            if(isset($aUser->company->type))
                                $value = $this->pruneValue($aUser->company->type);
                            
                        }
			return $value;
		}
		private function value_positions_company_isCurrent($aUser){
			$value = null;
			
			if(isset($aUser->isCurrent))
                            $value = $this->pruneValue($aUser->isCurrent);
					
			return $value;
		}
		private function value_positions_company_startDate_year($aUser){
			$value = null;
			
			if(isset($aUser->startDate)){
                            if(isset($aUser->startDate->year))
                                $value = $this->pruneValue($aUser->startDate->year);
                            
                        }
			return $value;
		}
                private function value_positions_company_startDate_month($aUser){
			$value = null;
			
			if(isset($aUser->startDate)){
                            if(isset($aUser->startDate->month))
                                $value = $this->pruneValue($aUser->startDate->month);
                            
                        }
			return $value;
		}
		private function value_positions_company_title($aUser){
			$value = null;
			
			if(isset($aUser->title))
                            $value = $this->pruneValue($aUser->title);
					
			return $value;
		}

                
                
                
                /**
		 * Retrieves all the education and returns an array of it
		 */
		private function value_educations($aUser){
                    $dataArray = array();
                    if(isset($aUser->educations)){
                        if(isset($aUser->educations->values)){
                            foreach($aUser->educations->values as $anItem){
                                $data['activities'] = $this->get_edu_activities($anItem);
                                $data['degree'] = $this->get_edu_degree($anItem);         
                                $data['endDate'] = $this->get_edu_endDate($anItem); 
                                $data['fieldOfStudy'] = $this->get_edu_fieldOfStudy($anItem); 
                                $data['id'] = $this->get_edu_id($anItem); 
                                $data['notes'] = $this->get_edu_notes($anItem); 
                                $data['schoolName'] = $this->get_edu_schoolName($anItem); 
                                        
                                array_push($dataArray, $data);
                            }
                        }
                    }
                    return $dataArray;
                }
                private function get_edu_activities($data){
                    $value = null;
                    if(isset($data->activities))
                        $value = $this->pruneValue($data->activities);
                    return $value;
		}
                private function get_edu_degree($data){
                    $value = null;
                    if(isset($data->degree))
                        $value = $this->pruneValue($data->degree);
                    return $value;
		}
                private function get_edu_endDate($data){
                    $value = null;
                    if(isset($data->endDate)){
                        if(isset($data->endDate->year))
                            $value = $this->pruneValue($data->endDate->year);
                    }
                    return $value;
		}
                private function get_edu_fieldOfStudy($data){
                    $value = null;
                    if(isset($data->fieldOfStudy))
                        $value = $this->pruneValue($data->fieldOfStudy);
                    return $value;
		}
                private function get_edu_id($data){
                    $value = null;
                    if(isset($data->id))
                        $value = $this->pruneValue($data->id);
                    return $value;
		}
                private function get_edu_notes($data){
                    $value = null;
                    if(isset($data->notes))
                        $value = $this->pruneValue($data->notes);
                    return $value;
		}
                private function get_edu_schoolName($data){
                    $value = null;
                    if(isset($data->schoolName))
                        $value = $this->pruneValue($data->schoolName);
                    return $value;
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
                    $value = trim($value);
                    
                    // Replace tabs, line return etc
                    $order = array("\r\n", "\n", "\r", "\t");
                    $replace = '';
                    $value = str_replace($order, $replace, $value);
                    
                    $value = iconv("ISO-8859-1//IGNORE", "UTF-8", $value);
                    return $value;
                }
	}
}
