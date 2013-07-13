<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | https://github.com/hybridauth/hybridauth
*  (c) 2009-2011 HybridAuth authors | hybridauth.sourceforge.net/licenses.html
*/

/**
 * Hybrid_Providers_AngelList
 * 
 * Requires no authentication.  Just need an access token in the GET URL.
 * Once you have an access token.  It never expires (per the Angel List API doc).
 */
class Hybrid_Providers_AngelList extends Hybrid_Provider_Model
{
	// default permissions  
	public $scope = "";
	
	private $api_base_url = "https://api.angel.co/1/";
	private $access_token = "null";
	 
	/**
	* IDp wrappers initializer 
	*/
	function initialize() 
	{
		//parent::initialize();
		$this->access_token = $this->config['keys']['access_token'];
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
	function get($endpoint=null){

		$data['angellist'] = file_get_contents($this->api_base_url . $endpoint.$this->getURLParamSeparator($endpoint)."access_token=".$this->access_token);

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
