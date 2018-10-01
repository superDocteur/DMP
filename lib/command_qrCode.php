<?php
require $config["path.lib"]."phpqrcode/qrlib.php";
header("content-type:image/png");
switch ($app["subCmd"]){
  case "admin":
    $subURL="?key=".urlencode($config["keys.admin"]);
    break;
  case "access":
    $subURL="?key=".urlencode($config["keys.login"]);
    break;
  default:
    $subURL="";
}
QRcode::png($app["baseUrl"].$subURL,NULL, 'L', 4, 2);
?>