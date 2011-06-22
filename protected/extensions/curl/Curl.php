<?php
class Curl extends CApplicationComponent{
	/**
	* Curl config.
	*
	* @var array
	*/
	protected $_config = array(CURLOPT_RETURNTRANSFER => true);
	
	/**
	* Curl handle.
	*
	* @var resource
	*/
	protected $_curl = null;
	
	/**
	* Result string.
	*
	* @var string
	*/
	protected $_result = '';
	
	/**
	* Result headers.
	*
	* @var array
	*/
	protected $_header = array();
	
	/**
	* Curl error number.
	*
	* @var integer
	*/
	protected $_errNo = CURLE_OK;
	protected $_info = '';
	protected $_key = null;
	/**
	* Curl error message.
	*
	* @var string
	*/
	protected $_error = '';
	
	/**
	 * Initialize the extension
	 * check to see if CURL is enabled and the format used is a valid one
	 */
	public function init(){
		if( !function_exists('curl_init') )
		throw new CException( Yii::t('Curl', 'You must have CURL enabled in order to use this extension.') );
	}
	
	/**
	* Constructor.
	*
	* @param null|array With cURL settings
	* @return void
	*/
	public function run($config=array()) {
		
		if(!extension_loaded('curl')) {
			throw new CException(Yii::t('Curl', __METHOD__.'(): cURL extension has to be loaded to use this class.'));
		}
		
		// Add headerfunction, so we can save raw headers
		$this->setOption(CURLOPT_HEADERFUNCTION, array($this, '_setHeader'));
		
		$this->setOption(CURLOPT_TIMEOUT, 20);
		
		// Set user options
		if(is_array($config) && count($config) > 0) {
			foreach($config as $key => $value) {
				$this->setOption($key, $value);
			}
		}
		
		$this->_init();
		
		if(!$this->_createNewHandle()) {
			throw new CException(Yii::t('Curl', __METHOD__.'(): Can not create new cURL handle.'));
		}
	}
	
	/**
	* Destructor.
	*
	* Will close cURL handle.
	*
	* @return void
	*/
	public function __destruct() {
		// Always close handle
		$this->close();
	}
	
	/**
	* Init the response specific data.
	*
	* @return bool
	*/
	protected function _init() {
		$this->_result = '';
		$this->_header = array();
		$this->_errNo = CURLE_OK;
		$this->_error = '';
		return true;
	}
	
	/**
	* Set URL.
	*
	* @param String $url
	* @return Whiz_Http_Client_Curl
	*/
	public function setUrl($url) {
		if(!preg_match('!^\w+://! i', $url)) {
			$url = 'http://'.$url;
		}
		if(empty($url) || !$this->is_url($url)){
			throw new CException( Yii::t('Curl', 'You must set correct Url.') );
		}
				
		$this->_config[CURLOPT_URL] = $url;
		return $this;
	}
	
	function is_url($url){
		$validate=new CUrlValidator();
		if(empty($url)){
			return false;
		}
		if($validate->validateValue($url)===false){
			return false;
		}
	    return true;
	}
	
	/**
	* Set cURL options.
	*
	* @param String $key Curl Option
	* @param String $value
	* @return Whiz_Http_Client_Curl
	*/
	public function setOption($key, $value) {
		$this->_config[$key] = $value;
		return $this;
	}
	
	/**
	* Creates a new cURL handle.
	*
	* @return bool
	*/
	protected function _createNewHandle() {
		
		// Close handle first
		$this->close();
		
		// Create new handle
		$this->_curl = curl_init();
		
		if(!$this->_curl) {
			// We have a error
			$this->close();
			return false;
		}
		
		return true;
	}
	
	/**
	* Returns curl Handle with options set.
	*
	* @return resource
	*/
	public function getHandle() {
	
		// Set options
		if(!curl_setopt_array($this->_curl, $this->_config))
			return null;
		
		return $this->_curl;
	}
	
	/**
	* Returns a copy of curl Handle with options set.
	*
	* @return resource
	*/
	public function copyHandle() {
		$handle = $this->getHandle();
		return ($handle == null) ? null : curl_copy_handle($handle);
	}
	
	/**
	* Callback for settings headers.
	*
	* This method _needs_ to be public for Curl_Multi to access.
	*
	* @param resource $ch Curl handle
	* @param string $header Current Header line
	* @return integer Length of header
	*/
	public function _setHeader($ch, $header) {
		// We get the newline too so using trim().
		$header_trim = trim($header);
		if($header_trim) {
			// Saving no empty header
			$this->_header[] = $header_trim;
		}
		// Need to return read length
		return strlen($header);
	}
	
	/**
	* Executes current handle.
	*
	* @param string | null $url
	* @return bool
	*/
	public function exec($url=null) {
	
		// Init request specific stuff
		$this->_init();
		
		if($url != null) {
			$this->setUrl($url);
		}
		
		if(!($ch = $this->getHandle())) {
			// We have no valid handle
			return false;
		}
		
		// Execute
		$result = curl_exec($ch);
		
		// Save result
		$this->setFromHandle($ch, (string)$result);
		
		return ! $this->isError();
	}
	
	/**
	* Closes cURL handle.
	*
	* @return bool
	*/
	public function close() {
		if(!isset($this->_curl))
			return true;
			
		if(is_resource($this->_curl)) {
			curl_close($this->_curl);
			unset($this->_curl);
		}
		return true;
	}
	
	/**
	* Set info and result from cURL handle.
	*
	* @param resource $ch
	* @param string $result
	* @return Whiz_Http_Client_Curl
	*/
	public function setFromHandle($ch, $result) {
		// Set result
		$this->_result = $result;
		// Fetch info from handle
		if($ch) {
			$this->_info = curl_getinfo($ch);
			$this->_errNo = curl_errno($ch);
			$this->_error = curl_error($ch);
		}
		return $this;
	}
	
	/**
	* Return result.
	*
	* @return string
	*/
	public function getResult() {
		return $this->_result;
	}
	
	/**
	* Return if an error occured.
	*
	* @return bool
	*/
	public function isError() {
		return ($this->_errNo != CURLE_OK) || ($this->_error);
	}
	
	/**
	* Return error number.
	*
	* @return integer
	*/
	public function getErrNo() {
		return $this->_errNo;
	}
	
	/**
	* Return error message.
	*
	* @return string
	*/
	public function getError() {
		return $this->_error;
	}
	
	/**
	* Return reponse info.
	*
	* @return array
	*/
	public function getInfo() {
		return $this->_info;
	}
	
	/**
	* Return reponse heaader.
	*
	* @return array
	*/
	public function getHeader() {
		return $this->_header;
	}
	
	/**
	* Return cURL version info.
	*
	* @return array
	*/
	public function getVersion() {
		return curl_version();
	}

}//end of method