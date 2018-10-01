<?php

#########################################
# 
#
#
#
#########################################
error_reporting(-1);

require "./dbconfig.php";

# Loading the database and interfacing the abstraction layer
require $config["path.lib"]."db.php";
require $config["path.lib"]."oo.php";

## Getting base configuration
require "./config.php";

## Starting session management
require $config["path.lib"]."sessions.php";

## Init the template engine
require $config["path.lib"]."smarty/Smarty.class.php";

## Initializing available functionalities
require $config["path.lib"]."commands.php";

## Load the security module (Currently working on it)
require $config["path.lib"]."security.php";


## Getting the access key
$key=get_key();

if (is_null($key)) {
	# the access key is not defined
	unset($_SESSION["key"]);
} else {
	# the access key exists (we dont yet know if it's legit)
	# Store it in the session
	$_SESSION["key"]=$key;	
}

$auth=new Authentificator($db,$key);

if ($auth->isAdmin()) {
	// Yeah we are admin so we should  be able to use the $key to decipher the options to create the Deciphyer 
	$tempDeciphyer=new Deciphyer(array("AES128CBC.KEY"=>$key));
//	var_dump($tempDeciphyer);
	$masterKeys=$db->getProperty("masterKey","SEC",$tempDeciphyer);
//	print_r($masterKeys);
	$mainDeciphyer=new Deciphyer(json_decode($masterKeys,true));
//	print_r($mainDeciphyer);//Next line doesn't work 
	$db->addDecifyer($mainDeciphyer);
} else if ($auth->isAllowed()) {
	// Well at least we are Allowed so we should be able to use the $key to decifer the options to create the Decifyer 
	$tempDeciphyer=new Deciphyer(array("AES128CBC.KEY"=>$key));
	var_dump($tempDeciphyer);
	$masterKeys=$db->getProperty("viewingKey","SEC",$tempDeciphyer);
//TODO	

}

## Hook up the security to the database

	
if (isset($_REQUEST["command"]) && $commands->get($_REQUEST["command"])) {
	# getting the command
	$command=$_REQUEST["command"];
} elseif ($commands->get($config["action.unknown"])) {
	# Setting default action
	$command=$config["action.unknown"];
} else {
	die ("FATAL ERROR (".htmlentities($_REQUEST["command"])."=> ".htmlentities($config["action.unknown"])." =>".htmlentities($commands->get($config["action.unknown"])).")");
}

##Initializing the $app array
$app["version"]="0.01a";
$app["url"]=$_SERVER["PHP_SELF"];
$app["fullUrl"]=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$app["baseUrl"]=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
$app["vcommand"]=$_REQUEST["command"];
$app["command"]=$command;
$app["subCmd"]=$_REQUEST["subCmd"];
$app["isAdmin"]=$auth->isAdmin();
$app["isAllowed"]=$auth->isAllowed();
$app["passwd"]=$key; //TO BE REMOVED

#Connect the database to the model
$myDMP=new DMP($db);

#Verify that the called command module can be accessed by the user
if ($commands->needAdmin($command) && !$auth->isAdmin()){
	$command=$config["action.default"];
} elseif ($commands->needAllowed($command) && !$auth->isAllowed()){
	$command=$config["action.default"];
}

if ($commands->needTemplate($command)){
	$template = new Smarty();

	$template->setTemplateDir($config["path.template"]);
	//$smarty->setCompileDir('/web/www.example.com/guestbook/templates_c/');
	//$smarty->setConfigDir('/web/www.example.com/guestbook/configs/');
	//$smarty->setCacheDir('/web/www.example.com/guestbook/cache/');

	$template->assignByRef("myDMP",$myDMP);
	$template->assign('Patient',$myDMP->patient->getFull());
	$template->assign('AD',$myDMP->advanceDirectives->getFull());
	$template->assign('APP',$app);
	$template->assign('CONFIG',$config);
	$template->debugging = TRUE; #($config["DEBUG"]!="");
	
}

include $config["path.lib"].$commands->run($command);

############################################## END (reminder may be for tests only)

?>