<?php

namespace AlgorithmsIO{

	class LinkedIn{
		
		private $access_token = null;
		
		private $keys = array('id','firstName','lastName','headline','industry','location_country_code','location_name','numConnections','numConnectionsCapped','pictureUrl','positions_1_company_id','positions_1_company_industry','positions_1_company_name','positions_1_company_size','positions_1_company_type','positions_1_company_isCurrent','positions_1_company_startDate','positions_1_company_title','positions_2_company_id','positions_2_company_industry','positions_2_company_name','positions_2_company_size','positions_2_company_type','positions_2_company_isCurrent','positions_2_company_startDate','positions_2_company_title','positions_3_company_id','positions_3_company_industry','positions_3_company_name','positions_3_company_size','positions_3_company_type','positions_3_company_isCurrent','positions_3_company_startDate','positions_3_company_title','positions_4_company_id','positions_4_company_industry','positions_4_company_name','positions_4_company_size','positions_4_company_type','positions_4_company_isCurrent','positions_4_company_startDate','positions_4_company_title');//,'positions_4','publicProfileUrl','specialties');
	
		public function __construct(){}
		public function setAccessToken($access_token){
			$this->access_token = $access_token;
		}
		public function fetch($method, $resource, $body = '') {
		    $params = array('oauth2_access_token' => $this->access_token,
		                    'format' => 'json',
		              );
		     
		    // Need to use HTTPS
		    $url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
		    // Tell streams to make a (GET, POST, PUT, or DELETE) request
		    $context = stream_context_create(
		                    array('http' => 
		                        array('method' => $method,
		                        )
		                    )
		                );
		 
		    // Hocus Pocus
		    $response = file_get_contents($url, false, $context);
		
		    // Native PHP object, please
		    return json_decode($response);
		}
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
		private function value_id($aUser){
			if(isset($aUser->id))
				return $aUser->id;
			else
				return null;
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
		private function value_positions_1_company_id($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[0])){
						if(isset($aUser->positions->values[0]->company)){
							if(isset($aUser->positions->values[0]->company->id))
								$value = $aUser->positions->values[0]->company->id;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_1_company_industry($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[0])){
						if(isset($aUser->positions->values[0]->company)){
							if(isset($aUser->positions->values[0]->company->industry))
								$value = $aUser->positions->values[0]->company->industry;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_1_company_name($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[0])){
						if(isset($aUser->positions->values[0]->company)){
							if(isset($aUser->positions->values[0]->company->name))
								$value = $aUser->positions->values[0]->company->name;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_1_company_size($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[0])){
						if(isset($aUser->positions->values[0]->company)){
							if(isset($aUser->positions->values[0]->company->size))
								$value = $aUser->positions->values[0]->company->size;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_1_company_type($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[0])){
						if(isset($aUser->positions->values[0]->company)){
							if(isset($aUser->positions->values[0]->company->type))
								$value = $aUser->positions->values[0]->company->type;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_1_company_isCurrent($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[0])){
						if(isset($aUser->positions->values[0]->isCurrent))
							$value = $aUser->positions->values[0]->isCurrent;
					}
						
				}
			}
			return $value;
		}
		private function value_positions_1_company_startDate($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[0])){
						if(isset($aUser->positions->values[0]->startDate)){
							if(isset($aUser->positions->values[0]->startDate->year))
								$value = $aUser->positions->values[0]->startDate->year;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_1_company_title($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[0])){
						if(isset($aUser->positions->values[0]->title))
							$value = $aUser->positions->values[0]->title;
					}
						
				}
			}
			return $value;
		}
		private function value_positions_2_company_id($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[1])){
						if(isset($aUser->positions->values[1]->company)){
							if(isset($aUser->positions->values[1]->company->id))
								$value = $aUser->positions->values[1]->company->id;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_2_company_industry($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[1])){
						if(isset($aUser->positions->values[1]->company)){
							if(isset($aUser->positions->values[1]->company->industry))
								$value = $aUser->positions->values[1]->company->industry;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_2_company_name($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[1])){
						if(isset($aUser->positions->values[1]->company)){
							if(isset($aUser->positions->values[1]->company->name))
								$value = $aUser->positions->values[1]->company->name;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_2_company_size($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[1])){
						if(isset($aUser->positions->values[1]->company)){
							if(isset($aUser->positions->values[1]->company->size))
								$value = $aUser->positions->values[1]->company->size;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_2_company_type($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[1])){
						if(isset($aUser->positions->values[1]->company)){
							if(isset($aUser->positions->values[1]->company->type))
								$value = $aUser->positions->values[1]->company->type;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_2_company_isCurrent($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[1])){
						if(isset($aUser->positions->values[1]->isCurrent))
							$value = $aUser->positions->values[1]->isCurrent;
					}
						
				}
			}
			return $value;
		}
		private function value_positions_2_company_startDate($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[1])){
						if(isset($aUser->positions->values[1]->startDate)){
							if(isset($aUser->positions->values[1]->startDate->year))
								$value = $aUser->positions->values[1]->startDate->year;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_2_company_title($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[1])){
						if(isset($aUser->positions->values[1]->title))
							$value = $aUser->positions->values[1]->title;
					}
						
				}
			}
			return $value;
		}
		private function value_positions_3_company_id($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[2])){
						if(isset($aUser->positions->values[2]->company)){
							if(isset($aUser->positions->values[2]->company->id))
								$value = $aUser->positions->values[2]->company->id;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_3_company_industry($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[2])){
						if(isset($aUser->positions->values[2]->company)){
							if(isset($aUser->positions->values[2]->company->industry))
								$value = $aUser->positions->values[2]->company->industry;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_3_company_name($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[2])){
						if(isset($aUser->positions->values[2]->company)){
							if(isset($aUser->positions->values[2]->company->name))
								$value = $aUser->positions->values[2]->company->name;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_3_company_size($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[2])){
						if(isset($aUser->positions->values[2]->company)){
							if(isset($aUser->positions->values[2]->company->size))
								$value = $aUser->positions->values[2]->company->size;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_3_company_type($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[2])){
						if(isset($aUser->positions->values[2]->company)){
							if(isset($aUser->positions->values[2]->company->type))
								$value = $aUser->positions->values[2]->company->type;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_3_company_isCurrent($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[2])){
						if(isset($aUser->positions->values[2]->isCurrent))
							$value = $aUser->positions->values[2]->isCurrent;
					}
						
				}
			}
			return $value;
		}
		private function value_positions_3_company_startDate($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[2])){
						if(isset($aUser->positions->values[2]->startDate)){
							if(isset($aUser->positions->values[2]->startDate->year))
								$value = $aUser->positions->values[2]->startDate->year;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_3_company_title($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[2])){
						if(isset($aUser->positions->values[2]->title))
							$value = $aUser->positions->values[2]->title;
					}
						
				}
			}
			return $value;
		}
		private function value_positions_4_company_id($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[3])){
						if(isset($aUser->positions->values[3]->company)){
							if(isset($aUser->positions->values[3]->company->id))
								$value = $aUser->positions->values[3]->company->id;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_4_company_industry($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[3])){
						if(isset($aUser->positions->values[3]->company)){
							if(isset($aUser->positions->values[3]->company->industry))
								$value = $aUser->positions->values[3]->company->industry;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_4_company_name($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[3])){
						if(isset($aUser->positions->values[3]->company)){
							if(isset($aUser->positions->values[3]->company->name))
								$value = $aUser->positions->values[3]->company->name;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_4_company_size($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[3])){
						if(isset($aUser->positions->values[3]->company)){
							if(isset($aUser->positions->values[3]->company->size))
								$value = $aUser->positions->values[3]->company->size;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_4_company_type($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[3])){
						if(isset($aUser->positions->values[3]->company)){
							if(isset($aUser->positions->values[3]->company->type))
								$value = $aUser->positions->values[3]->company->type;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_4_company_isCurrent($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[3])){
						if(isset($aUser->positions->values[3]->isCurrent))
							$value = $aUser->positions->values[3]->isCurrent;
					}
						
				}
			}
			return $value;
		}
		private function value_positions_4_company_startDate($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[3])){
						if(isset($aUser->positions->values[3]->startDate)){
							if(isset($aUser->positions->values[3]->startDate->year))
								$value = $aUser->positions->values[3]->startDate->year;
						}
					}
						
				}
			}
			return $value;
		}
		private function value_positions_4_company_title($aUser){
			$value = null;
			if(isset($aUser->positions)){
				if(isset($aUser->positions->values)){
					if(isset($aUser->positions->values[3])){
						if(isset($aUser->positions->values[3]->title))
							$value = $aUser->positions->values[3]->title;
					}
						
				}
			}
			return $value;
		}
	}
}
