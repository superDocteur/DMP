<?php 

// $myDMP->patient->setName("Fred");
// $myDMP->patient->setSurname("N");
// $myDMP->patient->setBirthName("BN");
// $myDMP->patient->setBirthDate("2018");
// $att=$myDMP->attachments->getByID($id);print "[".$att->content;

// $id=$myDMP->attachments->add("CNI","image/jpeg","CNI",NULL,file_get_contents("./Untitled-2.jpg"));
// $att=$myDMP->attachments->getByID($id);print "...".$att->content;

//$myDMP->attachments->delete($id);
//$att=$myDMP->attachments->getByID($id);print "...".$att->content."]<br/>";
//$atts=$myDMP->attachments->list();

//var_dump($atts);

/*$atcd=$myDMP->advanceDirectives->set("maintain_InUnrecoverableComa",TRUE);
*/
//$db->dumpTable("properties");
/*$myDMP->medicalHistory->delete($atcd);
$db->dumpTable("content");
*/
//$x=$security->_setViewingKey("ABCDEF");
print "<br/>";
//$x=$security->getViewingKey();

print "<br/>VK : ".$x;
print "<br/>";
$x=$tempDecifyer->encrypt("Lorem Ipsum");
print_r ($x);
$x=$tempDecifyer->decrypt($x['encrypted'],$x['cryptinit']);
print $tempDecifyer->exportOptions()."\n===========================";
var_dump($x);
$securityHelper=new Security($db,$auth,$tempDecifyer);
$masterKey=$securityHelper->_createMasterKey();
print "===========================\n";
print "MASTERKEY =".base64_encode($masterKey);
print "===========================\n";
$mKey=$db->getProperty("masterKey","SEC",$tempDecifyer);
print "===========================\n";

print "\nMKEY =".$mKey.":::\n";
print_r(json_decode($mKey,true));

$x=new Deciphyer(json_decode($mKey,true));
print_r($x);
print $x->exportOptions();
//$db->dumpTable("properties");
//print "$key : isadmin = ".$security->isAdmin()." / isAllowed =".$security->isAllowed();
?>