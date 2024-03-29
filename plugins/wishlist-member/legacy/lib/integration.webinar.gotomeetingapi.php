<?php

//$__classname__ = 'WishListMemberWebinarIntegrationGotowebinarApi';

//First we define the applications API Key...
define('GOTO_WEBINAR_API_KEY','d2cb66902668ea5bb4ddc15f052f3b66');
define('GOTO_WEBINAR_API_SECRET','7ecdc56095c1980c');

if (!class_exists('WishListMemberWebinarIntegrationGotowebinarApi')) {
	class WishListMemberWebinarIntegrationGotowebinarApi {

		public function __construct() {
			$this->name = "Gotowebinar API";
			$this->slug = "gotomeetingapi";
		}

		public function init() {
		}

		public function subscribe($data) {

			$obj = new WLM_GTMAPI_OAuth_En();
			$oauth = new WLM_GTMAPI_OAuth($obj);

			if(is_object($obj) && is_object($oauth)) {

				$vars['firstName'] = $data['first_name'];
				$vars['lastName'] = $data['last_name'];
				$vars['email'] = $data['email'];

				// get settings
				global $WishListMemberInstance;
				$webinars = $WishListMemberInstance->GetOption('webinar');
				$settings = $webinars[$this->slug];

				$webinar4 = explode('---',  $settings[$data['level']]);

				if (empty($settings)) {
					return;
				}

				$obj->setOrganizerKey($settings['organizerkey']);
				$obj->setAccessToken($settings['accesstoken']);
				$oauth->setWebinarId($webinar4[0]);
				$oauth->setRegistrantInfo($vars);

				$oauth->createRegistrant();

				if($oauth->hasApiError()){
					// This means that the user wasn't added to the webinar, probably because the Access TOken expired (expires in 60 minutes)
					// Let's refresh the token

					$this->refreshtoken();
					// Let's try again subscribing the user

					$webinars = $WishListMemberInstance->GetOption('webinar');
					$settings = $webinars[$this->slug];

					$webinar4 = explode('---',  $settings[$data['level']]);

					$obj->setOrganizerKey($settings['organizerkey']);
					$obj->setAccessToken($settings['accesstoken']);
					$oauth->setWebinarId($webinar4[0]);
					$oauth->setRegistrantInfo($vars);

					$oauth->createRegistrant();

				}
			}

		}

		// Refreshes the refreshtoken and accessToken
		// Reason we need this is because access token expires in 60 minutes
		// while the refreshtoken expires in 1 month, If we don't do this then
		// the integration will stop working after a month without the refreshtoken getting refresh, complicated right? lol
		public function refreshtoken() {

			global $WishListMemberInstance;
			$webinars = $WishListMemberInstance->GetOption('webinar');

			$settings = $webinars['gotomeetingapi'];
			$refresh_token = $settings['refreshtoken'];

			if (empty($settings)) {
				return;
			}

			// No need to refresh if they're still using the previuos auth code format
			if(strlen($settings['authorizationcode']) < 10) {
				return;
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.getgo.com/oauth/v2/token");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=".$refresh_token);
			curl_setopt($ch, CURLOPT_POST, 1);

			$headers = array();

			$str = GOTO_WEBINAR_API_KEY.':'.GOTO_WEBINAR_API_SECRET;
			$id_plus_secret = base64_encode($str);
		
			$headers[] = "Authorization: Basic ".$id_plus_secret;
			$headers[] = "Accept: application/json";
			$headers[] = "Content-Type: application/x-www-form-urlencoded";
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);
			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close ($ch);

			$isJson = 0;
			$decodedString = json_decode($result);
			if(is_array($decodedString) || is_object($decodedString))
				$isJson = 1;    

			if($isJson)
				$result = json_decode($result);

			if($result) {
				$settings['accesstoken'] = $result->access_token;
				$settings['organizerkey'] = $result->organizer_key;
				$settings['authorizationcode'] = $settings['authorizationcode'];
				$settings['refreshtoken'] = $result->refresh_token;

				$webinar_settings['gotomeetingapi'] = $settings;

				$WishListMemberInstance->SaveOption('webinar', $webinar_settings);
			}

		}

	}
}

/*-------------*/
/* API CLASSES */
/*-------------*/

if (!class_exists('WLM_GTMAPI_OAuth_En')) {
	class WLM_GTMAPI_OAuth_En{

		protected $_accessToken;
		protected $_userId;
		protected $_organizerKey;
		protected $_refreshToken;
		protected $_expiresIn;

		public function getAccessToken(){
			return $this->_accessToken;
		}

		public function setAccessToken($token){
			$this->_accessToken = $token;
		}

		public function getUserId(){
			return $this->_userId;
		}

		public function setUserId($id){
			$this->_userId = $id;
		}   

		public function getOrganizerKey(){
			return $this->_organizerKey;
		}

		public function setOrganizerKey($key){
			$this->_organizerKey = $key;
		}

		public function getRefreshToken(){
			return $this->_refreshToken;
		}

		public function setRefreshToken($token){
			$this->_refreshToken = $token;
		}

		public function getExpiresIn(){
			return $this->_expiresIn;
		}

		public function setExpiresIn($expiresIn){
			$this->_expiresIn = $expiresIn;
		}   
	}
}

if (!class_exists('WLM_GTMAPI_OAuth')) {
	class WLM_GTMAPI_OAuth{

		protected $_redirectUrl;
		protected $_OAuthEnObj;
		protected $_curlHeader = array();
		protected $_apiResponse;
		protected $_apiError;
		protected $_apiErrorCode;
		protected $_apiRequestUrl;
		protected $_apiResponseKey;
		protected $_accessTokenUrl;
		protected $_webinarId;
		protected $_registrantInfo = array();
		protected $_apiRequestType;
		protected $_apiPostData;

		public function __construct(WLM_GTMAPI_OAuth_En $oAuthEn){
			$this->_OAuthEnObj = $oAuthEn;  
		}

		public function getOAuthEntityClone(){
			return clone $this->_OAuthEnObj;    
		}

		public function getWebinarId(){
			return $this->_webinarId;
		}

		public function setWebinarId($id){
			$id = (int)$id;
			$this->_webinarId = empty($id) ? 0 : $id;
		}

		public function setApiErrorCode($code){
			$this->_apiErrorCode = $code;   
		}

		public function getApiErrorCode(){
			return $this->_apiErrorCode;    
		}   

		public function getApiAuthorizationUrl(){
			return 'https://api.getgo.com/oauth/v2/authorize?client_id='.GOTO_WEBINAR_API_KEY.'&response_type=code&redirect_uri='.$this->getRedirectUrl(); 
		}

		public function getApiKey(){
			return  GOTO_WEBINAR_API_KEY;
		}

		public function getApiRequestUrl(){
			return  $this->_apiRequestUrl;
		}

		public function setApiRequestUrl($url){
			$this->_apiRequestUrl = $url;
		}

		public function setRedirectUrl($url){
			$this->_redirectUrl = urlencode($url);  
		}

		public function getRedirectUrl(){
			return $this->_redirectUrl; 
		}

		public function setCurlHeader($header){
			$this->_curlHeader = $header;   
		}

		public function getCurlHeader(){
			return $this->_curlHeader;  
		} 

		public function setApiResponseKey($key){
			$this->_apiResponseKey = $key;
		}

		public function getApiResponseKey(){
			return $this->_apiResponseKey;
		}

		public function setRegistrantInfo($arrInfo){
			$this->_registrantInfo = $arrInfo;  
		}

		public function getRegistrantInfo(){
			return $this->_registrantInfo;  
		}

		public function authorizeUsingResponseKey($responseKey){
			$this->setApiResponseKey($responseKey);
			$this->setApiTokenUsingResponseKey();
		}

		protected function setAccessTokenUrl(){
			$url = 'https://api.getgo.com/oauth/access_token?grant_type=authorization_code&code={responseKey}&client_id={api_key}';
			$url = str_replace('{api_key}', $this->getApiKey(), $url);
			$url = str_replace('{responseKey}', $this->getApiResponseKey(), $url);
			$this->_accessTokenUrl = $url;
		}

		protected function getAccessTokenUrl(){
			return $this->_accessTokenUrl;  
		}

		protected function resetApiError(){
			$this->_apiError = '';  
		}

		public function setApiTokenUsingResponseKey(){
			//set the access token url
			$this->setAccessTokenUrl();

			//set the url where api should go for request
			$this->setApiRequestUrl($this->getAccessTokenUrl());

			//make request
			$this->makeApiRequest();

			if($this->hasApiError()){
				//echo $this->getApiError();
			}else{
				//if api does not have any error set the token
				//echo $this->getResponseData();
				$responseData = json_decode($this->getResponseData());
				$this->_OAuthEnObj->setAccessToken($responseData->access_token);
				$this->_OAuthEnObj->setOrganizerKey($responseData->organizer_key);
				$this->_OAuthEnObj->setRefreshToken($responseData->refresh_token);
				$this->_OAuthEnObj->setExpiresIn($responseData->expires_in);
			}
		}

		function hasApiError(){
			return $this->getApiError() ? 1 : 0;
		}

		function getApiError(){
			return $this->_apiError;
		}

		function setApiError($errors){
			return $this->_apiError = $errors;
		}

		function getApiRequestType(){
			return $this->_apiRequestType;
		}

		function setApiRequestType($type){
			return $this->_apiRequestType = $type;
		}   

		function getResponseData(){
			return $this->_apiResponse;
		}

		function setApiPostData($data){
			return $this->_apiPostData = $data;
		}   

		function getApiPostData(){
			return $this->_apiPostData;
		}   

		function makeApiRequest(){
			$header = array();

			$this->getApiRequestUrl();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt($ch, CURLOPT_URL, $this->getApiRequestUrl());
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			if($this->getApiRequestType()=='POST'){
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getApiPostData());  
			}

			if($this->getCurlHeader()){
				$headers = $this->getCurlHeader();
			}else{
				$headers = array(
						"HTTP/1.1",
						"Content-type: application/json",
						"Accept: application/json",
						"Authorization: OAuth oauth_token=".$this->_OAuthEnObj->getAccessToken()
					);  
			}

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 

			$data = curl_exec($ch);
			$validResponseCodes = array(200,201,409);
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 

			$this->resetApiError();

			if (curl_errno($ch)) {
				$this->setApiError(array(curl_error($ch)));
			} elseif(!in_array($responseCode, $validResponseCodes)){
				if($this->isJsonString($data)){
					$data = json_decode($data);
				}

				$this->setApiError($data);
				$this->setApiErrorCode($responseCode);
			}else {
				$this->_apiResponse = $data;
				$_SESSION['gotoApiResponse'] = $this->getResponseData();
				curl_close($ch);
			}
		}

		

		function getWebinars(){
			$url = 'https://api.getgo.com/G2W/rest/organizers/'.$this->_OAuthEnObj->getOrganizerKey().'/webinars';
			$this->setApiRequestUrl($url);
			$this->setApiRequestType('GET');
			$this->makeApiRequest();

			if($this->hasApiError()){
				return null;    
			}
			$webinars = json_decode($this->getResponseData());

			return $webinars;
		}

		function createRegistrant(){
			if(!$this->getWebinarId()){
				$this->setApiError(array('Webinar id not provided'));               
				return null;
			}

			if(!$this->getRegistrantInfo()){
				$this->setApiError(array('Registrant info not provided'));              
				return null;
			}

			$this->setApiRequestType('POST');   
			$this->setApiPostData(json_encode($this->getRegistrantInfo())); 
			$url = 'https://api.getgo.com/G2W/rest/organizers/'.$this->_OAuthEnObj->getOrganizerKey().'/webinars/'.$this->getWebinarId().'/registrants';

			$this->setApiRequestUrl($url);
			$this->makeApiRequest();

			if($this->hasApiError()){
				return null;    
			}

			$webinar = json_decode($this->getResponseData());

			return $webinar;
		}

		

		function isJsonString($string){
			$isJson = 0;
			$decodedString = json_decode($string);
			if(is_array($decodedString) || is_object($decodedString))
				$isJson = 1;    

			return $isJson;
		}

		function getAccessTokenv2($auth_code) {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.getgo.com/oauth/v2/token");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=".$auth_code."&redirect_uri=http%3A%2F%2Fwlptest.com");
			curl_setopt($ch, CURLOPT_POST, 1);

			$headers = array();

			$str = GOTO_WEBINAR_API_KEY.':'.GOTO_WEBINAR_API_SECRET;
			$id_plus_secret = base64_encode($str);
		
			$headers[] = "Authorization: Basic ".$id_plus_secret;
			$headers[] = "Accept: application/json";
			$headers[] = "Content-Type: application/x-www-form-urlencoded";
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);
			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close ($ch);

			if($this->isJsonString($result)){
				$result = json_decode($result);
			}

			return $result;
		}
	}
}