<?php
##### config.php #######

$config=new Config($db,$config);

$config->importVolatile(array(
	"url.base"=>"",
	"url.admin"=>"",
	"path.lib"=>"./lib/",
	"path.attachements"=>"./attachements/",
	"path.template"=>"./templates/FR/",
	"db.engine"=>"sqlite",
	"db.sqliteFile"=>"dmp.db",
	"db.sqlite.password"=>"GreZsd",
	
	"DEBUG"=>"FULL_DEBUG",
	
	"action.default"=>"HOME",
	"action.splash"=>"SPLASH",
	"action.unknown"=>"HOME",
	
	"keys.login"=>"ABCDEF",
	"keys.admin"=>"ABCDE"
));

?>