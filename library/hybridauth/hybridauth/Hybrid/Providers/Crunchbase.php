<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | https://github.com/hybridauth/hybridauth
*  (c) 2009-2011 HybridAuth authors | hybridauth.sourceforge.net/licenses.html
*/

/**
 * Hybrid_Providers_Crunchbase
 * 
 * Requires no authentication.  Just need an api key in the GET URL.
 */
class Hybrid_Providers_Crunchbase extends Hybrid_Provider_Model
{
	// default permissions  
	public $scope = "";
	
	private $api_base_url = "http://api.crunchbase.com/v/1/";
	private $api_key = "null";
	 
	/**
	* IDp wrappers initializer 
	*/
	function initialize() 
	{
		//parent::initialize();
		$this->api_key = $this->config['keys']['api_key'];
	}
	function loginBegin(){}
	function loginFinish(){}
	function logout(){}
	
	/**
	 * Always returning true, there is no login for this API.
	 * 
	 * @return bool
	 */
	public function isUserConnected(){
		return true;
	}
	public function get($endpoint=null){

            $data['crunchbase'] = file_get_contents($this->api_base_url . $endpoint.$this->getURLParamSeparator($endpoint)."api_key=".$this->api_key);

            return $data;
	}
        /**
         * Returns an & or ? depending on what is already in the URL.
         * 
         * @param string $url
         * @return string
         */
        private function getURLParamSeparator($url){
            if(preg_match('/\?/', $url))
                return '&';
            else
                return '?';
        }
        
}
