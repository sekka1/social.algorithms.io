<?php
/**
 * Garland
 * This class is used to retrieve values from a CrunchBase API user profile call.  
 * It will check if the value is there and if it is, it will retrieve that value and return it
 * in a normalized array.
 * 
 */
namespace AlgorithmsIO\DataNormalization{

	class CrunchBaseCompany{
		
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
							'company',
							'data_meta_data', // Data about this data, created, update, etc
							'funding'
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
		private function value_company($aUser){
			$dataArray = array();
                        $dataArray['crunchbase_url'] = $this->value_crunchbase_url($aUser);
                        $dataArray['homepage_url'] = $this->value_homepage_url($aUser);
			$dataArray['blog_url'] = $this->value_blog_url($aUser);
			$dataArray['blog_feed_url'] = $this->value_blog_feed_url($aUser);
			$dataArray['tag_list'] = $this->value_tag_list($aUser);
			$dataArray['alias_list'] = $this->value_alias_list($aUser);
			$dataArray['image_url'] = $this->value_image_url($aUser);
                        $dataArray['overview'] = $this->value_overview($aUser);
                        $dataArray['total_money_raised'] = $this->value_total_money_raised($aUser);
                        $dataArray['twitter_username'] = $this->value_twitter_username($aUser);
                        $dataArray['number_of_employees'] = $this->value_number_of_employees($aUser);
                        
                        // Founded dates
                        $dataArray['founded_year'] = $this->value_founded_year($aUser);
                        $dataArray['founded_month'] = $this->value_founded_month($aUser);
                        $dataArray['founded_day'] = $this->value_founded_day($aUser);
                                
                        // Deadpool dates
                        $dataArray['deadpooled_year'] = $this->value_deadpooled_year($aUser);
                        $dataArray['deadpooled_month'] = $this->value_deadpooled_month($aUser);
                        $dataArray['deadpooled_day'] = $this->value_deadpooled_day($aUser);
			
			return $dataArray;
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
                        return $value;
		}
                private function value_total_money_raised($aUser){
  			if(isset($aUser->total_money_raised))
				return $aUser->total_money_raised;
			else
				return null;                  
                }
                private function value_twitter_username($aUser){
                    if(isset($aUser->twitter_username))
				return $aUser->twitter_username;
			else
				return null; 
                }
                private function value_number_of_employees($aUser){
                    if(isset($aUser->number_of_employees))
				return $aUser->number_of_employees;
			else
				return null; 
                }
                private function value_founded_year($aUser){
                    if(isset($aUser->founded_year))
				return $aUser->founded_year;
			else
				return null; 
                }
                private function value_founded_month($aUser){
                    if(isset($aUser->founded_month))
				return $aUser->founded_month;
			else
				return null; 
                }
                private function value_founded_day($aUser){
                    if(isset($aUser->founded_day))
				return $aUser->founded_day;
			else
				return null; 
                }
                private function value_deadpooled_year($aUser){
                    if(isset($aUser->deadpooled_year))
				return $aUser->deadpooled_year;
			else
				return null; 
                }
                private function value_deadpooled_month($aUser){
                    if(isset($aUser->deadpooled_month))
				return $aUser->deadpooled_month;
			else
				return null; 
                }
                private function value_deadpooled_day($aUser){
                    if(isset($aUser->deadpooled_day))
				return $aUser->deadpooled_day;
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
                 * Retrieves all funding round
                 * 
                 */
                private function value_funding($aUser){
                    $dataArray = array();
                    if(isset($aUser->funding_rounds) && (count($aUser->funding_rounds)>0)){
                        foreach($aUser->funding_rounds as $anItem){
                            $data['round'] = $this->value_funding_round($anItem);
                            $data['raised_amount'] = $this->value_funding_raised_amount($anItem);
                            $data['raised_currency'] = $this->value_funding_currency($anItem);
                            $data['funding_year'] = $this->value_funding_year($anItem);
                            $data['funding_month'] = $this->value_funding_month($anItem);
                            $data['funding_date'] = $this->value_funding_day($anItem);
                            
                            // Get Investors
                            if(isset($anItem->investments)){
                                $data['investors'] = $this->getInvestors($anItem->investments);
                            }
                            
                            array_push($dataArray, $data);
                        }
                    }
                    return $dataArray;
                }
                /**
                 * Get investors foreah funding
                 * 
                 * @param jsonObj $investors the investments array from the funding_rounds
                 */
                private function getInvestors($investors){
                    $dataArray = array();
                    foreach($investors as $anItem){
                                    $data['name'] = $anItem->financial_org->name;
                                    $data['permalink'] = $anItem->financial_org->permalink;
                                    array_push($dataArray, $data);
                    }
                    return $dataArray;
                }
                private function value_funding_round($anItem){
			$value = null;
			if(isset($anItem->round_code)){
				return $anItem->round_code;
			}
                        return $value;
		}
                private function value_funding_raised_amount($anItem){
			$value = null;
			if(isset($anItem->raised_amount)){
				return $anItem->raised_amount;
			}
                        return $value;
		}
                private function value_funding_currency($anItem){
			$value = null;
			if(isset($anItem->raised_currency_code)){
				return $anItem->raised_currency_code;
			}
                        return $value;
		}
                private function value_funding_year($anItem){
			$value = null;
			if(isset($anItem->funded_year)){
				return $anItem->funded_year;
			}
                        return $value;
		}
                private function value_funding_month($anItem){
			$value = null;
			if(isset($anItem->funded_month)){
				return $anItem->funded_month;
			}
                        return $value;
		}
                private function value_funding_day($anItem){
			$value = null;
			if(isset($anItem->funded_day)){
				return $anItem->funded_day;
			}
                        return $value;
		}
		/**
                 * Will go through each of the arrays holding values and prune it
                 * 
                 * -lower case the value
                 * -remove '
                 * -remove "
                 */
		private function pruneValues(){
                  
                  // Company
                  foreach($this->allValues['company'] as $key=>$val){
                      $this->allValues['company'][$key] = strtolower(mysql_real_escape_string($val));
                  }
                  // Funding
                  for($i=0;$i<count($this->allValues['funding']);$i++){
                      if(!is_array($this->allValues['funding'][$i])){
                        foreach($this->allValues['funding'][$i] as $key=>$val){
                            // funding round info
                            $this->allValues['funding'][$i][$key] = strtolower(mysql_real_escape_string($val));
                        }
                      }else{
                            // Investor for each funding round - array
                            $investorsArray = $this->pruneInvestors($this->allValues['funding'][$i]['investors']);

                            $this->allValues['funding'][$i]['investors'] = $investorsArray; 
                    }
                  }
                  
                }
                /**
                 * Investor array to be pruned.  Should only be called from pruneValues()
                 * 
                 * @param array $investorsArray
                 * @return array
                 */
                private function pruneInvestors($investorsArray){
                    for($i=0;$i<count($investorsArray);$i++){
                        foreach($investorsArray[$i] as $key=>$val)
                            $investorsArray[$i][$key] = strtolower(mysql_real_escape_string($val));
                    }
                    return $investorsArray;
                }
	
						
	}
}
