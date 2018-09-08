<?php
##### Security class

class Security {
	protected $key;
	
	function __construct($key=NULL){
		$this->key=$key;
	}
	
	function changeKey($key){
		$this->key=$key;
	}
	
	function isAdmin(){
		global $config;
		if($this->key==$config["keys.admin"]) {
			return TRUE;
		} else {
			return FALSE;
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
	
}

?>