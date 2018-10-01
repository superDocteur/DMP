<?php
// Can be accessed only if isAdmin
print "************\n";
print_r ($_REQUEST);
print "************\n";
if ($app["subCmd"]=="save"){
	print "We should save the result";
	$myDMP->patient->setTrustedPersonName($_REQUEST["trustedPersonName"]);
	$myDMP->patient->setTrustedPersonSurname($_REQUEST["trustedPersonSurname"]);
	$myDMP->patient->setTrustedPersonPhone($_REQUEST["trustedPersonPhone"]);
	$myDMP->patient->setTrustedPersonOther($_REQUEST["trustedPersonOther"]);
	
}

$template->assign("Patient",$myDMP->patient->getFull());
$template->display("editTrustedPersonData.html");
?>