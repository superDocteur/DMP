<?php
##### Security class
class Authentificator {
	protected $db;
	protected $key;
	protected $_isAdmin=FALSE;
	protected $_isAllowed=FALSE;

	function __construct($db,$key=NULL) {
		$this->db=$db;
		$this->key=$key;
	}
	function isAdmin(){
		global $config;
		if($this->_isAdmin!=FALSE) {
			return TRUE;
		} else {
			$hash=$this->db->getProperty("adminPasswd","SEC");
			if(!is_a($hash,"DmpDbNotSet") && ($this->verifyHash($this->key,$hash))){
				$this->_isAdmin=$this->key;
				return TRUE;
			} else {
				if($this->key==$config["keys.admin"]) {
					
					$this->_isAdmin=TRUE;
					return TRUE;
				} else {
					
					return FALSE;
				}
			}
		}
	}

	function isAllowed(){
		global $config;
		if($this->key==$config["keys.login"]) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function getHash($text){
		return password_hash($text,PASSWORD_DEFAULT);
	}
	
	function verifyHash($text,$hash){
		if (password_verify($text,$hash)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function getKey(){
		return $this->key;
	}
}
	
class Deciphyer {
	protected $db;
	protected $key;
	public $defaultCipher;
	public $defaultKey; //should be private (for debugging purpose)
	const allowedCiphers=array(
		"AES-128-CBC","AES-128-CBC-HMAC-SHA1"
	);

	function setDefaultEncryptKey($key) {
		$this->defaultKey=$key;
	}

	function __construct($options) {
		//$this->db=$db;
		$this->key=$options["AES128CBC.KEY"];
		
		$ciphers=$this->listCiphers();
		foreach ($this::allowedCiphers as $cipher){
			if (in_array($cipher,$ciphers)) {
				$this->defaultCipher = $cipher ;
				break;
			}
		}
	}
	
	function listCiphers(){
		$ciphers=openssl_get_cipher_methods();
		// We should filter out outdated cipher methods but... another day since we already only accept the ones in Security::allowedCiphers
		return $ciphers;
	}
	
	function decrypt($blob,$cipher,$key=NULL,$iv=NULL,$base64=TRUE){
		$key=(is_null($key)||!is_array($key)?$this->defaultKey:$key["AES128CBC.KEY"]);
		//print_r($key);
		if (is_null($iv)){
			$x=preg_split("/\|/",$cipher);
			$cipher=$x[0];
			$iv=$x[1];
		}
		$iv=base64_decode($iv);
		$b64encoded=($base64!=TRUE?OPENSSL_RAW_DATA:0);
		$result = openssl_decrypt($blob, $cipher, $key, $b64encoded, $iv);
//		print $result;
		if ($result==FALSE){
			print openssl_error_string()." $cipher $key $iv";
			return NULL;
		} else {
			return $result;
		}
	}
	
	function encrypt($blob,$cipher=NULL,$key=NULL,$iv=NULL,$base64=TRUE){
		
		$key=(is_null($key)?$this->defaultKey:$key);
		
		if($cipher==NULL){$cipher=$this->defaultCipher;}

		$iv =(is_null($iv)?$this->createIv($cipher):$iv);
		$b64encoded=($base64!=TRUE?OPENSSL_RAW_DATA:0);
		$result=openssl_encrypt($blob,$cipher,$key, $b64encoded, $iv);
		if ($result==FALSE){
			return NULL;
		} else {
			
			return array("encrypted"=>$result,"cryptinit"=>$cipher."|".base64_encode($iv));
		};
	}
	
	function createIv($cipher){
		return openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
	}

	function exportOptions(){
		return json_encode(array("AES128CBC.KEY"=>$this->key));
	}
}

// Security is a utility class not to be used
class Security {
	const KEYSIZE=2;
	protected $deciphyer;
	protected $authentificator;
	protected $db;
	
	function __construct($db,$authentificator,$deciphyer){
		$this->db=$db;
		$this->authentificator=$authentificator;
		$this->deciphyer=$deciphyer;
	}

	function init_authentificator() {
		if ($this->authentificator->isAdmin()){
			$tempDecifyer=new Deciphyer($db,array("key"=>$key));
				
				//get the master key(s)
			} else if ($this->authentificator->isAllowed()){
				//get the viewing key
				$tempDecifyer=new Deciphyer($db,array("key"=>$key));
			}
			$this->deciphyer=new Deciphyer($db);
	}
	
	// Be careful, setDefaultEncryptKey() does not reencrypt already stored data, it just changes the encryption/decryption key.
	// If you change it you will not be able to get your previous datas back.
	function setDefaultEncryptKey($key){
		
	}
	
	function getViewingKey(){
		if ($this->isAdmin()){
			$key= $this->db->getProperty("viewingKey","SEC",NULL);
			if (is_a($key,"DmpDbNotSet")){ return NULL;}
			print "O-n : " .$key;
			return $key;
		} else {
			return NULL;
		}
	}

	function _createMasterKey($field="masterKey"){
		if ($this->authentificator->isAdmin()){
			$masterKey='{"AES128CBC.KEY":"1234"}';//openssl_random_pseudo_bytes ($this::KEYSIZE);
			$key= $this->db->setProperty($field,"SEC",$masterKey,$this->deciphyer);
			return $masterKey;
		} else {
			return NULL;
		}
	
	}
	function _setViewingKey($key){
		if ($this->isAdmin()){
			$key= $this->db->setProperty("viewingKey","SEC",NULL,$key);
			print "O-n : " .$key;
			return $key;
		} else {
			return NULL;
		}
	}

	function changeAdminPassword($passwd){
		$hash= $this->getHash($passwd);
		
		print $hash."<br/>";
		if ($this->verifyHash($passwd,$hash)) {
			print "password is ok";
			if ($this->db->setProperty("adminPasswd","SEC",$hash)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	
}

?>