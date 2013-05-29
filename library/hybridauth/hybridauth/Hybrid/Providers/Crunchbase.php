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

	function get($endpoint=null){

		$data['crunchbase'] = file_get_contents($this->api_base_url . $endpoint."?api_key=".$this->api_key);

		return $data;
	}
}
