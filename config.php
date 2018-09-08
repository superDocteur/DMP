<?php
##### config.php #######

$config=array(
	"url.base"=>"",
	"url.admin"=>"",
	"path.lib"=>"./lib/",
	"path.attachements"=>"./attachements/",
	"path.template"=>"./templates/FR/",
	"db.engine"=>"sqlite",
	"db.sqliteFile"=>"dmp.db",
	"db.sqlite.password"=>"GreZsd",
	
	"DEBUG"=>"FULL_DEBUG",
	
	"action.default"=>"LOGIN",
	"action.splash"=>"SPLASH",
	
	"keys.login"=>"ABCDEF",
	"keys.admin"=>"ABCDE"
);

//if ($config["DEBUG"]=="FULL_DEBUG") print "CONFIG LOADED\n" ;

?>