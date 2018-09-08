<?php

#########################################
# 
#
#
#
#########################################
error_reporting(-1);

## Getting base configuration
require "./config.php";

## Starting session management
require $config["path.lib"]."sessions.php";

## Init the template engine
require $config["path.lib"]."smarty/Smarty.class.php";

## Get runtime customization vars
##TODO
//$config["runtime.locale"] = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

## Initializing available fonctionalities
require $config["path.lib"]."commands.php";

require $config["path.lib"]."security.php";

#Initializing
$app["url"]=$_SERVER["PHP_SELF"];
$app["command"]=$_REQUEST["command"];
$app["subCmd"]=$_REQUEST["subCmd"];

## Getting the access key
$key=get_key();
print "@@@ $key @@@";

if (is_null($key)) {
	# the access key is not defined
	unset($_SESSION["key"]);
} else {
	# the access key exists (we dont yet know if it's legit)
	# Store it in the session
	$security=new Security($key);
	$_SESSION["key"]=$key;	
}

if (isset($_REQUEST["command"]) && $commands->get($_REQUEST["command"])) {
	# getting the command
	$command=$_REQUEST["command"];
} else {
	# Setting default action
	$command=$config["action.default"];
}


# Loading the database and interfacing the abstraction layer
require $config["path.lib"]."db.php";
require $config["path.lib"]."oo.php";

#Connect the database to the model
$myDMP=new DMP($db);

#Verify that the called command module can be accessed with by the user
if ($commands->needAdmin($command) && $security->isAdmin()){
	$command=$config["action.default"];
}
if ($commands->needAllowed($command) && $security->isAllowed()){
	$command=$config["action.default"];
}

if ($commands->needTemplate($command)){
	$template = new Smarty();

	$template->setTemplateDir($config["path.template"]);
	//$smarty->setCompileDir('/web/www.example.com/guestbook/templates_c/');
	//$smarty->setConfigDir('/web/www.example.com/guestbook/configs/');
	//$smarty->setCacheDir('/web/www.example.com/guestbook/cache/');

	$template->assign('DMPversion','v0.1a');
	$template->assignByRef("myDMP",$myDMP);
	$template->assign('Patient',$myDMP->patient->getFull());
	$template->assign('AD',$myDMP->advanceDirectives->getFull());
	$template->assign('APP',$app);
	
	$template->debugging = TRUE; #($config["DEBUG"]!="");
	
}

include $config["path.lib"].$commands->run($command);

############################################## END (reminder is for tests only)

?>