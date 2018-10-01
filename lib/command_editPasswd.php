<?php 

if(!($security->isAdmin())){
	header("Location: ".$app["baseURL"]."?command=SPLASH");
	
} else {
	print "admin.";
	$subCommand=$_REQUEST["subCmd"];
	
	if ($subCommand=="setAdminPasswd") {
		print " SET";
		$passwd=$_REQUEST["nPasswd"];
		print $passwd."<br/>";
		if(!empty($passwd)) {
			$security->changeAdminPassword($passwd);
			$_SESSION["key"]=$passwd;
			header("Location: ".$app["baseURL"]."?command=HOME");	
		} else {
			$template->display("editPASSWD.html");
		}
	} else {
		$template->display("editPASSWD.html");
	}
}

?>