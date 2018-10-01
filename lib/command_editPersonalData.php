<?php
// Can be accessed only if isAdmin
print "************\n";
print_r ($_REQUEST);
print "************\n";
if ($app["subCmd"]=="save"){
	print "We should save the result";
	$myDMP->patient->setName($_REQUEST["name"]);
	$myDMP->patient->setSurname($_REQUEST["surname"]);
	$myDMP->patient->setBirthName($_REQUEST["birthName"]);
	$myDMP->patient->setBirthDate($_REQUEST["birthDate"]);
	$myDMP->patient->setGender($_REQUEST["gender"]);
	$myDMP->patient->setBiologicalGender($_REQUEST["biologicalGender"]);
	$myDMP->patient->setPersonalPhone($_REQUEST["personalPhone"]);
	$myDMP->patient->setPersonalMail($_REQUEST["personalMail"]);
	$myDMP->patient->setPersonalAddress($_REQUEST["personalAddress"]);
}

$template->assign("Patient",$myDMP->patient->getFull());
$template->display("editPersonalData.html");
?>