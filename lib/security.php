<?php

require $config["path.lib"].'security.class.php';

function get_key(){
	if (isset($_REQUEST["key"])) {
		 return $_REQUEST["key"];
	} else {
		if (isset($_SESSION["key"])){
			return $_SESSION["key"];
		} else {
			return NULL;
		}
	}
}
?>