<?php
## setAD
$subCommand=$_REQUEST["subCmd"];

if ($subCommand=="setAD") {
	
	#Define a local function to simplify the update
	$a=function($x) use ($myDMP) {
		if (isset($_REQUEST[$x])) {$myDMP->advanceDirectives->set($x,$_REQUEST[$x]);};
	};

	$a("maintain_InUnrecoverableComa");
	$a("begin_CPR");
	$a("begin_Intubation");
	$a("begin_Dialysis");
	$a("begin_AnySurgery");
	$a("maintain_CPR");
	$a("maintain_Intubation");
	$a("maintain_Dialysis");
	$a("maintain_ParenteralAlimentation");
	$a("allow_ContinuousAntalgicSedation");
	$a("other");

	# We must reassign $AD since it may have just been modified
	$template->assign("AD",$myDMP->advanceDirectives->getFull());
	$template->display("editAD.html");

} else {
	
	$template->display("editAD.html");
}

?>