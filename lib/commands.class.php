<?php
##### Command listing

class Commands{
	private $_commands;
	const NEED_TEMPLATE=1;
	const IS_ADMIN=2;
	const IS_ALLOWED=4;
	
	function __construct(){
		$this->_commands=[];
	}
	
	function add($name,$file,$options=0) {
		$this->_commands[$name]=array("filename"=>$file,"options"=>$options);
	}
	
	function getNames() {
		return array_keys($this->_commands);
	}
	
	function __get($id) {
		return $this->_commands[$id];
	}
	function get($id){
		return $this->_commands[$id];
	}
	
	function needTemplate($id){
		return ($this->_commands[$id]["options"] & Commands::NEED_TEMPLATE);
	}
	function needAdmin($id){
		return ($this->_commands[$id]["options"] & Commands::IS_ADMIN);
	}
	function needAllowed($id){
		return ($this->_commands[$id]["options"] & Commands::IS_ALLOWED);
	}
	
	function run($id){
		return $this->_commands[$id]["filename"];
	}
}

?>