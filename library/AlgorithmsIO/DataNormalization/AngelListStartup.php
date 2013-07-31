<?php
/**
 * 
 * This class is used to normalize values from a Angel List  Startup (aka company).  
 * It will check if the value is there and if it is, it will retrieve that value and return it
 * in a normalized array.
 * 
 * @author garland
 * 
 */
namespace AlgorithmsIO\DataNormalization{

	class AngelListStartup{
				
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
                                        'markets',
                                        'locations',
                                        'company_type',
                                        'fundraising'
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
			if(isset($data->id))
				return $data->id;
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
                    $dataArray['id'] = $this->value_source_uid($data);
                    $dataArray['name'] = $this->get_name($data);
                    $dataArray['angellist_url'] = $this->get_angellist_url($data);
                    $dataArray['logo_url'] = $this->get_logo_url($data);
                    $dataArray['thumb_url'] = $this->get_thumb_url($data);
                    $dataArray['launch_date'] = $this->get_launch_date($data);
                    $dataArray['quality'] = $this->get_quality($data);
                    $dataArray['product_desc'] = $this->get_product_desc($data);
                    $dataArray['high_concept'] = $this->get_high_concept($data);
                    $dataArray['follower_count'] = $this->get_follower_count($data);
                    $dataArray['company_url'] = $this->get_company_url($data);
                    $dataArray['created_at'] = $this->get_created_at($data);
                    $dataArray['updated_at'] = $this->get_updated_at($data);
                    $dataArray['twitter_url'] = $this->get_twitter_url($data);
                    $dataArray['blog_url'] = $this->get_blog_url($data);
                    $dataArray['video_url'] = $this->get_video_url($data);
                    $dataArray['crunchbase_url'] = $this->get_crunchbase_url($data);
                    $dataArray['status'] = $this->get_status($data);
                    
                    
                    return $dataArray;
                }
                private function get_name($data){
			if(isset($data->name))
				return $this->pruneValue($data->name);
			else
				return null;
		}
                private function get_angellist_url($data){
			if(isset($data->angellist_url))
				return $this->pruneValue($data->angellist_url);
			else
				return null;
		}
                private function get_logo_url($data){
			if(isset($data->logo_url))
				return $this->pruneValue($data->logo_url);
			else
				return null;
		}
                private function get_thumb_url($data){
			if(isset($data->thumb_url))
				return $this->pruneValue($data->thumb_url);
			else
				return null;
		}
                private function get_launch_date($data){
			if(isset($data->launch_date))
				return $this->pruneValue($data->launch_date);
			else
				return null;
		}
                private function get_quality($data){
			if(isset($data->quality))
				return $this->pruneValue($data->quality);
			else
				return null;
		}
                private function get_product_desc($data){
			if(isset($data->product_desc))
				return $this->pruneValue($data->product_desc);
			else
				return null;
		}
                private function get_high_concept($data){
			if(isset($data->high_concept))
				return $this->pruneValue($data->high_concept);
			else
				return null;
		}
                private function get_follower_count($data){
			if(isset($data->follower_count))
				return $this->pruneValue($data->follower_count);
			else
				return null;
		}
                private function get_company_url($data){
			if(isset($data->company_url))
				return $this->pruneValue($data->company_url);
			else
				return null;
		}
                private function get_created_at($data){
			if(isset($data->created_at))
				return $this->pruneValue($data->created_at);
			else
				return null;
		}
                private function get_updated_at($data){
			if(isset($data->updated_at))
				return $this->pruneValue($data->updated_at);
			else
				return null;
		}
                private function get_twitter_url($data){
			if(isset($data->twitter_url))
				return $this->pruneValue($data->twitter_url);
			else
				return null;
		}
                private function get_blog_url($data){
			if(isset($data->blog_url))
				return $this->pruneValue($data->blog_url);
			else
				return null;
		}
                private function get_video_url($data){
			if(isset($data->video_url))
				return $this->pruneValue($data->video_url);
			else
				return null;
		}
                private function get_crunchbase_url($data){
			if(isset($data->crunchbase_url))
				return $this->pruneValue($data->crunchbase_url);
			else
				return null;
		}
                private function get_status($data){
			if(isset($data->status))
				return $this->pruneValue($data->status);
			else
				return null;
		}
                
                
                
                
                
                /**
                 * Getting market(s) values
                 * 
                 * @param type $data
                 * @return array
                 */
                private function value_markets($data){
                    $dataArray = array();
                    if(isset($data->markets)){
                        foreach($data->markets as $anItem){
                            $temp['id'] = $this->value_markets_id($anItem);
                            $temp['tag_type'] = $this->value_markets_tag_type($anItem);
                            $temp['name'] = $this->value_markets_name($anItem);
                            $temp['display_name'] = $this->value_markets_display_name($anItem);
                            $temp['angellist_url'] = $this->value_markets_angellist_url($anItem);
                            array_push($dataArray,$temp);
                        }
                    }
                    return $dataArray;
                }
                private function value_markets_id($data){
			if(isset($data->id))
				return $this->pruneValue($data->id);
			else
				return null;
		}
                private function value_markets_tag_type($data){
			if(isset($data->tag_type))
				return $this->pruneValue($data->tag_type);
			else
				return null;
		}
                private function value_markets_name($data){
			if(isset($data->name))
				return $this->pruneValue($data->name);
			else
				return null;
		}
                private function value_markets_display_name($data){
			if(isset($data->display_name))
				return $this->pruneValue($data->display_name);
			else
				return null;
		}
                private function value_markets_angellist_url($data){
			if(isset($data->angellist_url))
				return $this->pruneValue($data->angellist_url);
			else
				return null;
		}
                
                
                
                
                
                /**
                 * Getting locations(s) values
                 * 
                 * @param type $data
                 * @return array
                 */
                private function value_locations($data){
                    $dataArray = array();
                    if(isset($data->locations)){
                        foreach($data->locations as $anItem){
                            $temp['id'] = $this->value_locations_id($anItem);
                            $temp['tag_type'] = $this->value_locations_tag_type($anItem);
                            $temp['name'] = $this->value_locations_name($anItem);
                            $temp['display_name'] = $this->value_locations_display_name($anItem);
                            $temp['angellist_url'] = $this->value_locations_angellist_url($anItem);
                            array_push($dataArray,$temp);
                        }
                    }
                    return $dataArray;
                }
                private function value_locations_id($data){
			if(isset($data->id))
				return $this->pruneValue($data->id);
			else
				return null;
		}
                private function value_locations_tag_type($data){
			if(isset($data->tag_type))
				return $this->pruneValue($data->tag_type);
			else
				return null;
		}
                private function value_locations_name($data){
			if(isset($data->name))
				return $this->pruneValue($data->name);
			else
				return null;
		}
                private function value_locations_display_name($data){
			if(isset($data->display_name))
				return $this->pruneValue($data->display_name);
			else
				return null;
		}
                private function value_locations_angellist_url($data){
			if(isset($data->angellist_url))
				return $this->pruneValue($data->angellist_url);
			else
				return null;
		}
                
                
                
                
                
                
                
                
                
                
                /**
                 * Getting company_type values
                 * 
                 * @param type $data
                 * @return array
                 */
                private function value_company_type($data){
                    $dataArray = array();
                    if(isset($data->company_type)){
                        foreach($data->company_type as $anItem){
                            $temp['id'] = $this->value_company_type_id($anItem);
                            $temp['tag_type'] = $this->value_company_type_tag_type($anItem);
                            $temp['name'] = $this->value_company_type_name($anItem);
                            $temp['display_name'] = $this->value_company_type_display_name($anItem);
                            $temp['angellist_url'] = $this->value_company_type_angellist_url($anItem);
                            array_push($dataArray,$temp);
                        }
                    }
                    return $dataArray;
                }
                private function value_company_type_id($data){
			if(isset($data->id))
				return $this->pruneValue($data->id);
			else
				return null;
		}
                private function value_company_type_tag_type($data){
			if(isset($data->tag_type))
				return $this->pruneValue($data->tag_type);
			else
				return null;
		}
                private function value_company_type_name($data){
			if(isset($data->name))
				return $this->pruneValue($data->name);
			else
				return null;
		}
                private function value_company_type_display_name($data){
			if(isset($data->display_name))
				return $this->pruneValue($data->display_name);
			else
				return null;
		}
                private function value_company_type_angellist_url($data){
			if(isset($data->angellist_url))
				return $this->pruneValue($data->angellist_url);
			else
				return null;
		}
                
                
                
                
                
                
               /**
                 * fundraising data
                * 
                * Not sure why this isnt an array
                 * 
                 * @param type $data
                 * @return type
                 */
                private function value_fundraising($data){
                    $dataArray = array();
                    if(isset($data->fundraising)){
                        $dataArray['round_opened_at'] = $this->value_fundraising_round_opened_at($data->fundraising);
                        $dataArray['raising_amount'] = $this->value_fundraising_raising_amount($data->fundraising);
                        $dataArray['pre_money_valuation'] = $this->value_fundraising_pre_money_valuation($data->fundraising);
                        $dataArray['equity_basis'] = $this->value_fundraising_equity_basis($data->fundraising);
                        $dataArray['updated_at'] = $this->value_fundraising_updated_at($data->fundraising);
                        $dataArray['raised_amount'] = $this->value_fundraising_raised_amount($data->fundraising);
                    }
                    return $dataArray;
                }
                private function value_fundraising_round_opened_at($data){
			if(isset($data->round_opened_at))
				return $this->pruneValue($data->round_opened_at);
			else
				return null;
		} 
                private function value_fundraising_raising_amount($data){
			if(isset($data->raising_amount))
				return $this->pruneValue($data->raising_amount);
			else
				return null;
		} 
                private function value_fundraising_pre_money_valuation($data){
			if(isset($data->pre_money_valuation))
				return $this->pruneValue($data->pre_money_valuation);
			else
				return null;
		} 
                private function value_fundraising_equity_basis($data){
			if(isset($data->equity_basis))
				return $this->pruneValue($data->equity_basis);
			else
				return null;
		} 
                private function value_fundraising_updated_at($data){
			if(isset($data->updated_at))
				return $this->pruneValue($data->updated_at);
			else
				return null;
		} 
                private function value_fundraising_raised_amount($data){
			if(isset($data->raised_amount))
				return $this->pruneValue($data->raised_amount);
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
