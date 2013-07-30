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
                                        'company'
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
